<?php

namespace App\Models;

use App\Pivots\WorkoutSet;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workout extends Model
{
    use SoftDeletes, BelongsToUser;

    protected $guarded = [];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at', 'user_id'];

    public function sets()
    {
        return $this->belongsToMany(Set::class, 'workout_sets')
            ->using(WorkoutSet::class)
            ->orderBy('sort');
    }

    public function totalTimeUnit()
    {
        return $this->belongsTo(Unit::class);
    }
}
