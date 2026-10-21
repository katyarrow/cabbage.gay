<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EncryptedSize implements ValidationRule
{
    public function __construct(private int $size) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < $this->size) {
            $fail('The :attribute is the wrong length.');
        }
    }
}
