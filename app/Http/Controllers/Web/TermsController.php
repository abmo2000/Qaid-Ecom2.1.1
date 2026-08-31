<?php

namespace App\Http\Controllers\Web;

use App\Models\BuisnessSetting;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class TermsController extends Controller
{
    public function __invoke(): View
    {
        $termsSetting = BuisnessSetting::query()
            ->where('key', 'terms')
            ->first();

        $translation = $termsSetting?->translate(app()->getLocale()) ?? $termsSetting?->translate('en');
        $termsText = null;

        if ($translation && $translation->value) {
            $decodedValue = json_decode($translation->value, true);
            $termsText = $decodedValue['terms'] ?? null;
        }

        return view('web.pages.terms', [
            'termsText' => $termsText,
        ]);
    }
}
