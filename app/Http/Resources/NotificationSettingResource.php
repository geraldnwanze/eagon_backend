<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'allow_all_notifications' => (boolean) $this->allow_all_notifications,
            'allow_message_notifications' => (boolean) $this->allow_message_notifications,
            'allow_work_notifications' => (boolean) $this->allow_work_notifications,
            'start_work_notification_time_before' => $this->start_work_notification_time_before
        ];
    }
}
