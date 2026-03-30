@extends('layouts.app')

@php
    $currentCategory = $filters['category'] ?? 'rims';
    $isTyreCategory = in_array($currentCategory, ['tyres', 'all']);
@endphp

@section('content')
<div class="bg-gradient-to-b from-blue-600 to-indigo-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28">
        <h1 class="text-2xl sm:text-3xl font-bold mb-1">{{ __('messages.tagline') }}</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20">
    <form action="{{ route('search') }}" method="GET" id="searchForm">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-8">

            {{-- Category tabs --}}
            <div class="flex border-b border-gray-200">
                @foreach(['rims' => 'cat_rims', 'tyres' => 'cat_tyres', 'all' => 'cat_all'] as $catKey => $catLabel)
                    <button type="button"
                            onclick="document.getElementById('category').value='{{ $catKey }}'; document.getElementById('searchForm').submit();"
                            class="flex-1 py-3.5 text-center text-sm font-semibold transition-colors relative
                                {{ $currentCategory === $catKey
                                    ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600'
                                    : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        @if($catKey === 'rims')
                            <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="4" />
                                <line x1="12" y1="2" x2="12" y2="5" /><line x1="12" y1="19" x2="12" y2="22" />
                                <line x1="2" y1="12" x2="5" y2="12" /><line x1="19" y1="12" x2="22" y2="12" />
                            </svg>
                        @elseif($catKey === 'tyres')
                            <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="6" />
                                <path d="M12 2a10 10 0 0 1 0 20" stroke-dasharray="3 3" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="4" />
                                <circle cx="12" cy="12" r="7" stroke-dasharray="2 2" />
                            </svg>
                        @endif
                        {{ __("messages.$catLabel") }}
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="category" id="category" value="{{ $currentCategory }}">

            <div class="p-5">
                {{-- Search input + condition --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 mb-3">
                    <div class="lg:col-span-7">
                        <input type="text"
                               name="query"
                               value="{{ $filters['query'] ?? '' }}"
                               placeholder="{{ __('messages.search_placeholder') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div class="lg:col-span-3">
                        <select name="condition"
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white text-sm">
                            <option value="">{{ __('messages.condition') }}: {{ __('messages.condition_all') }}</option>
                            <option value="new" {{ ($filters['condition'] ?? '') === 'new' ? 'selected' : '' }}>{{ __('messages.condition_new') }}</option>
                            <option value="used" {{ ($filters['condition'] ?? '') === 'used' ? 'selected' : '' }}>{{ __('messages.condition_used') }}</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <select name="sort"
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white text-sm">
                            <option value="">{{ __('messages.sort_default') }}</option>
                            <option value="newest" {{ ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' }}>{{ __('messages.sort_newest') }}</option>
                            <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>{{ __('messages.sort_price_asc') }}</option>
                            <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>{{ __('messages.sort_price_desc') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Filter row 1: Price + Diameter + Bolt --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.price_from') }} (EUR)</label>
                        <input type="number" name="price_from" value="{{ $filters['price_from'] ?? '' }}" min="0" placeholder="0" step="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.price_to') }} (EUR)</label>
                        <input type="number" name="price_to" value="{{ $filters['price_to'] ?? '' }}" min="0" placeholder="2000" step="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.diameter') }}</label>
                        <select name="diameter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                            <option value="">{{ __('messages.all_diameters') }}</option>
                            @foreach($diameters as $d)
                                <option value="{{ $d }}" {{ ($filters['diameter'] ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.bolt_pattern') }}</label>
                        <select name="bolt_pattern" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                            <option value="">{{ __('messages.all_bolt_patterns') }}</option>
                            @foreach($boltPatterns as $bp)
                                <option value="{{ $bp }}" {{ ($filters['bolt_pattern'] ?? '') === $bp ? 'selected' : '' }}>{{ $bp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.width') }}</label>
                        <select name="width" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                            <option value="">{{ __('messages.all_widths') }}</option>
                            @foreach($widths as $w)
                                <option value="{{ $w }}" {{ ($filters['width'] ?? '') === $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.profile') }}</label>
                        <select name="profile" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                            <option value="">{{ __('messages.all_profiles') }}</option>
                            @foreach($profiles as $p)
                                <option value="{{ $p }}" {{ ($filters['profile'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Action row --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-1">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('search') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                            {{ __('messages.reset_filters') }}
                        </a>
                    </div>

                    <button type="submit"
                            class="w-full sm:w-auto px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" />
                            </svg>
                            {{ __('messages.search') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Results --}}
    @if($searched)
        @if(count($listings) > 0)
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-600 font-medium">
                    {{ __('messages.found_listings', ['count' => count($listings)]) }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($listings as $listing)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow group">
                        <a href="{{ route('listing.show', $listing['olx_id']) }}" class="block">
                            <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                                @if(!empty($listing['image_url']))
                                    <img src="{{ $listing['image_url'] }}"
                                         alt="{{ $listing['title'] }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="3" width="18" height="18" rx="2" />
                                            <circle cx="8.5" cy="8.5" r="1.5" />
                                            <path d="m21 15-5-5L5 21" />
                                        </svg>
                                    </div>
                                @endif

                                @if(!empty($listing['diameter']))
                                    <span class="absolute top-2 left-2 bg-gray-900/75 text-white text-xs font-medium px-2 py-1 rounded-md">
                                        {{ $listing['diameter'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2 line-clamp-2">
                                    {{ $listing['title'] }}
                                </h3>

                                <div class="flex items-baseline justify-between gap-2 mb-2">
                                    <div>
                                        @if($listing['price'])
                                            @if(($listing['currency'] ?? 'RON') === 'EUR')
                                                <span class="text-lg font-bold text-blue-600">
                                                    {{ number_format($listing['price'], 0, ',', '.') }} EUR
                                                </span>
                                            @else
                                                <span class="text-lg font-bold text-blue-600">
                                                    {{ number_format($listing['price'] * $ronToEur, 0, ',', '.') }} EUR
                                                </span>
                                                <span class="block text-xs text-gray-500">
                                                    {{ number_format($listing['price'], 0, ',', '.') }} RON
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-500 italic">{{ __('messages.negotiable') }}</span>
                                        @endif
                                    </div>

                                    @if(!empty($listing['bolt_pattern']))
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-medium">
                                            {{ $listing['bolt_pattern'] }}
                                        </span>
                                    @endif
                                </div>

                                @if(!empty($listing['location']))
                                    <p class="text-xs text-gray-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        {{ $listing['location'] }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" />
                </svg>
                <p class="text-gray-500 text-lg">{{ __('messages.no_results') }}</p>
            </div>
        @endif
    @else
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="4" />
                    <line x1="12" y1="2" x2="12" y2="5" /><line x1="12" y1="19" x2="12" y2="22" />
                    <line x1="2" y1="12" x2="5" y2="12" /><line x1="19" y1="12" x2="22" y2="12" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('messages.start_searching') }}</h2>
            <p class="text-gray-500 max-w-md mx-auto">{{ __('messages.start_description') }}</p>
        </div>
    @endif
</div>
@endsection
