<x-app-layout>
    <x-slot:title>{{ $job->title }}</x-slot:title>

    {{-- Header --}}
    <div class="mb-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ $job->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    <a href="{{ route('clients.show', $job->client) }}" class="text-blue-600">{{ $job->client->name }}</a>
                    @if($job->scheduled_date) · {{ $job->scheduled_date->format('d.m.Y') }} @endif
                </p>
            </div>
            <x-status-badge :status="$job->status" />
        </div>
    </div>

    {{-- Primary actions --}}
    <div class="grid grid-cols-2 gap-2 mb-5">
        <a href="{{ route('jobs.edit', $job) }}" class="flex items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-medium text-white active:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
            Редактирай
        </a>
        @if($job->client->phone)
            <a href="tel:{{ $job->client->phone }}" class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white py-3 text-sm font-medium text-gray-700 active:bg-gray-50 transition">
                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                {{ $job->client->phone }}
            </a>
        @endif
    </div>

    {{-- Description + prices --}}
    <x-card class="p-4 mb-4">
        @if($job->description)
            <p class="text-[15px] text-gray-700 whitespace-pre-line leading-relaxed">{{ $job->description }}</p>
            @if($job->estimated_price || $job->actual_price)
                <hr class="my-3 border-gray-100">
            @endif
        @endif

        @if($job->estimated_price || $job->actual_price)
        <div class="flex gap-6">
            @if($job->estimated_price)
            <div>
                <p class="text-[11px] text-gray-400 uppercase tracking-wider">Оценка</p>
                <p class="text-lg font-semibold text-gray-900 mt-0.5">{{ number_format($job->estimated_price, 0) }} <span class="text-sm font-normal text-gray-400">лв.</span></p>
            </div>
            @endif
            @if($job->actual_price)
            <div>
                <p class="text-[11px] text-gray-400 uppercase tracking-wider">Реална</p>
                <p class="text-lg font-semibold text-green-600 mt-0.5">{{ number_format($job->actual_price, 0) }} <span class="text-sm font-normal text-green-400">лв.</span></p>
            </div>
            @endif
        </div>
        @endif

        @if($job->notes)
            <hr class="my-3 border-gray-100">
            <p class="text-sm text-gray-500 whitespace-pre-line">{{ $job->notes }}</p>
        @endif
    </x-card>

    {{-- Internal notes --}}
    @if($job->internal_notes)
    <x-card class="p-4 mb-4 border-amber-200 bg-amber-50/40">
        <div class="flex items-center gap-1.5 mb-1.5">
            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Вътрешни бележки</p>
        </div>
        <p class="text-sm text-amber-900/80 whitespace-pre-line">{{ $job->internal_notes }}</p>
    </x-card>
    @endif

    {{-- Checklist --}}
    @php $checklist = $job->checklist ?? []; @endphp
    <section class="mb-5" x-data="checklist" data-items='@json($checklist)'>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-900">Чеклист</h2>
            <template x-if="items.length > 0">
                <span class="text-xs text-gray-400" x-text="items.filter(i => i.done).length + '/' + items.length"></span>
            </template>
        </div>

        @if(count($checklist))
        <div class="mb-3">
            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-green-500 transition-all duration-300"
                     :style="'width:' + (items.length ? (items.filter(i=>i.done).length / items.length * 100) : 0) + '%'"></div>
            </div>
        </div>
        @endif

        <x-card class="p-3">
            <input type="hidden" form="checklist-form" name="checklist" :value="json">
            <div class="space-y-1">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-center gap-2.5 py-1.5 group" @click="toggle(index)">
                        <span class="shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center cursor-pointer transition"
                              :class="item.done ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 hover:border-blue-400'">
                            <svg x-show="item.done" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </span>
                        <span class="flex-1 text-sm cursor-pointer" :class="item.done ? 'line-through text-gray-400' : 'text-gray-700'" x-text="item.text"></span>
                    </div>
                </template>
            </div>

            <div x-show="items.length === 0" class="py-3 text-center text-sm text-gray-400">
                Няма точки в чеклиста
            </div>

            <div class="flex gap-2 mt-2 pt-2 border-t border-gray-100">
                <input type="text" x-model="newItem" @keydown.enter.prevent="add()" placeholder="Добави точка..."
                       class="flex-1 rounded-lg border-gray-200 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" @click="add()" class="shrink-0 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-600 active:bg-gray-200">+</button>
            </div>
        </x-card>

        <form id="checklist-form" method="POST" action="{{ route('jobs.checklist.update', $job) }}" class="mt-2">
            @csrf @method('PUT')
            <button type="submit" x-show="items.length > 0"
                    class="text-xs text-blue-600 font-medium hover:text-blue-700">Запази чеклиста</button>
        </form>
    </section>

    {{-- Photo gallery --}}
    <section class="mb-5">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Снимки</h2>

        @php
            $photosByCategory = $job->photos->groupBy('category');
            $categoryOrder = ['before', 'after', 'progress', 'general'];
        @endphp

        @if($job->photos->count())
            @foreach($categoryOrder as $cat)
                @if(isset($photosByCategory[$cat]) && $photosByCategory[$cat]->count())
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-3 first:mt-0">
                        {{ \App\Models\JobPhoto::CATEGORIES[$cat] }}
                        <span class="text-gray-400 font-normal">({{ $photosByCategory[$cat]->count() }})</span>
                    </p>
                    <div class="grid grid-cols-3 gap-1.5 mb-2">
                        @foreach($photosByCategory[$cat] as $photo)
                            <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ $photo->url() }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                <form method="POST" action="{{ route('jobs.photos.delete', [$job, $photo]) }}"
                                      class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-full bg-white/90 text-red-600 w-8 h-8 flex items-center justify-center text-sm font-bold">✕</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endif

        {{-- Upload with category --}}
        <form method="POST" action="{{ route('jobs.photos.upload', $job) }}" enctype="multipart/form-data"
              x-data="{ uploading: false, category: 'general' }" @submit="uploading = true" class="mt-3">
            @csrf
            <div class="flex gap-2 mb-2">
                <select name="category" x-model="category"
                        class="rounded-lg border-gray-200 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
                    @foreach(\App\Models\JobPhoto::CATEGORIES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-white py-4 text-sm text-gray-500 cursor-pointer hover:border-blue-300 hover:text-blue-600 active:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                <span x-show="!uploading">Добави снимки</span>
                <span x-show="uploading" x-cloak>Качване...</span>
                <input type="file" name="photos[]" multiple accept="image/*" class="hidden"
                       @change="if ($event.target.files.length) { uploading = true; $el.closest('form').submit(); }">
            </label>
            <x-input-error :messages="$errors->get('photos')" class="mt-1" />
            <x-input-error :messages="$errors->get('photos.*')" class="mt-1" />
        </form>
    </section>

    {{-- Voice notes placeholder --}}
    <section class="mb-5">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Гласови бележки</h2>
        @if($job->voiceNotes->count())
            <div class="space-y-2 mb-3">
                @foreach($job->voiceNotes as $note)
                    <x-card class="p-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 truncate">{{ $note->transcription ?: 'Без транскрипция' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $note->user->name }} · {{ $note->created_at->diffForHumans() }}</p>
                        </div>
                        @if($note->duration_seconds)
                            <span class="text-xs text-gray-400 shrink-0">{{ gmdate('i:s', $note->duration_seconds) }}</span>
                        @endif
                    </x-card>
                @endforeach
            </div>
        @endif
        <x-card class="p-4 text-center border-dashed">
            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z"/></svg>
            <p class="text-xs text-gray-400">Записването на гласови бележки ще бъде достъпно скоро</p>
        </x-card>
    </section>

    {{-- Comments --}}
    <section class="mb-5">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Коментари</h2>

        <form method="POST" action="{{ route('jobs.comments.store', $job) }}" class="mb-4"
              x-data="{ body: '', sending: false }" @submit="sending = true">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="body" x-model="body" required placeholder="Добави бележка..."
                       class="flex-1 rounded-xl border-gray-200 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" :disabled="!body.trim() || sending"
                        class="shrink-0 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white disabled:opacity-40 active:bg-gray-800 transition">
                    <span x-show="!sending">Добави</span>
                    <span x-show="sending" x-cloak>...</span>
                </button>
            </div>
        </form>

        @if($job->comments->count())
        <div class="space-y-1">
            @foreach($job->comments as $comment)
                <div class="flex gap-3 py-2">
                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-500 shrink-0 mt-0.5">
                        {{ mb_substr($comment->user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                            <span class="text-[11px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $comment->body }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <p class="text-sm text-gray-400 text-center py-3">Няма коментари</p>
        @endif
    </section>

    {{-- Client communication log --}}
    <section class="mb-5">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Комуникация с клиента</h2>

        {{-- Add communication --}}
        <div x-data="{ open: false, sending: false }" class="mb-3">
            <button type="button" @click="open = !open"
                    class="text-sm text-blue-600 font-medium mb-2" x-text="open ? 'Скрий' : '+ Запиши комуникация'"></button>

            <form method="POST" action="{{ route('jobs.communications.store', $job) }}"
                  x-show="open" x-cloak x-transition @submit="sending = true">
                @csrf
                <x-card class="p-3 space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <select name="type" class="rounded-lg border-gray-200 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(\App\Models\ClientCommunication::TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="direction" class="rounded-lg border-gray-200 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(\App\Models\ClientCommunication::DIRECTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <textarea name="summary" rows="2" required placeholder="Какво обсъдихте..."
                              class="w-full rounded-lg border-gray-200 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <button type="submit" :disabled="sending"
                            class="w-full rounded-lg bg-gray-900 py-2.5 text-sm font-medium text-white disabled:opacity-40 active:bg-gray-800 transition">
                        <span x-show="!sending">Запиши</span>
                        <span x-show="sending" x-cloak>Запазване...</span>
                    </button>
                </x-card>
            </form>
        </div>

        @if($job->client->communications->count())
        <div class="space-y-2">
            @foreach($job->client->communications->take(8) as $comm)
                <div class="flex gap-3 py-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5
                        {{ $comm->direction === 'inbound' ? 'bg-green-50 text-green-500' : 'bg-blue-50 text-blue-500' }}">
                        @if($comm->type === 'call')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        @elseif($comm->type === 'viber' || $comm->type === 'sms')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                        @elseif($comm->type === 'email')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-medium text-gray-700">{{ $comm->typeLabel() }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $comm->direction === 'inbound' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">{{ $comm->directionLabel() }}</span>
                            <span class="text-[11px] text-gray-400">{{ $comm->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $comm->summary }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <p class="text-sm text-gray-400 text-center py-3">Няма записана комуникация</p>
        @endif
    </section>

    {{-- Unified timeline --}}
    <section class="mb-5">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Хронология</h2>

        @php
            $timeline = collect();

            foreach ($job->statusHistory as $h) {
                $timeline->push([
                    'time' => $h->created_at,
                    'type' => 'status',
                    'icon' => 'arrow-path',
                    'color' => 'blue',
                    'text' => ($h->old_status ? \App\Models\Job::STATUSES[$h->old_status] . ' → ' : 'Създадена: ') . \App\Models\Job::STATUSES[$h->new_status],
                    'by' => $h->user->name,
                ]);
            }

            foreach ($job->comments as $c) {
                $timeline->push([
                    'time' => $c->created_at,
                    'type' => 'comment',
                    'icon' => 'chat-bubble-left',
                    'color' => 'gray',
                    'text' => $c->body,
                    'by' => $c->user->name,
                ]);
            }

            if ($job->client->communications) {
                foreach ($job->client->communications->where('job_id', $job->id) as $comm) {
                    $timeline->push([
                        'time' => $comm->created_at,
                        'type' => 'comm',
                        'icon' => 'phone',
                        'color' => 'green',
                        'text' => $comm->typeLabel() . ' (' . $comm->directionLabel() . '): ' . $comm->summary,
                        'by' => $comm->user->name,
                    ]);
                }
            }

            $timeline = $timeline->sortByDesc('time')->values();
        @endphp

        @if($timeline->count())
        <div class="relative pl-4 border-l-2 border-gray-100 space-y-3">
            @foreach($timeline as $event)
                <div class="relative">
                    <span class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full ring-2 ring-white
                        {{ match($event['color']) { 'blue' => 'bg-blue-500', 'green' => 'bg-green-500', default => 'bg-gray-300' } }}"></span>
                    <p class="text-sm text-gray-700">{{ Str::limit($event['text'], 120) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $event['by'] }} · {{ $event['time']->format('d.m.Y, H:i') }}</p>
                </div>
            @endforeach
        </div>
        @else
            <p class="text-sm text-gray-400 text-center py-3">Няма активност</p>
        @endif
    </section>

    {{-- Danger zone --}}
    <div class="pt-4 border-t border-gray-100">
        <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Сигурни ли сте, че искате да изтриете тази задача?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium">Изтрий задачата</button>
        </form>
    </div>
</x-app-layout>
