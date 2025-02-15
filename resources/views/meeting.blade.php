<x-main-layout>
    <show-meeting
        :meeting="{{ json_encode(new \App\Http\Resources\MeetingResource($meeting)) }}"
    ></show-meeting>
</x-main-layout>