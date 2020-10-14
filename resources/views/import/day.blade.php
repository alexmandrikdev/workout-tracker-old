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
