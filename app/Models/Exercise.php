<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Pivots\SetExercise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes, EagerLoadPivotTrait;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'user_id'];

    public function sets()
    {
        return $this->belongsToMany(Set::class, 'set_exercises')->using(SetExercise::class);
    }
}
