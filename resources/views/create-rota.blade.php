<x-main-layout
    title="Rota"
    description="Create a rota."
    feature="ROTA"
>
    <create-rota
        submit-route="{{ route('rota.store') }}"
        captcha-challenge-route="{{ route('captcha.index') }}"
    ></create-rota>
</x-main-layout>