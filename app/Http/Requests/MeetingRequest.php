<?php

namespace App\Http\Requests;

use App\Http\Services\CryptService;
use App\Models\Meeting;
use App\Rules\EncryptedMax;
use App\Rules\EncryptedSize;
use App\Rules\Hexadecimal;
use App\Rules\SignsRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class MeetingRequest extends FormRequest
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
            'data' => ['required', new Hexadecimal, new EncryptedMax(CryptService::MAX_MEETING_LENGTH)],
            'destroy_at' => ['required', 'date', 'after_or_equal:tomorrow'],
        ];

        // We probably don't want to sign the private and public keys.
        return array_merge($rules, [
            'private_key' => ['required', new Hexadecimal, new EncryptedSize(CryptService::ENCRYPTED_PRIVATE_KEY_LENGTH)],
            'public_key' => ['required', new Hexadecimal, new EncryptedSize(CryptService::UNENCRYPTED_PUBLIC_KEY_LENGTH)],
            'signature' => ['required', new SignsRequest(array_keys($rules), $this->public_key)],
        ]);
    }

    public function save(Meeting $meeting)
    {
        do {
            $identifier = str()->random(10);
        } while (Meeting::identifier($identifier)->exists());
        $meeting->identifier = $identifier;
        $meeting->private_key = $this->private_key;
        $meeting->public_key = $this->public_key;
        $meeting->data = $this->data;
        $givenDestroyDate = Carbon::parse($this->destroy_at);
        $meeting->destroy_at = $givenDestroyDate < now()->addMonths(6) ? $givenDestroyDate : now()->addMonths(6);
        $meeting->save();
    }
}
