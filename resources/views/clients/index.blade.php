<x-app-layout>
    <x-slot:title>Клиенти</x-slot:title>

    <x-page-header title="Клиенти" action="Нов клиент" :actionUrl="route('clients.create')" />

    {{-- Search --}}
    <form method="GET" class="mb-5">
        <div class="relative">
            <svg class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Търси по име, телефон, имейл..."
                   class="w-full rounded-xl border-gray-200 bg-white pl-10 pr-4 py-3 text-[15px] focus:border-blue-500 focus:ring-blue-500 shadow-sm">
        </div>
    </form>

    {{-- Client list --}}
    <div class="space-y-2">
        @forelse($clients as $client)
            <a href="{{ route('clients.show', $client) }}" class="block">
                <x-card class="p-4 active:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-[15px] text-gray-900 truncate">{{ $client->name }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                @if($client->phone)
                                    <span class="text-sm text-gray-500">{{ $client->phone }}</span>
                                @endif
                                @if($client->address)
                                    <span class="text-xs text-gray-400 truncate">{{ Str::limit($client->address, 25) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            @if($client->phone)
                                <span onclick="event.preventDefault(); event.stopPropagation(); window.location.href='tel:{{ $client->phone }}'"
                                      class="p-2 rounded-full text-blue-500 hover:bg-blue-50 active:bg-blue-100">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                </span>
                            @endif
                            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </div>
                    </div>
                </x-card>
            </a>
        @empty
            <x-card class="py-12 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                @if(request('search'))
                    <p class="text-sm text-gray-500 mb-1">Няма резултати за "{{ request('search') }}"</p>
                    <a href="{{ route('clients.index') }}" class="text-sm text-blue-600 font-medium">Изчисти търсенето</a>
                @else
                    <p class="text-sm text-gray-500 mb-3">Все още нямате клиенти</p>
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white active:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Добавете първия клиент
                    </a>
                @endif
            </x-card>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</x-app-layout>
