<?php

namespace App\Http\Controllers;

use App\Services\ExchangeRateService;
use App\Services\OlxScraper;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly OlxScraper $scraper,
        private readonly TranslationService $translator,
        private readonly ExchangeRateService $exchangeRate,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'query' => 'nullable|string|max:200',
            'category' => 'nullable|string|in:rims,tyres,all',
            'condition' => 'nullable|string|in:new,used',
            'price_from' => 'nullable|integer|min:0',
            'price_to' => 'nullable|integer|min:0',
            'diameter' => 'nullable|string|max:10',
            'bolt_pattern' => 'nullable|string|max:20',
            'width' => 'nullable|string|max:10',
            'profile' => 'nullable|string|max:10',
            'season' => 'nullable|string|in:summer,winter,all_season',
            'sort' => 'nullable|string|in:newest,price_asc,price_desc',
            'delivery_only' => 'nullable|boolean',
            'page' => 'nullable|integer|min:1',
        ]);

        $listings = [];
        $searched = $request->hasAny(['query', 'category', 'price_from', 'price_to', 'diameter', 'bolt_pattern', 'width', 'condition']);

        $ronToEur = $this->exchangeRate->getRonToEur();

        if ($searched) {
            $searchQuery = $this->buildSearchQuery($filters);
            $scraperFilters = array_merge($filters, ['query' => $searchQuery]);

            if (! empty($scraperFilters['price_from']) && $ronToEur > 0) {
                $scraperFilters['price_from'] = (int) round($scraperFilters['price_from'] / $ronToEur);
            }
            if (! empty($scraperFilters['price_to']) && $ronToEur > 0) {
                $scraperFilters['price_to'] = (int) round($scraperFilters['price_to'] / $ronToEur);
            }

            $listings = $this->scraper->search($scraperFilters);
            $listings = $this->applyLocalFilters($listings, $filters);
            $listings = $this->applySorting($listings, $filters['sort'] ?? '');
        }

        return view('search.index', [
            'listings' => $listings,
            'filters' => $filters,
            'searched' => $searched,
            'ronToEur' => $ronToEur,
            'diameters' => ['R13', 'R14', 'R15', 'R16', 'R17', 'R18', 'R19', 'R20', 'R21', 'R22'],
            'boltPatterns' => [
                '3x98', '3x100', '3x112',
                '4x98', '4x100', '4x108', '4x114.3',
                '5x98', '5x100', '5x105', '5x108', '5x110', '5x112', '5x114.3', '5x115', '5x118', '5x120', '5x120.65', '5x127', '5x130', '5x139.7',
                '6x114.3', '6x127', '6x130', '6x139.7',
                '8x165.1', '8x170', '8x180',
            ],
            'widths' => ['155', '165', '175', '185', '195', '205', '215', '225', '235', '245', '255', '265', '275', '285', '295', '305'],
            'profiles' => ['30', '35', '40', '45', '50', '55', '60', '65', '70'],
        ]);
    }

    private function applyLocalFilters(array $listings, array $filters): array
    {
        if (! empty($filters['diameter'])) {
            $diameter = strtoupper($filters['diameter']);
            $listings = array_filter($listings, function ($l) use ($diameter) {
                if (! empty($l['diameter']) && strtoupper($l['diameter']) === $diameter) {
                    return true;
                }

                return str_contains(mb_strtoupper($l['title'] ?? ''), $diameter);
            });
        }

        if (! empty($filters['bolt_pattern'])) {
            $bolt = $filters['bolt_pattern'];
            $listings = array_filter($listings, function ($l) use ($bolt) {
                if (! empty($l['bolt_pattern']) && $l['bolt_pattern'] === $bolt) {
                    return true;
                }

                return str_contains($l['title'] ?? '', $bolt);
            });
        }

        if (! empty($filters['delivery_only'])) {
            $listings = array_filter($listings, fn ($l) => ! empty($l['has_delivery']));
        }

        return array_values($listings);
    }

    public function show(string $olxId)
    {
        $listing = $this->scraper->getListingDetails($olxId);

        if (! $listing) {
            abort(404);
        }

        $locale = app()->getLocale();
        $listing = $this->translator->translateListing($listing, $locale);

        return view('search.show', [
            'listing' => $listing,
            'ronToEur' => $this->exchangeRate->getRonToEur(),
        ]);
    }

    private function applySorting(array $listings, string $sort): array
    {
        if (! $sort) {
            return $listings;
        }

        $ronToEur = $this->exchangeRate->getRonToEur();

        usort($listings, function ($a, $b) use ($sort, $ronToEur) {
            $priceA = ($a['currency'] ?? 'RON') === 'EUR' ? ($a['price'] ?? 0) : ($a['price'] ?? 0) * $ronToEur;
            $priceB = ($b['currency'] ?? 'RON') === 'EUR' ? ($b['price'] ?? 0) : ($b['price'] ?? 0) * $ronToEur;

            return match ($sort) {
                'price_asc' => $priceA <=> $priceB,
                'price_desc' => $priceB <=> $priceA,
                'newest' => ($b['olx_id'] ?? 0) <=> ($a['olx_id'] ?? 0),
                default => 0,
            };
        });

        return $listings;
    }

    private function buildSearchQuery(array $filters): string
    {
        $parts = [];

        if (! empty($filters['query'])) {
            $parts[] = $filters['query'];
        }

        if (! empty($filters['diameter'])) {
            $parts[] = $filters['diameter'];
        }

        if (! empty($filters['bolt_pattern'])) {
            $parts[] = $filters['bolt_pattern'];
        }

        if (! empty($filters['width']) && ! empty($filters['profile']) && ! empty($filters['diameter'])) {
            $parts = array_filter($parts, fn ($p) => $p !== ($filters['diameter'] ?? ''));
            $parts[] = $filters['width'].'/'.$filters['profile'].' '.$filters['diameter'];
        } elseif (! empty($filters['width'])) {
            $parts[] = $filters['width'];
        }

        $category = $filters['category'] ?? 'rims';
        if (empty($parts)) {
            return match ($category) {
                'tyres' => 'anvelope',
                'all' => 'jante anvelope',
                default => 'jante',
            };
        }

        return implode(' ', $parts);
    }
}
