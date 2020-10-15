<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workout extends Model
{
    use SoftDeletes, Multitenantable;

    protected $guarded = [];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at', 'user_id'];
}
