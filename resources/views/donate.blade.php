<x-main-layout
    title="Donate"
    description="Enjoying {{ config('app.name') }}? Consider donating to help with continued development and hosting."
>
    <div class="text-center mx-auto">
        <h1 class="text-3xl font-semibold tracking-wider mb-5">Make a Donation</h1>

        <p class="text-lg max-w-2xl mx-auto">
            Hosting, maintaining, and adding features to {{ config('app.name') }} takes effort and money.
            If you are enjoying the site and are able to contribute then any amount is appreciated. If you are
            not able to contribute (or just don't want to) then don't worry, we aim to keep this site free for
            everyone to use with no strings attached.
        </p>
        @if (config('donate.monero_address'))
            <h2 class="text-xl font-semibold tracking-wider mt-10">Monero</h2>
            <p class="break-all">Address: {{ config('donate.monero_address') }}</p>
        @endif
        @if (config('donate.email_address'))
            <h2 class="text-xl font-semibold tracking-wider mt-10">Other Ways</h2>
            <p>
                Please contact us at:
                <a
                    class="underline text-green-600"
                    href="mailto:{{ config('donate.email_address') }}"
                >
                    {{ config('donate.email_address') }}
                </a>
            </p>
        @endif
    </div>
</x-main-layout>