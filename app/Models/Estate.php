<?php

namespace App\Models;

use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estate extends Model
{
    use SoftDeletes, UUIDTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function guests()
    {
        return $this->belongsToMany(Guest::class);
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}
