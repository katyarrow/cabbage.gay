<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function show(Meeting $meeting): View
    {
        return view('meeting', [
            'meeting' => $meeting,
        ]);
    }

    public function store(MeetingRequest $request): MeetingResource
    {
        $meeting = new Meeting;
        $request->save($meeting);

        return new MeetingResource($meeting);
    }
}
