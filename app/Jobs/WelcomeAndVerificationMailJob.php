<?php

namespace App\Jobs;

use App\Mail\WelcomeAndVerificationMail;
use App\Models\Estate;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class WelcomeAndVerificationMailJob implements ShouldQueue
{
    use Queueable;

    public $resident;
    public $password;
    public $estate;

    /**
     * Create a new job instance.
     */
    public function __construct($resident, $password)
    {
        $this->resident = $resident;
        $this->password = $password;
        $tenant = Tenant::where('key', $resident->tenant_key)->first();
        $this->estate = $tenant->estate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->resident->email)->send(new WelcomeAndVerificationMail($this->resident, $this->password, $this->estate));
    }
}
