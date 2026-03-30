<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    private const ECB_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    private const CACHE_KEY = 'exchange_rate:ron_eur';

    private const FALLBACK_RATE = 0.1963;

    public function getRonToEur(): float
    {
        return Cache::remember(self::CACHE_KEY, now()->endOfDay(), function () {
            return $this->fetchFromEcb();
        });
    }

    private function fetchFromEcb(): float
    {
        try {
            $response = Http::timeout(10)->get(self::ECB_URL);

            if (! $response->successful()) {
                return self::FALLBACK_RATE;
            }

            $xml = simplexml_load_string($response->body());
            if (! $xml) {
                return self::FALLBACK_RATE;
            }

            $xml->registerXPathNamespace('gesmes', 'http://www.gesmes.org/xml/2002-08-01');
            $xml->registerXPathNamespace('eurofxref', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

            $ronNodes = $xml->xpath("//eurofxref:Cube[@currency='RON']/@rate");

            if (! empty($ronNodes)) {
                $ronPerEur = (float) $ronNodes[0];
                if ($ronPerEur > 0) {
                    $rate = round(1 / $ronPerEur, 6);
                    Log::info('ECB exchange rate updated', ['RON/EUR' => $rate, '1 EUR' => $ronPerEur.' RON']);

                    return $rate;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch ECB exchange rate', ['error' => $e->getMessage()]);
        }

        return self::FALLBACK_RATE;
    }
}
