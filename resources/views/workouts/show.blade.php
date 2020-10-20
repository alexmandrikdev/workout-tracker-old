@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="/css/workouts/show.css">
@endsection

@section('content')
    <table class="table table-dark">
        <thead>
            <tr>
                <th>Name</th>
                <th>Amount</th>
                <th>Rest</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($workouts as $workout)
                <tr>
                    <td colspan="4" class="text-center workout-name-col">{{ $workout['name'] }}</td>
                </tr>
                @foreach ($workout['sets'] as $set)
                    @foreach ($set['data']->exercises as $key => $exercise)
                        <tr class="{{ $key === $set['data']->exercises->keys()->first() ? 'exercise-first-row' : '' }}">
                            <td>{{ $exercise->name }}</td>
                            <td>{{ $exercise->pivot->amount }} {{ Str::plural($exercise->pivot->unit->name, $exercise->pivot->amount) }}</td>
                            <td class="exercise-last-col">{{ $exercise->pivot->rest_amount }} {{ $exercise->pivot->restUnit->name }}</td>
                            @if($key === 0)
                            <td rowspan="{{ $set['data']->exercises->count() }}" class="align-middle text-center">x{{ $set['count'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
@endsection
