<header class="relative">
    <div class="flex justify-between px-2 py-1 gap-2">
        <div class="relative">
            
        </div>
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