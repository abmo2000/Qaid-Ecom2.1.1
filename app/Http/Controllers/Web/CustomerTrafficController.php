<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerTraffic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerTrafficController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! $user->isCustomer()) {
            abort(403);
        }

        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
            'path' => ['required', 'string', 'max:1024'],
            'referer' => ['nullable', 'string', 'max:2048'],
            'user_agent' => ['nullable', 'string', 'max:2048'],
        ]);

        $user->traffic()->create([
            'url' => $validated['url'],
            'path' => $validated['path'],
            'referer' => $validated['referer'] ?? null,
            'user_agent' => $validated['user_agent'] ?? null,
            'ip' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }
}
