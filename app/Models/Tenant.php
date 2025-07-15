<?php

namespace App\Models;

use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use SoftDeletes, UUIDTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function estate() {
        return $this->belongsTo(Estate::class);
    }
}
