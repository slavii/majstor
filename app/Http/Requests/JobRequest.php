<?php

namespace App\Http\Requests;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['sometimes', Rule::in(array_keys(Job::STATUSES))],
            'scheduled_date' => ['nullable', 'date'],
            'estimated_price' => ['nullable', 'numeric', 'min:0'],
            'actual_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'checklist' => ['nullable', 'json'],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Моля, изберете клиент.',
            'client_id.exists' => 'Избраният клиент не съществува.',
            'title.required' => 'Моля, въведете заглавие на задачата.',
        ];
    }
}
