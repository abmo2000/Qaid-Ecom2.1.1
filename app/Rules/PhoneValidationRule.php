<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use App\Services\PhoneNumberService;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalize value: remove spaces, parentheses, dashes and non-digit/plus characters
        $normalized = preg_replace('/[^+0-9]/', '', (string) $value);

        $phoneServiceObj = new PhoneNumberService($normalized, 'EG');
        if (! $phoneServiceObj->isValid()) {
            $fail('the :attribute is invalid value');
        }
    }
}
