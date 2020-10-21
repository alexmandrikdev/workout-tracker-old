<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Workout Tracker</title>

    <link rel="stylesheet" href="/css/app.css?v=1.0.2">
    @livewireStyles
    @yield('extra-css')
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include('layouts.app.sidebar')
        <x-layouts.app.page-content>
            @yield('content')
        </x-layouts.app.page-content>

        @if (session('importing'))
            <div class="toast notification-bar" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                  <strong class="mr-auto">Importing Status</strong>
                </div>
                <div class="toast-body">
                    @livewire('importing-progress-bar')
                </div>
              </div>
        @endif
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts
    @yield('extra-js')
</body>

</html>
