<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use App\Helpers\CommonHelper;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'avatar' => $this->avatar,
            'role' => $this->role,
            'invited_by' => $this->invited_by,
            'is_active' => $this->is_active,
            'qr_code' => CommonHelper::generateQrCode($this->id),
            'joined' => Carbon::parse($this->created_at)->diffForHumans(),
            ...($this->role === RoleEnum::ADMIN->value ? [
                'location' => new EstateResource(Tenant::where('key', $this->tenant_key)->first()->estate),
            ] : [
                'location' => new UserLocationResource($this->location)
            ]),
        ];
    }
}
