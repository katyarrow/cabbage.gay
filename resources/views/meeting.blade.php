<x-main-layout :noindex="true" title="Encrypted Meeting">
    <show-meeting
        :meeting="{{ json_encode(new \App\Http\Resources\MeetingResource($meeting)) }}"
        site-title="{{ config('app.name') }}"
    ></show-meeting>
</x-main-layout>