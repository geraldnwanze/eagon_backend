<?php

namespace App\Observers;

use App\Enums\RoleEnum;
use App\Models\Estate;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserLocation;

class AdminObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        
    }

}
