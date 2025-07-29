<?php

namespace App\Http\Requests\API\Resident;

use Illuminate\Foundation\Http\FormRequest;

class InviteGuestRequest extends FormRequest
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
            'full_name' => ['required', 'string'],
            'valid_from_date' => ['required', 'date', 'date_format:Y-m-d'],
            'valid_from_time' => ['required', 'date_format:H:i'],
            'valid_to_date' => ['required', 'date', 'date_format:Y-m-d'],
            'valid_to_time' => ['required', 'date_format:H:i'],
            'phone_number' => ['required'],
            'email' => ['nullable', 'email'],
            'estate_id' => ['required', 'string', 'exists:estates,id'],
        ];
    }

}
