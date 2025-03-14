<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>{{ $attributes->has('title') ? $attributes->get('title') . ' - ' : '' }}{{ config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/cabbage-32.png') }}">

        <meta name="title" content="{{ $attributes->has('title') ? $attributes->get('title') . ' - ' : '' }}{{ config('app.name') }}" />
        <meta name="description" content="{{ $attributes->get('description') }}" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ request()->url() }}" />
        <meta property="og:title" content="{{ $attributes->has('title') ? $attributes->get('title') . ' - ' : '' }}{{ config('app.name') }}" />
        <meta property="og:description" content="{{ $attributes->get('description') }}" />
        <meta property="og:image" content="{{ asset('images/cabbage-1200.png') }}" />

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:url" content="{{ request()->url() }}" />
        <meta property="twitter:title" content="{{ $attributes->has('title') ? $attributes->get('title') . ' - ' : '' }}{{ config('app.name') }}" />
        <meta property="twitter:description" content="{{ $attributes->get('description') }}" />
        <meta property="twitter:image" content="{{ asset('images/cabbage-1200.png') }}" />


        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        @if ($attributes->has('noindex'))
            <meta name="robots" content="noindex">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app" class="min-h-screen flex flex-col">
            <x-header></x-header>
            <main class="flex-1 px-3 md:px-10 py-3 md:py-7 w-5xl max-w-screen mx-auto mb-40">
                {{ $slot }}
            </main>
            <x-footer></x-footer>
        </div>
    </body>
</html>