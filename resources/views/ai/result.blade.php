<x-app-layout>
    <x-slot:title>AI Резултат</x-slot:title>

    <x-page-header title="AI Анализ" />

    {{-- Original prompt --}}
    <x-card class="p-4 mb-4 bg-gray-50">
        <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-1">Вашата заявка</h3>
        <p class="text-sm text-gray-700">{{ $query->prompt }}</p>
    </x-card>

    {{-- Summary --}}
    @if(!empty($result['summary']))
    <x-card class="p-4 mb-4">
        <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-2">Обобщение</h3>
        <p class="text-gray-900">{{ $result['summary'] }}</p>
    </x-card>
    @endif

    {{-- Location --}}
    @if(!empty($result['location']))
    <x-card class="p-4 mb-4">
        <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-2">Местоположение</h3>
        <p class="text-gray-900">{{ $result['location'] }}</p>
    </x-card>
    @endif

    {{-- Checklist --}}
    @if(!empty($result['checklist']))
    <x-card class="p-4 mb-4">
        <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-2">Списък със задачи</h3>
        <ul class="space-y-2">
            @foreach($result['checklist'] as $item)
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-medium shrink-0 mt-0.5">{{ $loop->iteration }}</span>
                    {{ $item }}
                </li>
            @endforeach
        </ul>
    </x-card>
    @endif

    {{-- Materials --}}
    @if(!empty($result['materials']))
    <x-card class="p-4 mb-4">
        <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-2">Материали</h3>
        <ul class="space-y-1">
            @foreach($result['materials'] as $material)
                <li class="text-sm text-gray-700 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    {{ $material }}
                </li>
            @endforeach
        </ul>
    </x-card>
    @endif

    {{-- Quotation --}}
    @if(!empty($result['quotation']))
    <x-card class="p-4 mb-6">
        <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-2">Оферта (черновa)</h3>
        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans">{{ $result['quotation'] }}</pre>
    </x-card>
    @endif

    <div class="flex gap-3">
        <a href="{{ route('ai.index') }}" class="flex-1 text-center rounded-lg bg-blue-600 py-3 text-sm font-medium text-white hover:bg-blue-700 transition">Нова заявка</a>
        <a href="{{ route('dashboard') }}" class="rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Табло</a>
    </div>
</x-app-layout>
