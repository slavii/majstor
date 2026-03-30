<?php

namespace App\Services;

use App\Models\CachedListing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OlxScraper
{
    private const BASE_URL = 'https://www.olx.ro';

    private const API_URL = 'https://www.olx.ro/api/v1/offers/';

    private const CACHE_TTL_MINUTES = 30;

    private const API_CATEGORY_IDS = [
        'rims' => 1647,
        'tyres' => 1649,
        'all' => 1640,
    ];

    private const CATEGORY_PATHS = [
        'rims' => '/piese-auto/roti-jante-anvelope/jante-si-roti/',
        'tyres' => '/piese-auto/roti-jante-anvelope/anvelope/',
        'all' => '/piese-auto/roti-jante-anvelope/',
    ];

    private const DIAMETER_PATTERN = '/R\s*(\d{2})/i';

    private const BOLT_PATTERN = '/(\d)x(\d{2,3}(?:[.,]\d)?)/';

    private const TYRE_SIZE_PATTERN = '/(\d{3})\s*[\/]\s*(\d{2,3})\s*R\s*(\d{2})/i';

    public function search(array $filters = []): array
    {
        $cacheKey = $this->buildCacheKey($filters);
        $cached = $this->getCachedResults($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        try {
            $listings = $this->searchViaApi($filters, $cacheKey);
        } catch (\Exception $e) {
            Log::warning('OLX API failed, falling back to HTML', ['error' => $e->getMessage()]);
            $listings = $this->searchViaHtml($filters, $cacheKey);
        }

        return $listings;
    }

    private function searchViaApi(array $filters, string $cacheKey): array
    {
        $category = $filters['category'] ?? 'rims';
        $params = [
            'offset' => 0,
            'limit' => 50,
            'category_id' => self::API_CATEGORY_IDS[$category] ?? self::API_CATEGORY_IDS['rims'],
        ];

        if (! empty($filters['query'])) {
            $params['query'] = $filters['query'];
        }

        if (! empty($filters['price_from'])) {
            $params['filter_float_price:from'] = $filters['price_from'];
        }

        if (! empty($filters['price_to'])) {
            $params['filter_float_price:to'] = $filters['price_to'];
        }

        if (! empty($filters['condition'])) {
            $conditionMap = ['new' => 'new', 'used' => 'used'];
            $params['filter_enum_state[]'] = $conditionMap[$filters['condition']] ?? $filters['condition'];
        }

        $sort = $filters['sort'] ?? '';
        if ($sort === 'price_asc') {
            $params['sort_by'] = 'filter_float_price:asc';
        } elseif ($sort === 'price_desc') {
            $params['sort_by'] = 'filter_float_price:desc';
        } elseif ($sort === 'newest') {
            $params['sort_by'] = 'created_at:desc';
        }

        $page = (int) ($filters['page'] ?? 1);
        if ($page > 1) {
            $params['offset'] = ($page - 1) * 50;
        }

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept' => 'application/json',
        ])->timeout(15)->get(self::API_URL, $params);

        if (! $response->successful()) {
            throw new \RuntimeException("API returned {$response->status()}");
        }

        $data = $response->json();
        $listings = [];

        foreach ($data['data'] ?? [] as $ad) {
            $listing = $this->mapApiListing($ad, $cacheKey);
            if ($listing) {
                $listings[] = $listing;
            }
        }

        return $listings;
    }

    private function mapApiListing(array $ad, string $cacheKey): ?array
    {
        $olxId = (string) ($ad['id'] ?? '');
        if (! $olxId) {
            return null;
        }

        $title = $ad['title'] ?? '';
        $description = $ad['description'] ?? '';
        $fullText = mb_strtolower($title.' '.$description);

        $price = null;
        $currency = 'RON';
        foreach ($ad['params'] ?? [] as $param) {
            if (($param['key'] ?? '') === 'price') {
                $priceData = $param['value'] ?? [];
                $price = isset($priceData['value']) ? (int) $priceData['value'] : null;
                $currency = $priceData['currency'] ?? 'RON';
                break;
            }
        }

        $location = '';
        $city = $ad['location']['city']['name'] ?? '';
        $region = $ad['location']['region']['name'] ?? '';
        if ($city) {
            $location = $region ? "$city, $region" : $city;
        }

        $imageUrl = null;
        if (! empty($ad['photos'])) {
            $link = $ad['photos'][0]['link'] ?? '';
            $imageUrl = str_replace('{width}x{height}', '400x300', $link);
        }

        $listingUrl = $ad['url'] ?? '';
        if ($listingUrl && ! str_starts_with($listingUrl, 'http')) {
            $listingUrl = self::BASE_URL.$listingUrl;
        }

        $hasDelivery = ! empty($ad['delivery']['rock']['activeForUser']);
        [$diameter, $boltPattern, $tyreWidth, $tyreProfile, $season] = $this->extractSpecs($fullText);

        $data = [
            'olx_id' => $olxId,
            'title' => $title,
            'price' => $price,
            'currency' => $currency,
            'location' => $location,
            'image_url' => $imageUrl,
            'listing_url' => $listingUrl,
            'has_delivery' => $hasDelivery,
            'diameter' => $diameter,
            'bolt_pattern' => $boltPattern,
            'description' => Str::limit($description, 500),
            'search_query' => $cacheKey,
        ];

        $this->cacheListing($data);

        return $data;
    }

    private function searchViaHtml(array $filters, string $cacheKey): array
    {
        $page = $filters['page'] ?? 1;
        $url = $this->buildSearchUrl($filters, $page);

        try {
            $html = $this->fetchPage($url);

            return $this->parseListings($html, $cacheKey);
        } catch (\Exception $e) {
            Log::error('OLX HTML scrape failed', ['url' => $url, 'error' => $e->getMessage()]);

            return [];
        }
    }

    private function buildSearchUrl(array $filters, int $page = 1): string
    {
        $category = $filters['category'] ?? 'rims';
        $path = self::CATEGORY_PATHS[$category] ?? self::CATEGORY_PATHS['rims'];
        $url = self::BASE_URL.$path;
        $params = [];

        if (! empty($filters['query'])) {
            $params['q'] = $filters['query'];
        }

        if (! empty($filters['price_from'])) {
            $params['search[filter_float_price:from]'] = $filters['price_from'];
        }

        if (! empty($filters['price_to'])) {
            $params['search[filter_float_price:to]'] = $filters['price_to'];
        }

        if (! empty($filters['condition'])) {
            $params['search[filter_enum_state][0]'] = $filters['condition'];
        }

        $sort = $filters['sort'] ?? '';
        if ($sort === 'price_asc') {
            $params['search[order]'] = 'filter_float_price:asc';
        } elseif ($sort === 'price_desc') {
            $params['search[order]'] = 'filter_float_price:desc';
        } elseif ($sort === 'newest') {
            $params['search[order]'] = 'created_at:desc';
        }

        if ($page > 1) {
            $params['page'] = $page;
        }

        $queryString = http_build_query($params);

        return $queryString ? $url.'?'.$queryString : $url;
    }

    private function fetchPage(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'ro-RO,ro;q=0.9,en;q=0.8',
            'Accept-Encoding' => 'gzip, deflate',
            'Cache-Control' => 'no-cache',
        ])
            ->timeout(15)
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} fetching {$url}");
        }

        return $response->body();
    }

    private function parseListings(string $html, string $cacheKey): array
    {
        $listings = [];

        if (preg_match('/window\.__PRELOADED_STATE__\s*=\s*"(.+?)"\s*;/s', $html, $match)) {
            $jsonStr = stripcslashes($match[1]);
            $data = json_decode($jsonStr, true);

            if (isset($data['listing']['listing']['ads'])) {
                foreach ($data['listing']['listing']['ads'] as $ad) {
                    $listing = $this->mapPreloadedListing($ad, $cacheKey);
                    if ($listing) {
                        $listings[] = $listing;
                    }
                }

                return $listings;
            }
        }

        $imageMap = $this->extractImageMapFromLdJson($html);
        $listings = $this->parseHtmlListings($html, $cacheKey, $imageMap);

        return $listings;
    }

    private function extractImageMapFromLdJson(string $html): array
    {
        $map = [];

        if (! preg_match_all('/<script[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/si', $html, $matches)) {
            return $map;
        }

        foreach ($matches[1] as $json) {
            $data = json_decode($json, true);
            if (! $data) {
                continue;
            }

            foreach ($data['offers']['offers'] ?? [] as $offer) {
                $url = $offer['url'] ?? '';
                $images = $offer['image'] ?? [];
                if ($url && ! empty($images)) {
                    $path = parse_url($url, PHP_URL_PATH);
                    $map[$path] = $images[0];
                }
            }
        }

        return $map;
    }

    private function mapPreloadedListing(array $ad, string $cacheKey): ?array
    {
        $olxId = (string) ($ad['id'] ?? '');
        if (! $olxId) {
            return null;
        }

        $title = $ad['title'] ?? '';
        $description = $ad['description'] ?? '';
        $fullText = mb_strtolower($title.' '.$description);

        $price = null;
        $currency = 'RON';
        if (isset($ad['price']['regularPrice']['value'])) {
            $price = (int) $ad['price']['regularPrice']['value'];
            $currency = $ad['price']['regularPrice']['currencyCode'] ?? 'RON';
        }

        $location = '';
        if (isset($ad['location']['cityName'])) {
            $location = $ad['location']['cityName'];
            if (isset($ad['location']['regionName'])) {
                $location .= ', '.$ad['location']['regionName'];
            }
        }

        $imageUrl = $ad['photos'][0]['link'] ?? ($ad['photos'][0]['uri'] ?? null);
        $listingUrl = $ad['url'] ?? '';
        if ($listingUrl && ! str_starts_with($listingUrl, 'http')) {
            $listingUrl = self::BASE_URL.$listingUrl;
        }

        $hasDelivery = ! empty($ad['delivery']['rock']['activeForUser']);
        [$diameter, $boltPattern, $tyreWidth, $tyreProfile, $season] = $this->extractSpecs($fullText);

        $data = [
            'olx_id' => $olxId,
            'title' => $title,
            'price' => $price,
            'currency' => $currency,
            'location' => $location,
            'image_url' => $imageUrl,
            'listing_url' => $listingUrl,
            'has_delivery' => $hasDelivery,
            'diameter' => $diameter,
            'bolt_pattern' => $boltPattern,
            'description' => Str::limit($description, 500),
            'search_query' => $cacheKey,
        ];

        $this->cacheListing($data);

        return $data;
    }

    private function parseHtmlListings(string $html, string $cacheKey, array $imageMap = []): array
    {
        $listings = [];

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument;
        $doc->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        $cards = $xpath->query('//*[@data-cy="l-card"]');

        foreach ($cards as $card) {
            $listing = $this->parseCardElement($card, $xpath, $doc, $cacheKey, $imageMap);
            if ($listing) {
                $listings[] = $listing;
            }
        }

        return $listings;
    }

    private function parseCardElement(\DOMElement $card, \DOMXPath $xpath, \DOMDocument $doc, string $cacheKey, array $imageMap = []): ?array
    {
        $olxId = $card->getAttribute('id');
        if (! $olxId) {
            return null;
        }

        $linkNodes = $xpath->query('.//a[contains(@href, "/d/")]', $card);
        if ($linkNodes->length === 0) {
            return null;
        }

        $href = $linkNodes->item(0)->getAttribute('href');
        if ($href && ! str_starts_with($href, 'http')) {
            $href = self::BASE_URL.preg_replace('/\?.*$/', '', $href);
        }

        $titleNodes = $xpath->query('.//h4', $card);
        $title = $titleNodes->length > 0 ? trim($titleNodes->item(0)->textContent) : '';

        $priceNodes = $xpath->query('.//*[@data-testid="ad-price"]', $card);
        $priceText = '';
        if ($priceNodes->length > 0) {
            $priceText = $this->cleanNodeText($doc, $priceNodes->item(0));
        }
        [$price, $currency] = $this->parsePrice($priceText);

        $imgNodes = $xpath->query('.//img', $card);
        $imageUrl = null;
        if ($imgNodes->length > 0) {
            $img = $imgNodes->item(0);
            $dataSrc = $img->getAttribute('data-src');
            $src = $img->getAttribute('src');
            $srcset = $img->getAttribute('srcset');

            foreach ([$dataSrc, $src] as $candidate) {
                if ($candidate && str_contains($candidate, 'olxcdn.com')) {
                    $imageUrl = $candidate;
                    break;
                }
            }
            if (! $imageUrl && $srcset) {
                $firstSrc = explode(' ', trim($srcset))[0] ?? null;
                if ($firstSrc && str_contains($firstSrc, 'olxcdn.com')) {
                    $imageUrl = $firstSrc;
                }
            }
        }

        if (! $imageUrl && ! empty($imageMap)) {
            $rawHref = $linkNodes->item(0)->getAttribute('href');
            $hrefPath = preg_replace('/\?.*$/', '', $rawHref);
            if (isset($imageMap[$hrefPath])) {
                $imageUrl = $imageMap[$hrefPath];
            }
        }

        $locationNodes = $xpath->query('.//*[@data-testid="location-date"]', $card);
        $location = '';
        if ($locationNodes->length > 0) {
            $location = trim($locationNodes->item(0)->textContent);
            $location = preg_replace('/\s*-\s*(Azi|Ieri|Reactualizat).*$/iu', '', $location);
        }

        $fullText = mb_strtolower($title);
        [$diameter, $boltPattern, $tyreWidth, $tyreProfile, $season] = $this->extractSpecs($fullText);

        $data = [
            'olx_id' => $olxId,
            'title' => $title,
            'price' => $price,
            'currency' => $currency,
            'location' => $location,
            'image_url' => $imageUrl,
            'listing_url' => $href,
            'has_delivery' => false,
            'diameter' => $diameter,
            'bolt_pattern' => $boltPattern,
            'description' => null,
            'search_query' => $cacheKey,
        ];

        $this->cacheListing($data);

        return $data;
    }

    private function extractSpecs(string $text): array
    {
        $diameter = null;
        if (preg_match(self::DIAMETER_PATTERN, $text, $m)) {
            $diameter = 'R'.$m[1];
        }

        $boltPattern = null;
        if (preg_match(self::BOLT_PATTERN, $text, $m)) {
            $boltPattern = $m[1].'x'.$m[2];
        }

        $tyreWidth = null;
        $tyreProfile = null;
        if (preg_match(self::TYRE_SIZE_PATTERN, $text, $m)) {
            $tyreWidth = $m[1];
            $tyreProfile = $m[2];
            if (! $diameter) {
                $diameter = 'R'.$m[3];
            }
        }

        $season = null;
        if (preg_match('/\b(iarna|winter|zima)\b/i', $text)) {
            $season = 'winter';
        } elseif (preg_match('/\b(vara|summer|lqto)\b/i', $text)) {
            $season = 'summer';
        } elseif (preg_match('/\b(all\s*season|toate\s*anotimpurile|4\s*sezoane|celogodishn)/i', $text)) {
            $season = 'all_season';
        }

        return [$diameter, $boltPattern, $tyreWidth, $tyreProfile, $season];
    }

    private function cacheListing(array $data): void
    {
        try {
            CachedListing::updateOrCreate(
                ['olx_id' => $data['olx_id']],
                $data,
            );
        } catch (\Exception $e) {
            Log::warning('Failed to cache listing', ['olx_id' => $data['olx_id'], 'error' => $e->getMessage()]);
        }
    }

    private function getCachedResults(string $cacheKey): ?array
    {
        $cutoff = now()->subMinutes(self::CACHE_TTL_MINUTES);

        $results = CachedListing::where('search_query', $cacheKey)
            ->where('updated_at', '>=', $cutoff)
            ->get();

        if ($results->isEmpty()) {
            return null;
        }

        return $results->toArray();
    }

    private function buildCacheKey(array $filters): string
    {
        $parts = array_filter([
            $filters['category'] ?? 'rims',
            $filters['query'] ?? '',
            $filters['price_from'] ?? '',
            $filters['price_to'] ?? '',
            $filters['diameter'] ?? '',
            $filters['condition'] ?? '',
        ]);

        return implode('|', $parts);
    }

    public function getListingDetails(string $olxId): ?array
    {
        $cached = CachedListing::where('olx_id', $olxId)->first();
        $url = $cached?->listing_url;

        if (! $url) {
            return null;
        }

        try {
            $html = $this->fetchPage($url);
        } catch (\Exception $e) {
            Log::error('Failed to fetch listing', ['url' => $url, 'error' => $e->getMessage()]);

            return $cached?->toArray();
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument;
        $doc->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);
        libxml_clear_errors();
        $xpath = new \DOMXPath($doc);

        $title = $this->xpathText($xpath, '//*[@data-cy="offer_title"]//h4');

        $priceContainer = $xpath->query('//*[@data-testid="ad-price-container"]');
        $priceText = '';
        if ($priceContainer->length > 0) {
            $priceText = $this->cleanNodeText($doc, $priceContainer->item(0));
        }
        [$price, $currency] = $this->parsePrice($priceText);
        $negotiable = str_contains(mb_strtolower($priceText), 'negociabil');

        $descNode = $xpath->query('//*[@data-cy="ad_description"]');
        $description = '';
        if ($descNode->length > 0) {
            $description = $this->cleanNodeText($doc, $descNode->item(0));
            $description = preg_replace('/^\s*Descriere\s*/iu', '', $description);
        }

        $postedAt = $this->xpathText($xpath, '//*[@data-cy="ad-posted-at"]');
        $seller = $this->xpathText($xpath, '//*[@data-testid="user-profile-user-name"]');

        $images = [];
        $slides = $xpath->query('//*[@data-cy="adPhotos-swiperSlide"]//img');
        foreach ($slides as $img) {
            $src = $img->getAttribute('src');
            if ($src && str_contains($src, 'olxcdn.com')) {
                $images[] = preg_replace('/;s=\d+x\d+/', ';s=1000x700', $src);
            }
        }

        $params = [];
        $paramRows = $xpath->query('//*[starts-with(@data-testid, "param-row-")]');
        foreach ($paramRows as $row) {
            $testId = $row->getAttribute('data-testid');
            $paramKey = str_replace('param-row-', '', $testId);
            $nameNode = $xpath->query('.//*[starts-with(@data-testid, "param-name-")]', $row);
            $valueNode = $xpath->query('.//*[starts-with(@data-testid, "param-value-")]', $row);
            if ($nameNode->length > 0 && $valueNode->length > 0) {
                $params[$paramKey] = [
                    'name' => trim($nameNode->item(0)->textContent),
                    'value' => $this->cleanNodeText($doc, $valueNode->item(0)),
                ];
            }
        }

        $location = '';
        $breadcrumbs = $xpath->query('//*[@data-testid="breadcrumb-item"]');
        if ($breadcrumbs->length >= 2) {
            $lastBreadcrumb = $this->cleanNodeText($doc, $breadcrumbs->item($breadcrumbs->length - 1));
            $lastBreadcrumb = preg_replace('/^.*?\s*-\s*/', '', $lastBreadcrumb);
            $location = $lastBreadcrumb;
        }

        $hasDelivery = $xpath->query('//*[@data-testid="courier-btn"]')->length > 0;

        $fullText = mb_strtolower($title.' '.$description);
        [$diameter, $boltPattern] = $this->extractSpecs($fullText);

        return [
            'olx_id' => $olxId,
            'title' => $title ?: ($cached?->title ?? ''),
            'price' => $price ?? $cached?->price,
            'currency' => $currency,
            'negotiable' => $negotiable,
            'description' => $description,
            'images' => $images,
            'location' => $location ?: ($cached?->location ?? ''),
            'posted_at' => $postedAt,
            'seller' => $seller,
            'params' => $params,
            'has_delivery' => $hasDelivery,
            'diameter' => $diameter ?? $cached?->diameter,
            'bolt_pattern' => $boltPattern ?? $cached?->bolt_pattern,
            'listing_url' => $url,
        ];
    }

    private function xpathText(\DOMXPath $xpath, string $query): string
    {
        $nodes = $xpath->query($query);

        return $nodes->length > 0 ? trim($nodes->item(0)->textContent) : '';
    }

    private function cleanNodeText(\DOMDocument $doc, \DOMElement $node): string
    {
        $clone = $node->cloneNode(true);
        $styles = $clone->getElementsByTagName('style');
        while ($styles->length > 0) {
            $styles->item(0)->parentNode->removeChild($styles->item(0));
        }

        return trim($clone->textContent);
    }

    private function parsePrice(string $text): array
    {
        $price = null;
        $currency = 'RON';

        if (preg_match('/([\d][\d\s.,]*\d)\s*(lei|ron|eur|€)/i', $text, $pm)) {
            $priceStr = trim($pm[1]);
            $priceStr = preg_replace('/,\d{1,2}$/', '', $priceStr);
            $price = (int) preg_replace('/\D/', '', $priceStr);
            $matched = mb_strtoupper($pm[2]);
            $currency = ($matched === 'LEI') ? 'RON' : (($matched === '€') ? 'EUR' : $matched);
        } elseif (preg_match('/(\d+)\s*(lei|ron|eur|€)/i', $text, $pm)) {
            $price = (int) $pm[1];
            $matched = mb_strtoupper($pm[2]);
            $currency = ($matched === 'LEI') ? 'RON' : (($matched === '€') ? 'EUR' : $matched);
        }

        return [$price, $currency];
    }
}
