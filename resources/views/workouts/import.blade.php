@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (!session('days'))
        <form action="{{ route('workouts.import.getSheets') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="excel" id="customFile" accept=".xlsx"
                    onchange="form.submit()">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </form>
    @else
        <form id="days-form" action="/workouts/import" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="excelPath" value="{{ session('excelPath') }}">
            <table class="table table-dark text-center">
                <thead>
                    <tr>
                        <th>@lang('Date')</th>
                        <th>@lang('Workouts')</th>
                        <th><input type="checkbox" id="select-all"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $key = 0
                    @endphp
                    @foreach (session('days') as $date => $workouts)
                        <input type="hidden" name="days[]" value="{{ $date }}">
                        <tr>
                            <td>{{ $date }}</td>
                            <td>
                                @foreach ($workouts as $workoutKey => $workout)
                                <input type="hidden" name="day{{ $key }}Workouts[]" value="{{ $workout['name'] }}">
                                <input type="checkbox" name="day{{ $key }}Workout{{ $workoutKey }}Status"
                                    class="workout-checkbox day-{{ $key }}-workouts" data-parent-id="{{ $key }}">
                                <span class="{{ $workout['isImported'] ? 'text-danger' : '' }}">{{ $workout['name'] }}</span>
                                @endforeach
                            </td>
                            <td>
                                <input type="checkbox" class="day-checkbox" id="day-{{ $key }}-checkbox" data-id="{{ $key }}"
                                    name="day{{ $key }}Status">
                            </td>
                        </tr>
                        @php
                            $key++
                        @endphp
                    @endforeach
                    <input type="hidden" name="daysCount" value="{{ $key }}">
                </tbody>
            </table>
            <div class="text-center">
                <input type="submit" value="@lang('Import')" class="btn btn-secondary">
            </div>
        </form>
    @endif
@endsection

@section('extra-js')
    <script src="/js/workouts/import.js"></script>
@endsection
