<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class ImportStatus extends Model
{
    use BelongsToUser;

    protected $guarded = ['id'];
}
