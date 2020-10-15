<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SetExercise extends Model
{
    use SoftDeletes, Multitenantable;

    protected $guarded = [];

    protected $hidden = ['user_id'];
}
