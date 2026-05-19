<x-app-layout>
    <x-slot:title>AI Помощник</x-slot:title>

    <x-page-header title="AI Помощник" />

    <x-card class="p-4 md:p-6 mb-6">
        <p class="text-sm text-gray-500 mb-4">Опишете заявка на клиент и AI ще ви помогне с обобщение, списък със задачи, материали и оферта.</p>

        <form method="POST" action="{{ route('ai.query') }}">
            @csrf

            <div class="mb-4">
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Клиент (по избор)</label>
                <select name="client_id" id="client_id"
                        class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">— Без клиент —</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="prompt" class="block text-sm font-medium text-gray-700 mb-1">Описание на заявката *</label>
                <textarea name="prompt" id="prompt" rows="4" required minlength="10"
                          class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Пример: Клиент от Кичево иска топлоизолация на покрив, около 120 квадрата...">{{ old('prompt') }}</textarea>
                <x-input-error :messages="$errors->get('prompt')" class="mt-1" />
            </div>

            <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 text-sm font-medium text-white hover:bg-blue-700 active:bg-blue-800 transition">
                Анализирай с AI
            </button>
        </form>
    </x-card>

    {{-- History --}}
    @if($history->count())
    <h2 class="text-lg font-semibold text-gray-900 mb-3">Последни заявки</h2>
    <div class="space-y-2">
        @foreach($history as $query)
            <x-card class="p-4">
                <p class="text-sm text-gray-900 font-medium truncate">{{ Str::limit($query->prompt, 80) }}</p>
                <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
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
