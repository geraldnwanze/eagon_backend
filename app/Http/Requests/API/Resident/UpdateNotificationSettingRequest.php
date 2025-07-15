<?php

namespace App\Http\Requests\API\Resident;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'allow_all_notifications' => ['required', 'boolean'],
            'allow_message_notifications' => ['required', 'boolean'],
            'allow_work_notifications' => ['required', 'boolean'],
            'allow_location_update_notification' => ['required', 'boolean'],
            'start_work_notification_time_before' => ['nullable', 'string']
        ];
    }
}
