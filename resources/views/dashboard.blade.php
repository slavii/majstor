<x-app-layout>
    <x-slot:title>Табло</x-slot:title>

    {{-- Greeting --}}
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ now()->translatedFormat('l, j F') }}</p>
    </div>

    {{-- Quick add buttons --}}
    <div class="grid grid-cols-2 gap-2 mb-5">
        <a href="{{ route('jobs.create') }}" class="flex items-center gap-2.5 rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-medium text-white active:bg-blue-700 transition">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Нова задача
        </a>
        <a href="{{ route('clients.create') }}" class="flex items-center gap-2.5 rounded-xl bg-white border border-gray-200 px-4 py-3.5 text-sm font-medium text-gray-700 active:bg-gray-50 transition">
            <svg class="w-5 h-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
            Нов клиент
        </a>
    </div>

    {{-- Overdue alert --}}
    @if($overdueJobs->count())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 mb-5">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <p class="text-sm font-semibold text-red-800">{{ $overdueJobs->count() }} {{ $overdueJobs->count() === 1 ? 'просрочена задача' : 'просрочени задачи' }}</p>
            </div>
            @foreach($overdueJobs->take(3) as $job)
                <a href="{{ route('jobs.show', $job) }}" class="flex items-center justify-between py-1.5 text-sm text-red-700 hover:text-red-900">
                    <span class="truncate">{{ $job->title }} — {{ $job->client->name }}</span>
                    <span class="text-xs text-red-500 shrink-0 ml-2">{{ $job->scheduled_date->format('d.m') }}</span>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-2 mb-6">
        <x-card class="px-3 py-3.5 text-center">
            <p class="text-2xl font-bold text-blue-600 leading-none">{{ $unfinishedCount }}</p>
            <p class="text-[11px] text-gray-500 mt-1.5 leading-tight">Активни</p>
        </x-card>
        <x-card class="px-3 py-3.5 text-center">
            <p class="text-2xl font-bold text-green-600 leading-none">{{ $completedJobs }}</p>
            <p class="text-[11px] text-gray-500 mt-1.5 leading-tight">Завършени</p>
        </x-card>
        <x-card class="px-3 py-3.5 text-center">
            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $totalClients }}</p>
            <p class="text-[11px] text-gray-500 mt-1.5 leading-tight">Клиенти</p>
        </x-card>
    </div>

    {{-- Today's jobs --}}
    <section class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-900">Днес</h2>
            @if($todayJobs->count())
                <span class="text-xs bg-blue-100 text-blue-700 font-medium px-2 py-0.5 rounded-full">{{ $todayJobs->count() }}</span>
            @endif
        </div>
        @forelse($todayJobs as $job)
            <a href="{{ route('jobs.show', $job) }}" class="block mb-2">
                <x-card class="p-3.5 active:bg-gray-50 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-[15px] text-gray-900 leading-snug">{{ $job->title }}</p>
                            <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $job->client->name }}@if($job->client->phone) · {{ $job->client->phone }}@endif</p>
                        </div>
                        <x-status-badge :status="$job->status" />
                    </div>
                    @if($job->estimated_price)
                        <p class="text-xs text-gray-400 mt-2">~{{ number_format($job->estimated_price, 0) }} лв.</p>
                    @endif
                </x-card>
            </a>
        @empty
            <x-card class="p-6 text-center">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <p class="text-sm text-gray-400">Няма задачи за днес</p>
            </x-card>
        @endforelse
    </section>

    {{-- Upcoming --}}
    @if($upcomingJobs->count())
    <section class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-900">Предстоящи</h2>
            <a href="{{ route('jobs.index') }}" class="text-xs text-blue-600 font-medium">Виж всички</a>
        </div>
        @foreach($upcomingJobs as $job)
            <a href="{{ route('jobs.show', $job) }}" class="block mb-2">
                <x-card class="p-3.5 active:bg-gray-50 transition">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-sm text-gray-900 truncate">{{ $job->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $job->client->name }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-medium text-gray-700">{{ $job->scheduled_date->format('d.m') }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $job->scheduled_date->translatedFormat('D') }}</p>
                        </div>
                    </div>
                </x-card>
            </a>
        @endforeach
    </section>
    @endif

    {{-- Recent activity --}}
    @if(count($recentActivity))
    <section class="mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Последна активност</h2>
        <x-card class="divide-y divide-gray-100">
            @foreach($recentActivity as $activity)
                <a href="{{ $activity['url'] }}" class="flex items-start gap-3 p-3.5 active:bg-gray-50 transition">
                    @if($activity['type'] === 'status')
                        <span class="mt-1 w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                    @else
                        <span class="mt-1 w-2 h-2 rounded-full bg-gray-300 shrink-0"></span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 truncate">{{ $activity['text'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $activity['detail'] }}</p>
                    </div>
                    <span class="text-[11px] text-gray-400 shrink-0 mt-0.5">{{ $activity['time']->diffForHumans(short: true) }}</span>
                </a>
            @endforeach
        </x-card>
    </section>
    @endif

    {{-- Recent clients --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-900">Последни клиенти</h2>
            <a href="{{ route('clients.index') }}" class="text-xs text-blue-600 font-medium">Всички</a>
        </div>
        @forelse($recentClients as $client)
            <a href="{{ route('clients.show', $client) }}" class="block mb-2">
                <x-card class="p-3.5 active:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-gray-900 truncate">{{ $client->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $client->phone ?: $client->address ?: 'Без контакт' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </x-card>
            </a>
        @empty
            <x-card class="p-8 text-center">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                <p class="text-sm text-gray-400 mb-3">Все още нямате клиенти</p>
                <a href="{{ route('clients.create') }}" class="text-sm text-blue-600 font-medium">+ Добавете първия</a>
            </x-card>
        @endforelse
    </section>
</x-app-layout>
