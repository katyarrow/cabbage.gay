<?php

namespace App\Http\Requests;

use App\Http\Services\CryptService;
use App\Models\Rota;
use App\Rules\EncryptedMax;
use App\Rules\Hexadecimal;
use App\Rules\MatchesVersionOnRota;
use App\Rules\SignsRequest;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'data' => ['required', new Hexadecimal, new EncryptedMax(CryptService::MAX_ROTA_LENGTH)],
            'version' => ['required', new MatchesVersionOnRota($this->rota)],
        ];

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
