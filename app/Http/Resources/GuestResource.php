<?php

namespace App\Http\Resources;

use App\Helpers\CommonHelper;
use App\Models\GuestLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'invitation_code' => $this->invitation_code,
            'invitation_status' => $this->invitation_status,
            'entry_permission_status' => $this->entry_permission_status,
            'entry_permission_reason' => $this->entry_permission_reason,
            'entry_permission_status_updated_at' => Carbon::parse($this->entry_permission_status_updated_at)->diffForHumans(),
            'invited' => Carbon::parse($this->created_at)->diffForHumans(),
            'qr_code' => CommonHelper::generateQrCode($this->id),
            'valid_from_date' => $this->valid_from_date,
            'valid_from_time' => $this->valid_from_time,
            'valid_to_date' => $this->valid_to_date,
            'valid_to_time' => $this->valid_to_time,
            'premiss_status' => $this->premiss_status,
            'premiss_status_updated_at' => $this->premiss_status_updated_at,
            'resident' => new UserResource($this->resident),
            'estate' => new EstateResource($this->estate),
            'locations' => GuestLocationResource::collection($this->locations)
        ];
    }
}
