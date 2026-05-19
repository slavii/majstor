<x-app-layout>
    <x-slot:title>Задачи</x-slot:title>

    <x-page-header title="Задачи" action="Нова задача" :actionUrl="route('jobs.create')" />

    {{-- Filters --}}
    <form method="GET" class="flex flex-col md:flex-row gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Търсене..."
               class="flex-1 rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
        <select name="status" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Всички статуси</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    {{-- Job list --}}
    <div class="space-y-2">
        @forelse($jobs as $job)
            <x-card>
                <a href="{{ route('jobs.show', $job) }}" class="block p-4">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 truncate">{{ $job->title }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $job->client->name }}</p>
                        </div>
                        <x-status-badge :status="$job->status" />
                    </div>
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                        @if($job->scheduled_date)
                            <span>{{ $job->scheduled_date->format('d.m.Y') }}</span>
                        @endif
                        @if($job->estimated_price)
                            <span>~{{ number_format($job->estimated_price, 0) }} лв.</span>
                        @endif
                    </div>
                </a>
            </x-card>
        @empty
            <x-card class="p-8 text-center">
                <p class="text-gray-400 mb-4">Няма задачи.</p>
                <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    + Създайте първата задача
                </a>
            </x-card>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</x-app-layout>
