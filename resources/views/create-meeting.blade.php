<x-main-layout
    title="Meeting"
    description="Create a meeting"
    feature="POLL"
>
    <create-meeting
        submit-route="{{ route('meeting.store') }}"
        captcha-challenge-route="{{ route('captcha.index') }}"
    ></create-meeting>
</x-main-layout>