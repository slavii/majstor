<x-app-layout>
    <x-slot:title>{{ $job->title }}</x-slot:title>

    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $job->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('clients.show', $job->client) }}" class="text-blue-600 hover:underline">{{ $job->client->name }}</a>
                @if($job->scheduled_date) · {{ $job->scheduled_date->format('d.m.Y') }} @endif
            </p>
        </div>
        <x-status-badge :status="$job->status" />
    </div>

    {{-- Actions --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('jobs.edit', $job) }}" class="flex-1 text-center rounded-lg bg-blue-600 py-3 text-sm font-medium text-white hover:bg-blue-700 transition">Редактирай</a>
        @if($job->client->phone)
            <a href="tel:{{ $job->client->phone }}" class="rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">Обади се</a>
        @endif
        <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Сигурни ли сте?')">
            @csrf @method('DELETE')
            <button type="submit" class="rounded-lg border border-red-200 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50">Изтрий</button>
        </form>
    </div>

    {{-- Details --}}
    <x-card class="p-4 md:p-6 mb-6">
        @if($job->description)
            <div class="mb-4">
                <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-1">Описание</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $job->description }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            @if($job->estimated_price)
            <div>
                <h3 class="text-xs text-gray-500 uppercase tracking-wider">Приблизителна цена</h3>
                <p class="text-lg font-semibold text-gray-900 mt-1">{{ number_format($job->estimated_price, 2) }} лв.</p>
            </div>
            @endif
            @if($job->actual_price)
            <div>
                <h3 class="text-xs text-gray-500 uppercase tracking-wider">Реална цена</h3>
                <p class="text-lg font-semibold text-green-600 mt-1">{{ number_format($job->actual_price, 2) }} лв.</p>
            </div>
            @endif
        </div>

        @if($job->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-1">Бележки</h3>
                <p class="text-gray-700 whitespace-pre-line text-sm">{{ $job->notes }}</p>
            </div>
        @endif
    </x-card>

    {{-- Photos --}}
    <section class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Снимки</h2>

        @if($job->photos->count())
            <div class="grid grid-cols-3 gap-2 mb-3">
                @foreach($job->photos as $photo)
                    <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100">
                        <img src="{{ $photo->url() }}" alt="" class="w-full h-full object-cover">
                        <form method="POST" action="{{ route('jobs.photos.delete', [$job, $photo]) }}"
                              class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-full bg-red-500 text-white w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('jobs.photos.upload', $job) }}" enctype="multipart/form-data">
            @csrf
            <div class="flex gap-2">
                <input type="file" name="photos[]" multiple accept="image/*"
                       class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Качи</button>
            </div>
            <x-input-error :messages="$errors->get('photos')" class="mt-1" />
            <x-input-error :messages="$errors->get('photos.*')" class="mt-1" />
        </form>
    </section>

    {{-- Comments --}}
    <section class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Коментари</h2>

        <form method="POST" action="{{ route('jobs.comments.store', $job) }}" class="mb-4">
            @csrf
            <textarea name="body" rows="2" required placeholder="Добави коментар..."
                      class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 mb-2"></textarea>
            <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Добави</button>
        </form>

        <div class="space-y-3">
            @foreach($job->comments as $comment)
                <x-card class="p-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-700">{{ $comment->body }}</p>
                </x-card>
            @endforeach
        </div>
    </section>

    {{-- Status timeline --}}
    <section>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">История</h2>
        <div class="space-y-2">
            @foreach($job->statusHistory as $entry)
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                    <span class="text-gray-500">{{ $entry->created_at->format('d.m.Y H:i') }}</span>
                    <span class="text-gray-700">
                        @if($entry->old_status)
                            {{ \App\Models\Job::STATUSES[$entry->old_status] ?? $entry->old_status }} →
                        @endif
                        <span class="font-medium">{{ \App\Models\Job::STATUSES[$entry->new_status] ?? $entry->new_status }}</span>
                    </span>
                    <span class="text-gray-400">от {{ $entry->user->name }}</span>
                </div>
            @endforeach
        </div>
    </section>
</x-app-layout>
