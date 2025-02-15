<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingAttendeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identifier' => $this->identifier,
            'data' => $this->data,
            'destroy_challenge' => $this->destroy_challenge,
            'destroy_route' => route('meeting.attendee.destroy', $this),
        ];
    }
}
