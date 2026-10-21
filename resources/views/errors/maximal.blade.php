<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        @vite([
            'resources/css/app.css',
        ])
    </head>
    <body class="antialiased">
        <x-header></x-header>
        <main class="flex-1 px-1 md:px-10 py-3 md:py-7 w-5xl max-w-screen mx-auto mb-40 text-center">
            <h1 class="text-5xl font-bold">@yield('code')</h1>
            <p class="text-xl my-3">@yield('message')</p>
            <a class="underline text-green-600 text-xl" href="{{ route('home') }}">Go To The Homepage</a>
        </main>
        <x-footer></x-footer>
    </body>
</html>
