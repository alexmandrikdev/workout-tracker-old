<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workout extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'user_id',
        'date',
        'total_time',
        'total_time_unit_id',
    ];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'workout_x_exercises')->withPivot('set', 'amount', 'unit');
    }

    public function workoutXExercises()
    {
        return $this->hasMany(WorkoutXExercise::class);
    }
}
