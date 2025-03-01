<?php

namespace App\Http\Requests;

use App\Models\MeetingAttendee;
use App\Rules\SignsRequest;
use Illuminate\Foundation\Http\FormRequest;

class MeetingAttendeeDestroyRequest extends FormRequest
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
            'destroy_challenge' => ['required', 'in:'.$this->attendee->destroy_challenge],
        ];

        return array_merge($rules, [
            'signature' => ['required', new SignsRequest(array_keys($rules), $this->attendee->meeting->public_key)],
        ]);
    }

    public function delete(MeetingAttendee $attendee)
    {
        $attendee->delete();
    }
}
