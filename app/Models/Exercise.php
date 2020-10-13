<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes, Multitenantable;

    protected $fillable = [
        'name',
        'user_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'user_id'];

    public function workouts()
    {
        return $this->belongsToMany(Workout::class, 'workout_x_exercises')->withPivot('set', 'amount', 'unit_id');
    }

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutXExercise::class);
    }

}
