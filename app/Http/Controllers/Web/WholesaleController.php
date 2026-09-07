<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BuisnessSetting;
use App\Models\WholesaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class WholesaleController extends Controller
{
    public function index(): View
    {
        return view('web.pages.wholesale-sales');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:14',
                'regex:/^(?:01[0125][0-9]{8}|(?:\+20|0020)1[0125][0-9]{8})$/',
            ],
            'message' => ['nullable', 'string', 'max:2000'],
            'terms' => ['accepted'],
        ], [
            'phone.regex' => trans('wholesale.phone_invalid'),
        ]);

        $quote = $this->quoteFile();

        if (! $quote) {
            return back()->withErrors(['quote' => trans('wholesale.quote_unavailable')])->withInput();
        }

        WholesaleRequest::create([
            'business_name' => $validated['business_name'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'os' => $this->operatingSystem($request->userAgent()),
            'locale' => app()->getLocale(),
        ]);

        return redirect()->route('wholesale-sales')->with([
            'success' => trans('wholesale.submitted'),
            'wholesale_download_url' => route('wholesale-sales.quote-download'),
        ]);
    }

    public function downloadQuote()
    {
        $quote = $this->quoteFile();

        if (! $quote) {
            return response()->json([
                'message' => trans('wholesale.quote_unavailable'),
            ], 404);
        }

        return Storage::disk('public')->download($quote['path'], 'price-quote.pdf');
    }

    private function quoteFile(): ?array
    {
        $setting = BuisnessSetting::query()->where('key', 'wholesale-price-quote')->first();
        $disk = Storage::disk('public');

        if (! $setting || ! $setting->file_path) {
            Log::warning('Wholesale price quote setting or path is missing.');
            return null;
        }

        $absolutePath = $disk->path($setting->file_path);

        if (! $disk->exists($setting->file_path) || ! is_readable($absolutePath)) {
            Log::warning('Wholesale price quote file is missing or unreadable.', [
                'setting_id' => $setting->id,
                'file_path' => $setting->file_path,
            ]);
            return null;
        }

        return ['path' => $setting->file_path];
    }

    private function operatingSystem(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        $agent = new Agent;
        $agent->setUserAgent($userAgent);

        $platform = $agent->platform();
        $version = $agent->version($platform);

        if ($agent->device() === 'iPhone' || $agent->device() === 'iPad') {
            return 'iOS' . ($version ? ' ' . $version : '');
        }

        return match (strtolower((string) $platform)) {
            'windows' => 'Windows' . ($version ? ' ' . $version : ''),
            'mac os', 'os x' => 'macOS' . ($version ? ' ' . $version : ''),
            'android', 'androidos' => 'Android' . ($version ? ' ' . $version : ''),
            'ios' => 'iOS' . ($version ? ' ' . $version : ''),
            default => $platform ?: 'Unknown',
        };
    }
}