<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'max:10'],
            'photos.*' => ['image', 'max:5120'],
            'category' => ['sometimes', 'in:before,after,progress,general'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Моля, изберете поне една снимка.',
            'photos.max' => 'Максимум 10 снимки наведнъж.',
            'photos.*.image' => 'Файлът трябва да е изображение.',
            'photos.*.max' => 'Снимката не може да надвишава 5MB.',
        ];
    }
}
