<?php

namespace App\Observers;

use App\Models\Estate;
use App\Models\Tenant;

class EstateObserver
{
    /**
     * Handle the Estate "created" event.
     */
    public function created(Estate $estate): void
    {
        Tenant::create([
            'estate_id' => $estate->id,
            'name' => config('app.name') .' - '. $estate->name,
            'key' => config('app.name') . '_' . $estate->id
        ]);
    }

}
