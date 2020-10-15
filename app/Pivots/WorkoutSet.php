<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkoutSet extends Pivot
{
    protected $table = 'workout_sets';

    protected static function booted()
    {
        static::creating(function ($model) {
            $maxSort = WorkoutSet::where([
                'workout_id' => $model->workout_id,
                'set_id' => $model->set_id,
            ])->max('sort');

            $model->sort = $maxSort + 1;
        });
    }
}
