@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="/css/calendar/index.css?v=1.0.1">
@endsection

@section('content')
    @include('calendar.index.date-selector-form')
    <div class="calendar-container bg-dark">
        <div class="calendar">
            @foreach ($dayNames as $dayName)
                <span class="day-name">
                    {{ $dayName }}
                </span>
            @endforeach
            @foreach ($days as $day)
                @include('calendar.index.day')
            @endforeach
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        const dateSelectForm = $('#date-select-form');

        $('#year-select').on('change', () => {
            dateSelectForm.submit();
        });

        $('#month-select').on('change', () => {
            dateSelectForm.submit();
        });
    </script>
@endsection
