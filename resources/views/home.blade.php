<x-main-layout title="Home">
    <create-meeting
        submit-route="{{ route('meeting.store') }}"
        captcha-challenge-route="{{ route('captcha.index') }}"
    ></create-meeting>
</x-main-layout>