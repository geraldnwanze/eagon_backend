<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'sender' => $this->sender_type === RoleEnum::GUEST->value ? new GuestResource(Guest::where('uuid', $this->sender_id)->first()) : new UserResource(User::where('uuid', $this->sender_id)->first()),
            'receiver' => $this->receiver_type === RoleEnum::GUEST->value ? new GuestResource(Guest::where('uuid', $this->receiver_id)->first()) : new UserResource(User::where('uuid', $this->receiver_id)->first()),
            'message_body' => $this->message_body
        ];
    }
}
