@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Back button --}}
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('search') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors mb-5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M15 19l-7-7 7-7" />
        </svg>
        {{ __('messages.back_to_results') }}
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="lg:flex">

            {{-- Image gallery --}}
            <div class="lg:w-3/5 bg-gray-100">
                @if(count($listing['images'] ?? []) > 0)
                    <div x-data="{ active: 0, images: {{ json_encode($listing['images']) }} }" class="relative">
                        {{-- Main image --}}
                        <div class="aspect-[4/3] overflow-hidden bg-gray-900 flex items-center justify-center">
                            <img :src="images[active]"
                                 alt="{{ $listing['title'] }}"
                                 class="max-w-full max-h-full object-contain">
                        </div>

                        {{-- Nav arrows --}}
                        <template x-if="images.length > 1">
                            <div>
                                <button @click="active = active > 0 ? active - 1 : images.length - 1"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button @click="active = active < images.length - 1 ? active + 1 : 0"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/50 text-white text-xs px-3 py-1 rounded-full">
                                    <span x-text="active + 1"></span> / <span x-text="images.length"></span>
                                </div>
                            </div>
                        </template>

                        {{-- Thumbnails --}}
                        <template x-if="images.length > 1">
                            <div class="flex gap-1.5 p-3 overflow-x-auto">
                                <template x-for="(img, i) in images" :key="i">
                                    <button @click="active = i"
                                            :class="active === i ? 'ring-2 ring-blue-500 opacity-100' : 'opacity-60 hover:opacity-100'"
                                            class="w-16 h-16 rounded-lg overflow-hidden shrink-0 transition">
                                        <img :src="img.replace(';s=1000x700', ';s=100x100')" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                @else
                    <div class="aspect-[4/3] flex items-center justify-center text-gray-300">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="m21 15-5-5L5 21" />
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Details sidebar --}}
            <div class="lg:w-2/5 p-6 lg:p-8 flex flex-col">
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 leading-tight mb-1">
                    {{ $listing['title_translated'] ?? $listing['title'] }}
                </h1>
                @if(!empty($listing['title_translated']) && $listing['title_translated'] !== $listing['title'])
                    <p class="text-sm text-gray-400 mb-3">{{ $listing['title'] }}</p>
                @else
                    <div class="mb-3"></div>
                @endif

                {{-- Price --}}
                <div class="mb-5 pb-5 border-b border-gray-100">
                    @if($listing['price'])
                        @if(($listing['currency'] ?? 'RON') === 'EUR')
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($listing['price'], 0, ',', '.') }} EUR
                            </div>
                        @else
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($listing['price'] * $ronToEur, 0, ',', '.') }} EUR
                            </div>
                            <div class="text-sm text-gray-500 mt-0.5">
                                {{ number_format($listing['price'], 0, ',', '.') }} RON
                            </div>
                        @endif
                        @if(!empty($listing['negotiable']))
                            <div class="text-sm text-amber-600 font-medium mt-0.5">{{ __('messages.negotiable') }}</div>
                        @endif
                    @else
                        <div class="text-xl text-gray-500 italic">{{ __('messages.negotiable') }}</div>
                    @endif
                </div>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-5">
                    @if(!empty($listing['diameter']))
                        <span class="bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg">{{ $listing['diameter'] }}</span>
                    @endif
                    @if(!empty($listing['bolt_pattern']))
                        <span class="bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg">{{ $listing['bolt_pattern'] }}</span>
                    @endif
                </div>

                {{-- Parameters --}}
                @if(!empty($listing['params']))
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.specifications') }}</h3>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            @foreach($listing['params'] as $key => $param)
                                <dt class="text-sm text-gray-500">{{ $param['name'] }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $param['value'] }}</dd>
                            @endforeach
                        </dl>
                    </div>
                @endif

                {{-- Location & Date --}}
                <div class="mb-5 pb-5 border-b border-gray-100">
                    @if(!empty($listing['location']))
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            {{ $listing['location'] }}
                        </div>
                    @endif
                    @if(!empty($listing['posted_at']))
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" />
                            </svg>
                            {{ $listing['posted_at'] }}
                        </div>
                    @endif
                </div>

                {{-- Seller --}}
                @if(!empty($listing['seller']))
                    <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ mb_strtoupper(mb_substr($listing['seller'], 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $listing['seller'] }}</p>
                            <p class="text-xs text-gray-500">{{ __('messages.seller') }}</p>
                        </div>
                    </div>
                @endif

                {{-- OLX link --}}
                <a href="{{ $listing['listing_url'] }}" target="_blank" rel="noopener"
                   class="mt-auto w-full flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" /><line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                    {{ __('messages.view_on_olx') }}
                </a>
            </div>
        </div>

        {{-- Description --}}
        @if(!empty($listing['description']))
            <div class="border-t border-gray-200 p-6 lg:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">{{ __('messages.description') }}</h2>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line text-sm">{{ $listing['description_translated'] ?? $listing['description'] }}</div>
                @if(!empty($listing['description_translated']) && $listing['description_translated'] !== $listing['description'])
                    <details class="mt-4">
                        <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600 transition-colors">
                            {{ __('messages.original_description') }}
                        </summary>
                        <div class="mt-2 text-gray-400 leading-relaxed whitespace-pre-line text-sm border-l-2 border-gray-200 pl-4">{{ $listing['description'] }}</div>
                    </details>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
