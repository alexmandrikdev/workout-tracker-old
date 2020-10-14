@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="/css/calendar/index.css?v=1.0.1">
@endsection

@section('content')
    <div class="mb-4">
        <form id="date-select-form" class="mx-auto">
            @isset($workoutName)
                <input type="hidden" name="workout_name" value="{{ $workoutNameFilter }}">
            @endisset
            <div class="row">
                <div class="col">
                    <select id="year-select" class="custom-select" name="year">
                        @for ($year2 = $minWorkoutDate->year; $year2 <= $maxWorkoutDate->year; $year2++)
                            <option {{ $year == $year2 ? 'selected' : '' }} value="{{ $year2 }}">{{ $year2 }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col">
                    <select id="month-select" class="custom-select" name="month">
                        @foreach ($monthNames as $key => $monthName)
                            <option {{ $key + 1 == $month ? 'selected' : '' }} value="{{ $key + 1 }}">{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
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
