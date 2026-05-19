<x-app-layout>
    <x-slot:title>{{ $client->name }}</x-slot:title>

    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-bold text-gray-900">{{ $client->name }}</h1>
        <a href="{{ route('clients.edit', $client) }}" class="rounded-xl border border-gray-200 px-3.5 py-2 text-sm font-medium text-gray-600 active:bg-gray-50 transition">Редакция</a>
    </div>

    {{-- Client info --}}
    <x-card class="p-4 mb-5">
        <dl class="space-y-3">
            @if($client->phone)
            <div class="flex items-center justify-between">
                <div>
                    <dt class="text-[11px] text-gray-400 uppercase tracking-wider">Телефон</dt>
                    <dd class="mt-0.5"><a href="tel:{{ $client->phone }}" class="text-blue-600 font-medium text-lg">{{ $client->phone }}</a></dd>
                </div>
                <a href="tel:{{ $client->phone }}" class="p-2.5 rounded-full bg-green-50 text-green-600 active:bg-green-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                </a>
            </div>
            @endif
            @if($client->email)
            <div>
                <dt class="text-[11px] text-gray-400 uppercase tracking-wider">Имейл</dt>
                <dd class="mt-0.5"><a href="mailto:{{ $client->email }}" class="text-blue-600 text-sm">{{ $client->email }}</a></dd>
            </div>
            @endif
            @if($client->address)
            <div>
                <dt class="text-[11px] text-gray-400 uppercase tracking-wider">Адрес</dt>
                <dd class="mt-0.5 text-sm text-gray-900">{{ $client->address }}</dd>
            </div>
            @endif
            @if($client->notes)
            <div>
                <dt class="text-[11px] text-gray-400 uppercase tracking-wider">Бележки</dt>
                <dd class="mt-0.5 text-sm text-gray-700 whitespace-pre-line">{{ $client->notes }}</dd>
            </div>
            @endif
        </dl>
    </x-card>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 gap-2 mb-5">
        <a href="{{ route('jobs.create', ['client_id' => $client->id]) }}" class="flex items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-medium text-white active:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Нова задача
        </a>
        @if($client->phone)
        <a href="tel:{{ $client->phone }}" class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white py-3 text-sm font-medium text-gray-700 active:bg-gray-50 transition">
            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
            Обади се
        </a>
        @endif
    </div>

    {{-- Jobs --}}
    <section class="mb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-900">Задачи</h2>
            @if($client->jobs->count())
                <span class="text-xs text-gray-400">{{ $client->jobs->count() }}</span>
            @endif
        </div>
        <div class="space-y-2">
            @forelse($client->jobs as $job)
                <a href="{{ route('jobs.show', $job) }}" class="block">
                    <x-card class="p-3.5 active:bg-gray-50 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-sm text-gray-900 truncate">{{ $job->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $job->scheduled_date?->format('d.m.Y') ?? 'Без дата' }}</p>
                            </div>
                            <x-status-badge :status="$job->status" />
                        </div>
                    </x-card>
                </a>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">Няма задачи за този клиент</p>
            @endforelse
        </div>
    </section>

    {{-- Communication history --}}
    <section class="mb-5">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Комуникация</h2>
        @if($client->communications->count())
        <div class="space-y-2">
            @foreach($client->communications->take(10) as $comm)
                <div class="flex gap-3 py-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5
                        {{ $comm->direction === 'inbound' ? 'bg-green-50 text-green-500' : 'bg-blue-50 text-blue-500' }}">
                        @if($comm->type === 'call')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-medium text-gray-700">{{ $comm->typeLabel() }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $comm->direction === 'inbound' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">{{ $comm->directionLabel() }}</span>
                            <span class="text-[11px] text-gray-400">{{ $comm->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $comm->summary }}</p>
                        @if($comm->job)
                            <a href="{{ route('jobs.show', $comm->job) }}" class="text-[11px] text-blue-500 mt-0.5 inline-block">{{ $comm->job->title }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <p class="text-sm text-gray-400 text-center py-3">Няма записана комуникация</p>
        @endif
    </section>

    {{-- Danger zone --}}
    <div class="pt-4 border-t border-gray-100">
        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Сигурни ли сте, че искате да изтриете този клиент и всичките му задачи?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium">Изтрий клиента</button>
        </form>
    </div>
</x-app-layout>
