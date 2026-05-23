<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/', 'max:255'],
            'contact_number' => ['required', 'digits:10', Rule::unique('users', 'contact_number')->ignore($userId)],
            'district_id' => ['required', 'exists:districts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'User name must contain only alphabet characters and spaces.',
            'contact_number.digits' => 'User contact number must be exactly 10 digits.',
        ];
    }
}
