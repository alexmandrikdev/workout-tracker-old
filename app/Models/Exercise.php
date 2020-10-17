<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes, EagerLoadPivotTrait;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'user_id'];
}
