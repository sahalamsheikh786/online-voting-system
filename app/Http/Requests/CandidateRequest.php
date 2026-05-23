<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $candidateId = $this->route('candidate')?->id;

        return [
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/', 'max:255'],
            'party' => ['nullable', 'regex:/^[A-Za-z0-9\s\.\-&]+$/', 'max:255'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'image' => [$candidateId ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'vision' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:4096'],
            'district_id' => ['required', 'exists:districts,id'],
            'email' => ['required', 'email', Rule::unique('candidates', 'email')->ignore($candidateId)],
            'position' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Candidate name must contain only alphabet characters and spaces.',
            'party.regex' => 'Party name can contain letters, numbers, spaces, dot, dash, and ampersand only.',
            'age.integer' => 'Candidate age must contain only numbers.',
            'image.mimes' => 'Candidate image must be jpg, jpeg, or png.',
            'vision.mimes' => 'Candidate vision must be an image, PDF, or Word file.',
        ];
    }
}
