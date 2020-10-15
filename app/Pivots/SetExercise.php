<?php

namespace App\Pivots;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SetExercise extends Pivot
{
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function restUnit()
    {
        return $this->belongsTo(Unit::class);
    }
}
