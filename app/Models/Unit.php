<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use Multitenantable;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];
}
