<x-app-layout>
    <x-slot:title>Табло</x-slot:title>

    <x-page-header title="Добре дошли, {{ Auth::user()->name }}!" />

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <x-card class="p-4">
            <p class="text-2xl font-bold text-blue-600">{{ $unfinishedCount }}</p>
            <p class="text-sm text-gray-500 mt-1">Активни задачи</p>
        </x-card>
        <x-card class="p-4">
            <p class="text-2xl font-bold text-green-600">{{ $completedJobs }}</p>
            <p class="text-sm text-gray-500 mt-1">Завършени</p>
        </x-card>
        <x-card class="p-4">
            <p class="text-2xl font-bold text-gray-900">{{ $totalClients }}</p>
            <p class="text-sm text-gray-500 mt-1">Клиенти</p>
        </x-card>
        <x-card class="p-4">
            <p class="text-2xl font-bold text-gray-900">{{ $totalJobs }}</p>
            <p class="text-sm text-gray-500 mt-1">Общо задачи</p>
        </x-card>
    </div>

    {{-- Today's jobs --}}
    <section class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Днешни задачи</h2>
        @forelse($todayJobs as $job)
            <x-card class="p-4 mb-2">
                <div class="flex items-start justify-between">
                    <div>
                        <a href="{{ route('jobs.show', $job) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $job->title }}</a>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $job->client->name }} {{ $job->client->phone ? '· ' . $job->client->phone : '' }}</p>
                    </div>
                    <x-status-badge :status="$job->status" />
                </div>
            </x-card>
        @empty
            <x-card class="p-6 text-center text-gray-400">
                Няма задачи за днес.
            </x-card>
        @endforelse
    </section>

    {{-- Upcoming jobs --}}
    <section class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Предстоящи</h2>
        @forelse($upcomingJobs as $job)
            <x-card class="p-4 mb-2">
                <div class="flex items-start justify-between">
                    <div>
                        <a href="{{ route('jobs.show', $job) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $job->title }}</a>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $job->client->name }} · {{ $job->scheduled_date?->format('d.m.Y') }}</p>
                    </div>
                    <x-status-badge :status="$job->status" />
                </div>
            </x-card>
        @empty
            <x-card class="p-6 text-center text-gray-400">
                Няма предстоящи задачи.
            </x-card>
        @endforelse
    </section>

    {{-- Recent clients --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Последни клиенти</h2>
            <a href="{{ route('clients.create') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ Нов клиент</a>
        </div>
        @forelse($recentClients as $client)
            <x-card class="p-4 mb-2">
                <a href="{{ route('clients.show', $client) }}" class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $client->name }}</p>
                        <p class="text-sm text-gray-500">{{ $client->phone }}</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </x-card>
        @empty
            <x-card class="p-6 text-center text-gray-400">
                Все още нямате клиенти. <a href="{{ route('clients.create') }}" class="text-blue-600 hover:underline">Добавете първия</a>.
            </x-card>
        @endforelse
    </section>
</x-app-layout>
