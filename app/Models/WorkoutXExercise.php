<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutXExercise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workout_id',
        'exercise_id',
        'set',
        'amount',
        'unit',
        'rest_amount',
        'rest_unit'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
