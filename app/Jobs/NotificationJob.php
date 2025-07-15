<?php

namespace App\Jobs;

use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotificationJob implements ShouldQueue
{
    use Queueable;

    public $fcm_token;
    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct(string $fcm_token, array $data)
    {
        $this->fcm_token = $fcm_token;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        PushNotificationService::sendMessage($this->fcm_token, $this->data);
    }
}
