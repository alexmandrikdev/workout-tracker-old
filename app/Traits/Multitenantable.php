<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable
{
    public static function bootMultitenantable()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->user_id = auth()->id();
            }
        });

        static::addGlobalScope('user_id', function (Builder $builder) {
            if (auth()->check()) {
                return $builder->where('user_id', auth()->id());
            }
        });
    }
}
