<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    private const ENDPOINT = 'https://translate.googleapis.com/translate_a/single';

    public function translate(string $text, string $targetLang, string $sourceLang = 'ro'): string
    {
        if (empty(trim($text)) || $targetLang === $sourceLang) {
            return $text;
        }

        $cacheKey = 'translation:'.md5($text.$sourceLang.$targetLang);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)->get(self::ENDPOINT, [
                'client' => 'gtx',
                'sl' => $sourceLang,
                'tl' => $targetLang,
                'dt' => 't',
                'q' => $text,
            ]);

            if (! $response->successful()) {
                return $text;
            }

            $data = $response->json();
            $translated = '';
            if (is_array($data) && isset($data[0])) {
                foreach ($data[0] as $segment) {
                    if (isset($segment[0])) {
                        $translated .= $segment[0];
                    }
                }
            }

            $result = $translated ?: $text;
            Cache::put($cacheKey, $result, now()->addDays(7));

            return $result;
        } catch (\Exception $e) {
            Log::warning('Translation failed', ['error' => $e->getMessage()]);

            return $text;
        }
    }

    public function translateListing(array $listing, string $targetLang): array
    {
        if ($targetLang === 'ro') {
            return $listing;
        }

        if (! empty($listing['title'])) {
            $listing['title_translated'] = $this->translate($listing['title'], $targetLang);
        }

        if (! empty($listing['description'])) {
            $listing['description_translated'] = $this->translate($listing['description'], $targetLang);
        }

        if (! empty($listing['params'])) {
            foreach ($listing['params'] as $key => &$param) {
                $param['name'] = $this->translate($param['name'], $targetLang);
                $param['value'] = $this->translate($param['value'], $targetLang);
            }
        }

        if (! empty($listing['posted_at'])) {
            $listing['posted_at'] = $this->translate($listing['posted_at'], $targetLang);
        }

        return $listing;
    }
}
