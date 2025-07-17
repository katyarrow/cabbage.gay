<x-main-layout
    title="Releases"
    description="View the incremental releases of {{ config('app.name') }}"
>
    <h1 class="text-3xl font-semibold tracking-wider text-center mb-5">Releases</h1>

    <h2 class="text-xl font-semibold tracking-wider text-left mt-10">v1.1.0</h2>
    <p>17/07/2025 Fixing bugs, adding minor features, etc.</p>
    <ul class="list-disc ml-5">
        <li class="my-3">Updated backend and frontend for security.</li>
        <li class="my-3">Added admin error notifications.</li>
        <li class="my-3">Customised the error pages.</li>
        <li class="my-3">Created an automated end-end test suite using laravel dusk.</li>
        <li class="my-3">Clear selected day when switching to adding availability (this was causing border issues)</li>
        <li class="my-3">Fixed data being cleared when submission fails due to network issues.</li>
        <li class="my-3">Automatically refresh CSRF tokens on submission if needed.</li>
        <li class="my-3">Added disco animation.</li>
        <li class="my-3">Allow swiping on optional period meeting when not in add availability mode.</li>
        <li class="my-3">Added releases page.</li>
        <li class="my-3">Fixed issue when increasing size of viewport on last page of a meeting.</li>
        <li class="my-3">Made various code improvements.</li>
    </ul>

    <h2 class="text-xl font-semibold tracking-wider text-left mt-10">v1.0.0</h2>
    <p>16/03/2025 The initial release of the system.</p>
    <ul class="list-disc ml-5">
        <li class="my-3">Creating encrypted optional period and entire period meetings.</li>
        <li class="my-3">Added ability of adding availability to a poll.</li>
        <li class="my-3">Added ability to remove availability from a poll</li>
        <li class="my-3">Added destruction dates.</li>
        <li class="my-3">Added FAQ page.</li>
    </ul>
</x-main-layout>