<?php

namespace App\Services\AI;

interface AIServiceInterface
{
    /**
     * Analyze a rough work request and return structured data.
     *
     * @return array{summary: string, location: string|null, checklist: string[], materials: string[], quotation: string}
     */
    public function analyzeRequest(string $prompt, string $locale = 'bg'): array;
}
