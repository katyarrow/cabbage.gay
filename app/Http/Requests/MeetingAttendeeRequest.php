<?php

namespace App\Http\Requests;

use App\Http\Services\CryptService;
use App\Models\MeetingAttendee;
use App\Rules\EncryptedMax;
use App\Rules\Hexadecimal;
use App\Rules\SignsRequest;
use Illuminate\Foundation\Http\FormRequest;

class MeetingAttendeeRequest extends FormRequest
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
            'data' => ['required', new Hexadecimal, new EncryptedMax(CryptService::MAX_MEETING_ATTENDEE_LENGTH)],
        ];

        return array_merge($rules, [
            'signature' => ['required', new SignsRequest(array_keys($rules), $this->meeting->public_key)],
        ]);
    }

    public function save(MeetingAttendee $attendee)
    {
        do {
            $identifier = str()->random(16);
        } while (MeetingAttendee::identifier($identifier)->exists());
        $attendee->identifier = $identifier;
        $attendee->data = $this->data;
        $attendee->destroy_challenge = str()->random(64);
        $attendee->save();
    }
}
