<?php

namespace App\Pivots;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkoutSet extends Pivot
{
    use Sortable;

    protected $table = 'workout_sets';

    protected static function booted()
    {
        static::creating(function ($model) {
            $maxSort = WorkoutSet::where([
                'workout_id' => $model->workout_id,
            ])->max('sort');

            $model->sort = $maxSort + 1;
        });
    }
}
