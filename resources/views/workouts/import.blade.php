@extends('layouts.app')

@section('content')
<div class="m-3">
    @if (!session('days'))
        <form action="{{ route('workouts.import.getSheets') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="excel" id="customFile" accept=".xlsx" onchange="form.submit()">
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
                                    <input type="hidden"
                                        name="day{{ $key }}Workouts[]"
                                        value="{{ $workout }}">
                                    <input type="checkbox"
                                        name="day{{ $key }}Workout{{ $workoutKey }}Status"
                                        class="workout-checkbox day-{{ $key }}-workouts"
                                        data-parent-id="{{ $key }}"> {{ $workout }}
                                @endforeach
                            </td>
                            <td><input type="checkbox"
                                class="day-checkbox"
                                id="day-{{ $key }}-checkbox"
                                data-id="{{ $key }}"
                                name="day{{ $key }}Status"
                                ></td>
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
</div>
@endsection

@section('extra-js')
<script>
    $('#select-all').on('change', (e) => {
        const checkboxes = $('input[type="checkbox"]');
        checkboxes.prop('indeterminate', false);
        checkboxes.prop('checked', e.target.checked);
    });

    $('.day-checkbox').on('change', (e) => {
        const id = e.target.getAttribute('data-id');
        $(`.day-${id}-workouts`).prop('checked', e.target.checked);
    });

    $('.workout-checkbox').on('change', (e) => {
        const parentId = e.target.getAttribute('data-parent-id');
        const dayWorkouts = $(`.day-${parentId}-workouts`);
        const dayCheckbox = $(`#day-${parentId}-checkbox`);
        setCheckboxStatus(dayCheckbox, dayWorkouts);
    });

    $('#days-form').on('submit', (e) => {
        const indeterminateCheckboxes = $('input[type="checkbox"]:indeterminate');
        indeterminateCheckboxes.prop('indeterminate', false);
        indeterminateCheckboxes.prop('checked', true);
    });

    $('input[type="checkbox"]:not(#select-all)').on('change', (e) => {
        setCheckboxStatus($('#select-all'), $('input[type="checkbox"]:not(#select-all)'));
    });

    function setCheckboxStatus(target, influencers) {
        const influencersCount = influencers.length;
        const checkedInfluencersCount = influencers.filter(':checked').length;
        const propKey = checkedInfluencersCount === influencersCount ? 'checked' : 'indeterminate';
        const propValue = checkedInfluencersCount > 0;
        target.prop('checked', false);
        target.prop('indeterminate', false);
        target.prop(propKey, propValue);
    }
</script>
@endsection
