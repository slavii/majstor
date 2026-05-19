@props(['title', 'action' => null, 'actionUrl' => null])

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
    @if($action && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ $action }}
        </a>
    @endif
</div>
