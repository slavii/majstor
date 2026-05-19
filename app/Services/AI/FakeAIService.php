<?php

namespace App\Services\AI;

class FakeAIService implements AIServiceInterface
{
    public function analyzeRequest(string $prompt, string $locale = 'bg'): array
    {
        return [
            'summary' => 'Примерно обобщение на заявката: '.mb_substr($prompt, 0, 100),
            'location' => 'Неизвестно местоположение',
            'checklist' => [
                'Оглед на обекта',
                'Подготовка на материали',
                'Изпълнение на дейността',
                'Финална проверка',
            ],
            'materials' => [
                'Основен материал — по преценка',
                'Консумативи',
                'Крепежни елементи',
            ],
            'quotation' => "ОФЕРТА\n\nОписание: {$prompt}\n\nТруд: _____ лв.\nМатериали: _____ лв.\nОбщо: _____ лв.\n\n* Цените са ориентировъчни",
        ];
    }
}
