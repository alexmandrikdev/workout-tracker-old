<div class="mb-4">
    <form id="date-select-form" class="mx-auto">
        @isset($workoutNameFilter)
            <input type="hidden" name="workout_name" value="{{ $workoutNameFilter }}">
        @endisset
        <div class="row align-items-center">
            <a class="text-dark"
                href="/calendar?{{ isset($workoutNameFilter) ? '&workout_name=' . $workoutNameFilter : '' }}{{ isset($year) ? '&year=' . ($month - 1 < 1 ? $year - 1 : $year) : '' }}{{ isset($month) ? '&month=' . ($month - 1 < 1 ? 12 : $month - 1) : '' }}">
                <svg class="h-100" width="1.8em" height="1.8em" viewBox="0 0 16 16" class="bi bi-arrow-left-square-fill"
                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm9.5 8.5a.5.5 0 0 0 0-1H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5z" />
                </svg>
            </a>
            <div class="col">
                <select id="year-select" class="custom-select" name="year">
                    @if ($year < $minWorkoutDate->year)
                        <option selected value="{{ $year }}">{{ $year }}</option>
                    @endif
                    @for ($year2 = $minWorkoutDate->year; $year2 <= $maxWorkoutDate->year; $year2++)
                        <option {{ $year == $year2 ? 'selected' : '' }} value="{{ $year2 }}">{{ $year2 }}</option>
                    @endfor
                    @if ($year > $minWorkoutDate->year)
                        <option selected value="{{ $year }}">{{ $year }}</option>
                    @endif
                </select>
            </div>
            <div class="col">
                <select id="month-select" class="custom-select" name="month">
                    @foreach ($monthNames as $key => $monthName)
                        <option {{ $key + 1 == $month ? 'selected' : '' }} value="{{ $key + 1 }}">{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <a class="text-dark text-decoration-none"
                href="/calendar?{{ isset($workoutNameFilter) ? '&workout_name=' . $workoutNameFilter : '' }}{{ isset($year) ? '&year=' . ($month + 1 > 12 ? $year + 1 : $year) : '' }}{{ isset($month) ? '&month=' . ($month + 1 > 12 ? 1 : $month + 1) : '' }}">
                <svg class="h-100" width="1.8em" height="1.8em" viewBox="0 0 16 16"
                    class="bi bi-arrow-right-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm2.5 8.5a.5.5 0 0 1 0-1h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5z" />
                </svg>
            </a>
        </div>
    </form>
</div>
