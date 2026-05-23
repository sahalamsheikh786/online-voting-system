<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/', 'max:255'],
            'contact_number' => ['required', 'digits:10', 'unique:users,contact_number'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'pattern_lock' => ['required', 'regex:/^\d{4,9}$/'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'district_id' => ['required', 'exists:districts,id'],
            'citizenship_number' => ['required', 'regex:/^[0-9\/-]+$/', 'max:100', 'unique:users,citizenship_number'],
            'voter_id_number' => ['required', 'digits_between:1,30', 'unique:users,voter_id_number'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'User name must contain only alphabet characters and spaces.',
            'contact_number.digits' => 'User contact number must be exactly 10 digits.',
            'password.confirmed' => 'Confirm your both password.',
            'pattern_lock.required' => 'Please draw your pattern lock.',
            'pattern_lock.regex' => 'Pattern lock must use at least 4 dots.',
            'date_of_birth.before_or_equal' => 'You are not eligible to register. Your age must be 18 or above.',
            'citizenship_number.regex' => 'Citizenship number may contain only numbers, slash, and hyphen.',
            'citizenship_number.unique' => 'This citizenship number has already been used.',
            'voter_id_number.digits_between' => 'Voter ID number must contain only numbers.',
            'voter_id_number.unique' => 'This voter ID number has already been used.',
            'image.mimes' => 'Current image must be a jpg, jpeg, or png file.',
        ];
    }
}
