<x-main-layout
    title="Home"
    description="An end to end encrypted meeting poll app."
>
    <create-meeting
        submit-route="{{ route('meeting.store') }}"
        captcha-challenge-route="{{ route('captcha.index') }}"
    ></create-meeting>
</x-main-layout>