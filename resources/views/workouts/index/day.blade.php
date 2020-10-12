@if (isset($day['clickable']) && $day['clickable'])
<a href="/workouts/{{ $day['date']->toDateString() }}"
@else
<div
@endif
    class="day {{ !isset($day['workouts']) ? 'day-disabled' : '' }} {{ $day['date']->isCurrentDay() ? 'day-today' : '' }}
    {{ isset($day['clickable']) && $day['clickable'] ? '' : 'day-not-allowed' }}">
    <div class="day-number">{{ $day['date']->day }}</div>
    @isset($day['workouts'])
        <div class="text-center day-workouts">{{ $day['workouts']->join(', ') }}</div>
    @endisset
@if (isset($day['clickable']) && $day['clickable'])
</a>
@else
</div>
@endif
