<x-main-layout
    :noindex="true"
    title="Encrypted Meeting Poll"
    description="Show your availability on this meeting poll."
>
    <show-meeting
        :meeting="{{ json_encode(new \App\Http\Resources\MeetingResource($meeting)) }}"
        site-title="{{ config('app.name') }}"
    ></show-meeting>
</x-main-layout>