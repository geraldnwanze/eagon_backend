<?php

namespace App\Jobs;

use App\Mail\WelcomeAndVerificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class WelcomeAndVerificationMailJob implements ShouldQueue
{
    use Queueable;

    public $resident;
    public $password;

    /**
     * Create a new job instance.
     */
    public function __construct($resident, $password)
    {
        $this->resident = $resident;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->resident->email)->send(new WelcomeAndVerificationMail($this->resident, $this->password));
    }
}
