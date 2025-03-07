<?php

namespace App\Rules;

use App\Models\PowCaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VerifiesCaptchaToken implements ValidationRule
{
    public function __construct(public $deleteCaptcha = true) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $captcha = PowCaptcha::whereNotNull('solved_token')->where('solved_token', $value)->first();
        if (!$captcha) {
            $fail('Invalid Captcha');

            return;
        }
        $captcha->delete();
    }
}
