<?php

namespace App\Http\Requests;

use App\Http\Services\CryptService;
use App\Models\Meeting;
use App\Models\Rota;
use App\Rules\EncryptedMax;
use App\Rules\EncryptedSize;
use App\Rules\Hexadecimal;
use App\Rules\SignsRequest;
use App\Rules\VerifiesCaptchaToken;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RotaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'data' => ['required', new Hexadecimal, new EncryptedMax(CryptService::MAX_ROTA_LENGTH)],
        ];

        // We probably don't want to sign the private and public keys.
        return array_merge($rules, [
            'signature' => ['required', new SignsRequest(array_keys($rules), $this->rota->public_key)],
        ]);
    }

    public function save(Rota $rota)
    {
        $rota->data = $this->data;
        $rota->save();
    }
}
