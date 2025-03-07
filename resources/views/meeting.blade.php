<x-main-layout :noindex="true" title="LOADING">
    <show-meeting
        :meeting="{{ json_encode(new \App\Http\Resources\MeetingResource($meeting)) }}"
    ></show-meeting>
</x-main-layout>