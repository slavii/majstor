<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AIQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:10', 'max:5000'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'prompt.required' => 'Моля, опишете заявката.',
            'prompt.min' => 'Описанието трябва да е поне 10 символа.',
        ];
    }
}
