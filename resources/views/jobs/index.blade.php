<x-app-layout>
    <x-slot:title>Задачи</x-slot:title>

    <x-page-header title="Задачи" action="Нова задача" :actionUrl="route('jobs.create')" />

    {{-- Filters --}}
    <form method="GET" class="mb-5 space-y-2">
        <div class="relative">
            <svg class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Търси задача или клиент..."
                   class="w-full rounded-xl border-gray-200 bg-white pl-10 pr-4 py-3 text-[15px] focus:border-blue-500 focus:ring-blue-500 shadow-sm">
        </div>
        {{-- Status filter pills --}}
        <div class="flex gap-1.5 overflow-x-auto pb-1 -mx-1 px-1 no-scrollbar">
            <a href="{{ route('jobs.index', request()->except('status')) }}"
               class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-medium border transition {{ !request('status') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 active:bg-gray-50' }}">
                Всички
            </a>
            @foreach($statuses as $key => $label)
                <a href="{{ route('jobs.index', array_merge(request()->except('status'), ['status' => $key])) }}"
                   class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-medium border transition {{ request('status') === $key ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 active:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </form>

    {{-- Job list --}}
    <div class="space-y-2">
        @forelse($jobs as $job)
            <a href="{{ route('jobs.show', $job) }}" class="block">
                <x-card class="p-4 active:bg-gray-50 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-[15px] text-gray-900 leading-snug truncate">{{ $job->title }}</p>
                            <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $job->client->name }}</p>
                        </div>
                        <x-status-badge :status="$job->status" />
                    </div>
                    <div class="flex items-center gap-3 mt-2.5 text-xs text-gray-400">
                        @if($job->scheduled_date)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                {{ $job->scheduled_date->format('d.m.Y') }}
                            </span>
                        @endif
                        @if($job->estimated_price)
                            <span>~{{ number_format($job->estimated_price, 0) }} лв.</span>
                        @endif
                    </div>
                </x-card>
            </a>
        @empty
            <x-card class="py-12 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.193-.14 1.743"/></svg>
                @if(request('search') || request('status'))
                    <p class="text-sm text-gray-500 mb-1">Няма задачи с тези филтри</p>
                    <a href="{{ route('jobs.index') }}" class="text-sm text-blue-600 font-medium">Изчисти филтрите</a>
                @else
                    <p class="text-sm text-gray-500 mb-3">Все още нямате задачи</p>
                    <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white active:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Създайте първата задача
                    </a>
                @endif
            </x-card>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</x-app-layout>
