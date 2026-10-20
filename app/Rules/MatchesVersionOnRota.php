<?php

namespace App\Rules;

use App\Models\Rota;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class MatchesVersionOnRota implements ValidationRule
{

    public function __construct(public Rota $rota) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->rota->version !== $value) {
            $fail('Changes have been made since you last edited the rota, please refresh the page.');
        }
    }
}
