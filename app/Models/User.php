<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\TenancyTrait;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, UUIDTrait, TenancyTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function otp()
    {
        return $this->hasOne(OTP::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeIndividualTenant($query, $tenantKey)
    {
        return $query->where('tenant_key', $tenantKey);
    }
}
