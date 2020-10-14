@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="/css/import.css">
@endsection

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
            <table id="days" class="table table-dark text-center mb-0">
                <thead>
                    <tr>
                        <th>@lang('Date')</th>
                        <th>@lang('Workouts')</th>
                        <th><input type="checkbox" class="select-all"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $key = 0
                    @endphp
                    @foreach (session('days') as $date => $workouts)
                        <input type="hidden" name="days[]" value="{{ $date }}">
                        @include('import.day')
                        @php
                            $key++
                        @endphp
                    @endforeach
                </tbody>
            </table>
            @if (session('importedDays')->isNotEmpty())
                <div class="bg-dark p-3 text-center" id="imported-days-toggle-button">
                    <a href="#imported-days-container" class="dropdown-toggle collapsed text-decoration-none text-white w-100 d-block"
                        data-toggle="collapse">@lang('Imported Days')</a>
                </div>
                <div id="imported-days-container" class="collapse">
                    <table id="imported-days" class="table table-dark text-center text-danger">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Workouts')</th>
                                <th><input type="checkbox" class="select-all"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('importedDays') as $date => $workouts)
                                <input type="hidden" name="days[]" value="{{ $date }}">
                                @include('import.day')
                                @php
                                    $key++
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="text-center mt-3">
                <input type="submit" value="@lang('Import')" class="btn btn-secondary">
            </div>
            <input type="hidden" name="daysCount" value="{{ $key }}">
        </form>
    @endif
@endsection

@section('extra-js')
    <script src="/js/workouts/import.js"></script>
@endsection
