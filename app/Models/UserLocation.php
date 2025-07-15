<?php

namespace App\Models;

use App\Traits\TenancyTrait;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLocation extends Model
{
    use SoftDeletes, UUIDTrait, TenancyTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
