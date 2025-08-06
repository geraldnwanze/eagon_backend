<?php

namespace App\Http\Requests\API\Resident;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuestValidDateAndTimeRequest extends FormRequest
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
            'valid_from_date' => ['required', 'date', 'date_format:Y-m-d'],
            'valid_from_time' => ['required', 'date_format:H:i'],
            'valid_to_date' => ['required', 'date', 'date_format:Y-m-d', 'after:valid_from_date'],
            'valid_to_time' => ['required', 'date_format:H:i', 'after:valid_from_time'],
        ];
    }
}
