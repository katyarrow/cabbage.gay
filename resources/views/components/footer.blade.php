<footer class="text-center mb-3 text-sm">
    <div class="border-t border-gray-200 max-w-screen mx-auto py-2"></div>
    End to end encrypted meeting scheduler.<br/>
    We do not store data on you beyond what is required for the sites functioning.<br/>
    Data we store includes:
    <ul class="list-disc">
        <li class="list-inside">Meeting data: This is encrypted wherever possible on your machine and stored on the server.</li>
        <li class="list-inside">Encryption keys: used to verify data (encrypted where appropriate).</li>
        <li class="list-inside">Session cookies: used for cross-site request forgery prevention.</li>
    </ul>
    For more information see our <a href="{{ route('faq') }}" class="underline text-green-600">FAQ Page</a>. <br>
    View changes on the <a href="{{ route('releases') }}" class="underline text-green-600">Releases Page</a>.
    @if (config('donate.active'))
        <br>
        Enjoying {{ config('app.name') }}? Consider <a href="{{ route('donate') }}" class="underline text-green-600">Donating</a>.
    @endif
</footer>