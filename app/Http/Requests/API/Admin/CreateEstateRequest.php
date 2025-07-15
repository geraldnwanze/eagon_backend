<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateEstateRequest extends FormRequest
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
            'estate_name' => ['required', 'string', 'max:255'],
            'estate_address' => ['required', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255']
        ];
    }
}
