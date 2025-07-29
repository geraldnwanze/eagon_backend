<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Guest;
use App\Models\User;
use App\Traits\ForceValidationErrorToJson;
use Illuminate\Foundation\Http\FormRequest;

class SaveMessageRequest extends FormRequest
{
    use ForceValidationErrorToJson;
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
            'receiver_id' => ['required', 'exists:users,id'],
            'receiver_type' => ['required', 'in:guest,resident,admin'],
            'message_body' => ['required', 'string'],
        ];
    }
}
