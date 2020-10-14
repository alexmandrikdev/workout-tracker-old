<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Workout Tracker</title>

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @yield('extra-css')
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include('layouts.app.sidebar')
        <x-layouts.app.page-content>
            @yield('content')
        </x-layouts.app.page-content>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('extra-js')
</body>

</html>
