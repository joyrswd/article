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
    @section('main')
    <main id="app"></main>
    @show
    @php
    $lang = app()->currentLocale();
    @endphp
    @section('bottom')
    <footer>
        <ul class="container">
            <li><input type="date" name="date" id="dateSelector" max="{{date('Y-m-d')}}" value=""></li>
            <li><a href="/rss/{{$lang}}.xml" target="_blank">RSS</a></li>
        </ul>
        <script>
            {
                const dir = "{{route('date.index', ['date' => date('Y-m-d')])}}";
                document.getElementById('dateSelector').addEventListener('change', (e) => {
                    const date = new Date(e.target.value);
                    if(isNaN(date.getDate()) === false) {
                        location.href = dir.split('/').slice(0, -1).join('/') + '/' +e.target.value;
                    }
                });
            }
        </script>
    </footer>
    @show
</body>

</html>