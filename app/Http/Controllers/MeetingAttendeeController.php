<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingAttendeeDestroyRequest;
use App\Http\Requests\MeetingAttendeeRequest;
use App\Http\Resources\MeetingAttendeeResource;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MeetingAttendeeController extends Controller
{
    public function store(Meeting $meeting, MeetingAttendeeRequest $request): AnonymousResourceCollection
    {
        $attendee = new MeetingAttendee;
        $attendee->meeting_id = $meeting->id;
        $request->save($attendee);

        return MeetingAttendeeResource::collection($meeting->meetingAttendees);
    }

    public function destroy(MeetingAttendee $attendee, MeetingAttendeeDestroyRequest $request)
    {
        $meeting = $attendee->meeting;
        $request->delete($attendee);

        return MeetingAttendeeResource::collection($meeting->meetingAttendees()->get());
    }
}
