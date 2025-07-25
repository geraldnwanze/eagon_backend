<?php

namespace App\Models;

use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestLocation extends Model
{
    use SoftDeletes, UUIDTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
