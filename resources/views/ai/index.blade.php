<x-app-layout>
    <x-slot:title>AI Помощник</x-slot:title>

    <x-page-header title="AI Помощник" />

    <x-card class="p-4 md:p-6 mb-6">
        <p class="text-sm text-gray-500 mb-5">Опишете заявка на клиент и AI ще генерира обобщение, списък със задачи, материали и примерна оферта.</p>

        <form method="POST" action="{{ route('ai.query') }}" x-data="{ sending: false }" @submit="sending = true">
            @csrf

            <div class="mb-4">
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1.5">Клиент <span class="text-gray-400 font-normal">(по избор)</span></label>
                <select name="client_id" id="client_id"
                        class="w-full rounded-xl border-gray-200 py-3 text-[15px] focus:border-blue-500 focus:ring-blue-500">
                    <option value="">— Без клиент —</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }} {{ $client->phone ? "({$client->phone})" : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label for="prompt" class="block text-sm font-medium text-gray-700 mb-1.5">Описание на заявката</label>
                <textarea name="prompt" id="prompt" rows="5" required minlength="10"
                          class="w-full rounded-xl border-gray-200 py-3 text-[15px] focus:border-blue-500 focus:ring-blue-500 leading-relaxed"
                          placeholder="Пример: Клиент от Перник иска смяна на стар бойлер 80 литра, баня на втори етаж, достъпът е добър...">{{ old('prompt') }}</textarea>
                <x-input-error :messages="$errors->get('prompt')" class="mt-1" />
            </div>

            <button type="submit" :disabled="sending"
                    class="w-full rounded-xl bg-blue-600 py-3.5 text-sm font-medium text-white active:bg-blue-700 disabled:opacity-60 transition">
                <span x-show="!sending" class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
                    Анализирай с AI
                </span>
                <span x-show="sending" x-cloak class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Обработва се...
                </span>
            </button>
        </form>
    </x-card>

    @if($history->count())
    <h2 class="text-base font-semibold text-gray-900 mb-3">Предишни заявки</h2>
    <div class="space-y-2">
        @foreach($history as $query)
            <x-card class="p-4">
                <p class="text-sm text-gray-900 font-medium leading-snug">{{ Str::limit($query->prompt, 100) }}</p>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                    <span>{{ $query->created_at->diffForHumans() }}</span>
                    @if($query->client)
                        <span>· {{ $query->client->name }}</span>
                    @endif
                </div>
            </x-card>
        @endforeach
    </div>
    @endif
</x-app-layout>
