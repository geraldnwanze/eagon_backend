<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UUIDTrait
{
    protected static function bootUUIDTrait()
    {
        static::creating(function($model) {
            $model->id = Str::uuid();
        });
    }
}
