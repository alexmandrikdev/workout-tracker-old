@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="/css/workouts/index.css?v=1.0.0">
@endsection

@section('content')
    <div class="mx-0 my-5 m-sm-5 calendar-container bg-dark">
        <div class="calendar">
            @foreach ($dayNames as $dayName)
                <span class="day-name">
                    {{ $dayName }}
                </span>
            @endforeach
            @foreach ($days as $day)
                @include('workouts.index.day')
            @endforeach
        </div>
    </div>
@endsection
