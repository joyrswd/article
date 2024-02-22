<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @yield('head')
</head>
<body>
    <header>
        <div class="bg-body-tertiary p-3 p-sm-5 mb-4">
            <div class="container">
                <h1 class="display-4"><a href="/" class="text-black-50 text-decoration-none">{{ config('app.name') }}</a></h1>
                @yield('top')
            </div>
        </div>
    </header>
    @yield('middle')
    <main id="app"></main>
    @yield('bottom')
</body>
</html>