@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="/css/workouts/show.css?v=1.0.0">
@endsection

@section('content')
    <table class="table table-dark">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Amount</th>
                <th>Rest</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($workouts as $workout)
                <tr>
                    <td colspan="5" class="text-center workout-name-col">{{ $workout['name'] }}</td>
                </tr>
                @foreach ($workout['sets'] as $set)
                    @foreach ($set['data']->exercises as $key => $exercise)
                        <tr class="{{ $key === $set['data']->exercises->keys()->first() ? 'exercise-first-row' : '' }}">
                            @if($key === 0)
                                <td rowspan="{{ $set['data']->exercises->count() }}" class="align-middle text-center">
                                    <div>{{ $set['data']->pivot->sort }}</div>
                                    @for ($i = 1; $i < $set['count']; $i++)
                                        <div>{{ $set['data']->pivot->sort + $i }}</div>
                                    @endfor
                                </td>
                            @endif
                            <td class="exercise-first-col">{{ $exercise->name }}</td>
                            <td>{{ $exercise->pivot->amount }} {{ Str::plural($exercise->pivot->unit->name, $exercise->pivot->amount) }}</td>
                            <td class="exercise-last-col">{{ $exercise->pivot->rest_amount }} {{ $exercise->pivot->restUnit->name }}</td>
                            @if($key === 0)
                                <td rowspan="{{ $set['data']->exercises->count() }}" class="align-middle text-center">x{{ $set['count'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="5" class="text-center">Total Time ({{ $workout['total_time_unit'] }}): {{ $workout['total_time'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
