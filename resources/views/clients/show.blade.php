<x-app-layout>
    <x-slot:title>{{ $client->name }}</x-slot:title>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('clients.edit', $client) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Редакция</a>
            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Сигурни ли сте?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Изтрий</button>
            </form>
        </div>
    </div>

    {{-- Client info --}}
    <x-card class="p-4 md:p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($client->phone)
            <div>
                <dt class="text-xs text-gray-500 uppercase tracking-wider">Телефон</dt>
                <dd class="mt-1">
                    <a href="tel:{{ $client->phone }}" class="text-blue-600 font-medium text-lg">{{ $client->phone }}</a>
                </dd>
            </div>
            @endif
            @if($client->email)
            <div>
                <dt class="text-xs text-gray-500 uppercase tracking-wider">Имейл</dt>
                <dd class="mt-1"><a href="mailto:{{ $client->email }}" class="text-blue-600">{{ $client->email }}</a></dd>
            </div>
            @endif
            @if($client->address)
            <div class="md:col-span-2">
                <dt class="text-xs text-gray-500 uppercase tracking-wider">Адрес</dt>
                <dd class="mt-1 text-gray-900">{{ $client->address }}</dd>
            </div>
            @endif
            @if($client->notes)
            <div class="md:col-span-2">
                <dt class="text-xs text-gray-500 uppercase tracking-wider">Бележки</dt>
                <dd class="mt-1 text-gray-700 whitespace-pre-line">{{ $client->notes }}</dd>
            </div>
            @endif
        </dl>
    </x-card>

    {{-- Quick actions --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('jobs.create', ['client_id' => $client->id]) }}" class="flex-1 text-center rounded-lg bg-blue-600 py-3 text-sm font-medium text-white hover:bg-blue-700 transition">
            + Нова задача
        </a>
        @if($client->phone)
        <a href="tel:{{ $client->phone }}" class="rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Обади се
        </a>
        @endif
    </div>

    {{-- Job history --}}
    <h2 class="text-lg font-semibold text-gray-900 mb-3">Задачи</h2>
    <div class="space-y-2">
        @forelse($client->jobs as $job)
            <x-card class="p-4">
                <a href="{{ route('jobs.show', $job) }}" class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $job->title }}</p>
                        <p class="text-sm text-gray-500">{{ $job->scheduled_date?->format('d.m.Y') }}</p>
                    </div>
                    <x-status-badge :status="$job->status" />
                </a>
            </x-card>
        @empty
            <p class="text-sm text-gray-400 py-4 text-center">Няма задачи за този клиент.</p>
        @endforelse
    </div>
</x-app-layout>
