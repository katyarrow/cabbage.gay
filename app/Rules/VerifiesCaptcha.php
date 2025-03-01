<?php

namespace App\Rules;

use App\Models\PowCaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VerifiesCaptcha implements ValidationRule
{
    public function __construct(public $deleteCaptcha = true) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $invalidMsg = 'Invalid Captcha';
        if (! is_array($value)) {
            $fail($invalidMsg);

            return;
        }
        if (! array_key_exists('id', $value) || ! array_key_exists('answers', $value)) {
            $fail($invalidMsg);

            return;
        }
        if (! is_array($value['answers'])) {
            $fail($invalidMsg);

            return;
        }
        $captcha = PowCaptcha::find($value['id']);
        if (! $captcha) {
            $fail($invalidMsg);

            return;
        }
        $answers = json_decode($captcha->answers_json);
        if ($this->deleteCaptcha) {
            $captcha->delete();
        }
        if ($value['answers'] != $answers) {
            $fail($invalidMsg);

            return;
        }
    }
}
