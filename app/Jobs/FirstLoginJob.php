<?php

namespace App\Jobs;

use App\Mail\FirstLoginMail;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class FirstLoginJob implements ShouldQueue
{
    use Queueable;

    public User|Authenticatable $user;
    public string $code;

    /**
     * Create a new job instance.
     */
    public function __construct(User|Authenticatable $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new FirstLoginMail($this->user, $this->code));
    }
}
