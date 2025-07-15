<?php

namespace App\Http\Requests\API\Resident;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuestCodeStatusRequest extends FormRequest
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
            'invitation_code' => ['required', 'exists:guests,invitation_code'],
            'action' => ['required', 'in:activate,deactivate']
        ];
    }
}
