<?php

namespace App\Jobs;

use App\Mail\WelcomeAndAuthDetailMail;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class WelcomeAndAuthDetailJob implements ShouldQueue
{
    use Queueable;

    public User|Authenticatable $user;
    public string $password;
    /**
     * Create a new job instance.
     */
    public function __construct(User|Authenticatable $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new WelcomeAndAuthDetailMail($this->user, $this->password));
    }
}
