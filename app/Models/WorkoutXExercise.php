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
        'unit_id',
        'rest_amount',
        'rest_unit_id'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function workout()
    {
        return $this->belongsTo(workout::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
