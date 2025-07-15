<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes, UUIDTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function sender()
    {
        if ($this->sender_type === RoleEnum::GUEST->value) {
            return $this->belongsTo(Guest::class, 'sender_id');
        }
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        if ($this->receiver_type === RoleEnum::GUEST->value) {
            return $this->belongsTo(Guest::class, 'receiver_id');
        }
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
