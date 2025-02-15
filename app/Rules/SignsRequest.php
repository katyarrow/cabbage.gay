<?php

namespace App\Rules;

use App\Http\Services\CryptService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SignsRequest implements ValidationRule
{
    public function __construct(private array $fields, private string $publicKey) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = [];
        foreach ($this->fields as $field) {
            $data[] = request()->get($field);
        }
        if (! (new CryptService)->verifySignatureOfRequest($data, $value, $this->publicKey)) {
            $fail('Invalid request signature.');
        }
    }
}
