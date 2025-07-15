<?php

namespace App\Models;

use App\Traits\TenancyTrait;
use App\Traits\UUIDTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use UUIDTrait, TenancyTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function verify(User|Authenticatable|Model $user, string $code): bool
    {
        $db_code = $user->otp;
        if (!$db_code || $db_code->code != $code || $db_code->expired_at <= now()) {
            return false;
        }
        $db_code->update([
            'confirmation_token' => \Illuminate\Support\Str::random(32)
        ]);
        return true;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
