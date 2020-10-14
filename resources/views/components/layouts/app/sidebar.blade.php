<div class="bg-dark text-light border-right" id="sidebar-wrapper">
    <div class="sidebar-heading">Workout Tracker</div>
    <div class="list-group list-group-flush">
        <a href="/calendar" class="list-group-item list-group-item-action bg-dark text-light">@lang('Calendar')</a>
        <a href="#workouts" data-toggle="collapse"
            class="dropdown-toggle collapsed list-group-item list-group-item-action bg-dark text-light">@lang('My Workouts')</a>
            <div id="workouts" class="collapse list-group">
                @foreach ($workoutNames as $workoutName)
                    <a href="/calendar?wokout_name={{ $workoutName }}" class="list-group-item list-group-item-action bg-dark text-light">{{ $workoutName }}</a>
                @endforeach
            </div>
        <a href="/workouts/import" class="list-group-item list-group-item-action bg-dark text-light">@lang('Import Workouts')</a>
        <a href="#" class="list-group-item list-group-item-action bg-dark text-light">@lang('Excersises')</a>
    </div>
</div>
