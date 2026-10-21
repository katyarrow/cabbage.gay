<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <style>
            body * {
                font-family: '' 'sans-serif';
            }
        </style>
    </head>
    <body class="antialiased">
        Cabbage.gay
        <h1 style="padding-bottom: 0rem;">@yield('code')</h1>
        <p>@yield('message')</p>
        <a href="{{ route('home') }}">Go To The Homepage</a>
    </body>
</html>
