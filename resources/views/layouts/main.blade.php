<!DOCTYPE html>
<html>
    <head>
        <title>{{ $attributes->has('title') ? $attributes->get('title') . ' - ' : '' }}{{ config('app.name') }}</title>
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
            <main class="flex-1 px-3 md:px-10 py-3 md:py-7 w-5xl max-w-screen mx-auto">
                {{ $slot }}
            </main>
            <x-footer></x-footer>
        </div>
    </body>
</html>