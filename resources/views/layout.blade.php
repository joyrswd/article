<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->currentLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.title') }}</title>
    <meta name="description" content='{!! str_replace("\n", "", __("site.description"))!!}'>
    <meta name="keywords" content="{{__('site.keywords')}}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @yield('head')
</head>
<body>
    <header class="bg-body-tertiary sticky-top shadow-sm">
        <div class="container p-4">
            <h1 class="display-4"><a href="/" class="text-black-50 text-decoration-none">{{ config('app.name') }}</a></h1>
            @yield('top')
        </div>
    </header>
    @yield('middle')
    <main id="app"></main>
    @yield('bottom')
</body>
</html>