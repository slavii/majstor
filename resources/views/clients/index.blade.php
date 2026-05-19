<x-app-layout>
    <x-slot:title>Клиенти</x-slot:title>

    <x-page-header title="Клиенти" action="Нов клиент" :actionUrl="route('clients.create')" />

    {{-- Search --}}
    <form method="GET" class="mb-4">
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Търсене по име, телефон, имейл..."
                   class="w-full rounded-lg border-gray-300 pl-10 pr-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </div>
    </form>

    {{-- Client list --}}
    <div class="space-y-2">
        @forelse($clients as $client)
            <x-card>
                <a href="{{ route('clients.show', $client) }}" class="flex items-center justify-between p-4">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $client->name }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $client->phone ?: 'Без телефон' }}
                            @if($client->address) · {{ Str::limit($client->address, 30) }} @endif
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 shrink-0 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </x-card>
        @empty
            <x-card class="p-8 text-center">
                <p class="text-gray-400 mb-4">Няма намерени клиенти.</p>
                <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    + Добавете първия клиент
                </a>
            </x-card>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</x-app-layout>
