<?php

namespace App\Http\Requests;

use App\Models\Candidate;
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
            'party' => ['required', Rule::in(Candidate::PARTIES)],
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
            'party.required' => 'Please select a candidate party.',
            'party.in' => 'Please select a valid candidate party.',
            'age.integer' => 'Candidate age must contain only numbers.',
            'image.mimes' => 'Candidate image must be jpg, jpeg, or png.',
            'vision.mimes' => 'Candidate vision must be an image, PDF, or Word file.',
        ];
    }
}
