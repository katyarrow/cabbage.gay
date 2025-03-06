<!DOCTYPE html>
<html>
    <head>
        <title>Admin - {{ config('app.name') }}</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @auth
            <form action="{{ route('logout') }}" method="POST" class="fixed top-1 right-2">
                @csrf
                @method('delete')
                <button class="bg-red-500 rounded px-1 text-white font-semibold">Logout</button>
            </form>
        @endauth
        <div id="app" class="min-h-screen flex flex-col">
            <main class="flex-1 px-3 md:px-10 py-3 md:py-7 w-5xl max-w-screen mx-auto">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>