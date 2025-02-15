<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
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
            'private_key' => $this->private_key,
            'data' => $this->data,
            'destroy_at' => $this->destroy_at,
            'attendees' => MeetingAttendeeResource::collection($this->meetingAttendees),
            'show_route' => route('meeting.show', $this),
            'attendee_store_route' => route('meeting.attendee.store', $this),
        ];
    }
}
