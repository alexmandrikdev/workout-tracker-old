@extends('layouts.app')

@section('content')
<div class="container mt-5">
    @if (!$sheets)
        <form action="{{ route('workouts.import.getSheets') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="excel" id="customFile" accept=".xlsx" onchange="form.submit()">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </form>
    @else
        {{ $sheets }}
    @endif
</div>
@endsection
