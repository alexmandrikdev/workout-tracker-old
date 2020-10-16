<?php

namespace App\Pivots;

use App\Models\Unit;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SetExercise extends Pivot
{
    use Sortable;

    protected $table = 'set_exercises';

    protected static function booted()
    {
        static::creating(function ($model) {
            $maxSort = SetExercise::where([
                'set_id' => $model->set_id,
            ])->max('sort');

            $model->sort = $maxSort + 1;
        });
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function restUnit()
    {
        return $this->belongsTo(Unit::class);
    }
}
