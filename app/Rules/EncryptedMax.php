<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EncryptedMax implements ValidationRule
{
    public function __construct(private int $max, private int $plaintextMax = 0) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) > $this->max) {
            if ($this->plaintextMax > 0) {
                $fail('The :attribute cannot be longer than '.$this->plaintextMax.'.');
            } else {
                $fail('The :attribute is too long.');
            }
        }
    }
}
