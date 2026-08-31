<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(string $locale)
    {
        $availableLocales = config('app.available_locales', ['en', 'ar']);
    
        if (in_array($locale, $availableLocales)) {
            session(['locale' => $locale]);
        }
    
        return redirect()->back();
    }
}
