<?php

namespace App\Models;

use App\Helpers\CommonHelper;
use App\Traits\TenancyTrait;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes, UUIDTrait, TenancyTrait;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->invitation_code = CommonHelper::generateInvitationCode();
        });
    }

    public function resident()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function locations()
    {
        return $this->hasMany(GuestLocation::class);
    }

    public function scopeGuestFilter(Builder $query, $filter_array = [])
    {
        if (!empty($filter_array)) {
            $invitation_status = $filter_array['invitation_status'];
            $filter = $filter_array['filter'];
            if ($invitation_status) {
                $query = $query->where('invitation_status', trim($invitation_status));
            }

            if ($filter && strlen($filter) >= 3) {
                $query = $query
                    ->where('full_name', 'like', '%'.trim($filter).'%')
                    ->orWhere('invitation_code', '%'.trim($filter).'%')
                    ->orWhere('email', 'like', '%'.trim($filter).'%');
            }

        }

        return $query;
    }
}
