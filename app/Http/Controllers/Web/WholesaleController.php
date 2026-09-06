<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BuisnessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WholesaleController extends Controller
{
    public function index(): View
    {
        return view('web.pages.wholesale-sales');
    }

    public function download(Request $request)
    {
        $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]{7,30}$/'],
            'message' => ['nullable', 'string', 'max:2000'],
            'terms' => ['accepted'],
        ]);

        $setting = BuisnessSetting::query()->where('key', 'wholesale-price-quote')->first();
        $disk = Storage::disk('public');

        if (! $setting) {
            Log::warning('Wholesale price quote setting is missing.', [
                'key' => 'wholesale-price-quote',
            ]);

            return back()->withErrors(['quote' => trans('wholesale.quote_unavailable')])->withInput();
        }

        if (! $setting->file_path) {
            Log::warning('Wholesale price quote path is empty.', [
                'setting_id' => $setting->id,
            ]);

            return back()->withErrors(['quote' => trans('wholesale.quote_unavailable')])->withInput();
        }

        $absolutePath = $disk->path($setting->file_path);

        if (! $disk->exists($setting->file_path)) {
            Log::warning('Wholesale price quote file is missing.', [
                'setting_id' => $setting->id,
                'file_path' => $setting->file_path,
                'absolute_path' => $absolutePath,
            ]);

            return back()->withErrors(['quote' => trans('wholesale.quote_unavailable')])->withInput();
        }

        if (! is_readable($absolutePath)) {
            Log::error('Wholesale price quote file is not readable.', [
                'setting_id' => $setting->id,
                'file_path' => $setting->file_path,
                'absolute_path' => $absolutePath,
            ]);

            return back()->withErrors(['quote' => trans('wholesale.quote_unavailable')])->withInput();
        }

        Log::info('Wholesale price quote download started.', [
            'setting_id' => $setting->id,
            'file_path' => $setting->file_path,
        ]);

        return $disk->download($setting->file_path, 'price-quote.pdf');
    }
}