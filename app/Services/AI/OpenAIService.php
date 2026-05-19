<?php

namespace App\Services\AI;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService implements AIServiceInterface
{
    public function analyzeRequest(string $prompt, string $locale = 'bg'): array
    {
        $systemPrompt = <<<'PROMPT'
        You are a helpful assistant for Bulgarian construction and repair businesses.
        Analyze the following work request and respond ONLY in Bulgarian.
        Return a JSON object with these keys:
        - "summary": brief summary of the request
        - "location": extracted location or null
        - "checklist": array of task steps
        - "materials": array of estimated materials with approximate quantities
        - "quotation": a draft quotation text in Bulgarian with placeholder prices
        PROMPT;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        $content = $response->choices[0]->message->content;

        return json_decode($content, true) ?? [
            'summary' => 'Грешка при обработка',
            'location' => null,
            'checklist' => [],
            'materials' => [],
            'quotation' => '',
        ];
    }
}
