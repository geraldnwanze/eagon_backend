<?php

namespace App\Models;

use App\Traits\TenancyTrait;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationSetting extends Model
{
    use SoftDeletes, UUIDTrait, TenancyTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

}
