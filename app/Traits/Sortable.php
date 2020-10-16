<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public static function bootSortable()
    {
        static::addGlobalScope('sort', function (Builder $builder) {
            return $builder->orderBy('sort');
        });
    }
}
