<header class="relative">
    <ul>
        @foreach (\App\Models\SiteWideMessage::currentlyViewable()->orderBy('level')->get() as $message)
            <li class="
                {{ ['danger' => 'bg-red-400', 'warning' => 'bg-yellow-400', 'info' => 'bg-blue-400'][$message->level] }}
                p-2 text-gray-800 font-semibold text-center font-mono shadow-lg border border-b border-black
            " role="alert">
                {{ $message->message }}
            </li>
        @endforeach
    </ul>
    <div class="flex justify-end px-2 py-1 gap-2">
        <div class="flex gap-2">
            <a
                href="{{ route('faq') }}"
                class="rounded bg-green-600 text-white px-2 font-bold tracking-wider">
                FAQ
            </a>
            <a
                href="{{ route('donate') }}"
                class="rounded bg-green-600 text-white px-2 font-bold tracking-wider">
                DONATE
            </a>
        </div>
    </div>
    <div class="text-center px-3 md:px-10">
        <a href="{{ route('home') }}" class="text-4xl">
            <span class="text-green-700">cabbage</span>.gay
            <span class="relative">
                @if ($attributes->get('feature'))
                    <span class="absolute bottom-0 left-2 bg-gray-400 text-white px-1 rounded uppercase font-bold tracking-widest text-xs">
                        {{ $attributes->get('feature') }}
                    </span>
                @endif
            </span>
        </a>
    </div>
</header>