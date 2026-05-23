<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_number' => ['required', 'digits:10'],
            'password' => ['required', 'string'],
            'pattern_lock' => ['required', 'regex:/^\d{4,9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.digits' => 'Contact number must be exactly 10 digits.',
            'pattern_lock.required' => 'Please draw your pattern lock.',
            'pattern_lock.regex' => 'Pattern lock must use at least 4 dots.',
        ];
    }
}
