@csrf

<div class="space-y-4">
    <div>
        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Клиент *</label>
        <select name="client_id" id="client_id" required
                class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">— Изберете клиент —</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id', $job->client_id ?? request('client_id')) == $client->id)>
                    {{ $client->name }} {{ $client->phone ? "({$client->phone})" : '' }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('client_id')" class="mt-1" />
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Заглавие *</label>
        <input type="text" name="title" id="title" value="{{ old('title', $job->title ?? '') }}" required
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Ремонт на покрив">
        <x-input-error :messages="$errors->get('title')" class="mt-1" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
        <textarea name="description" id="description" rows="3"
                  class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Детайли за задачата...">{{ old('description', $job->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    @if(isset($job) && $job->exists)
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
        <select name="status" id="status"
                class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach(\App\Models\Job::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $job->status) === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div x-data="datepicker">
        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Насрочена дата</label>
        <input type="text" name="scheduled_date" id="scheduled_date" x-ref="input" autocomplete="off"
               value="{{ old('scheduled_date', isset($job) && $job->scheduled_date ? $job->scheduled_date->format('Y-m-d') : '') }}"
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Изберете дата">
        <x-input-error :messages="$errors->get('scheduled_date')" class="mt-1" />
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="estimated_price" class="block text-sm font-medium text-gray-700 mb-1">Приблизителна цена</label>
            <div class="relative">
                <input type="number" name="estimated_price" id="estimated_price" step="0.01" min="0"
                       value="{{ old('estimated_price', $job->estimated_price ?? '') }}"
                       class="w-full rounded-lg border-gray-300 py-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
                <span class="absolute right-3 top-3 text-sm text-gray-400">лв.</span>
            </div>
        </div>
        <div>
            <label for="actual_price" class="block text-sm font-medium text-gray-700 mb-1">Реална цена</label>
            <div class="relative">
                <input type="number" name="actual_price" id="actual_price" step="0.01" min="0"
                       value="{{ old('actual_price', $job->actual_price ?? '') }}"
                       class="w-full rounded-lg border-gray-300 py-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
                <span class="absolute right-3 top-3 text-sm text-gray-400">лв.</span>
            </div>
        </div>
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Бележки за клиента</label>
        <textarea name="notes" id="notes" rows="2"
                  class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Видими бележки...">{{ old('notes', $job->notes ?? '') }}</textarea>
    </div>

    <div>
        <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-1">
            Вътрешни бележки
            <span class="text-xs text-gray-400 font-normal">(само за мен)</span>
        </label>
        <textarea name="internal_notes" id="internal_notes" rows="2"
                  class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-amber-50/50"
                  placeholder="Лични бележки, които клиентът не вижда...">{{ old('internal_notes', $job->internal_notes ?? '') }}</textarea>
    </div>

    {{-- Checklist --}}
    <div x-data="checklist" data-items="{{ old('checklist', isset($job) ? json_encode($job->checklist ?? []) : '[]') }}">
        <label class="block text-sm font-medium text-gray-700 mb-2">Чеклист</label>
        <input type="hidden" name="checklist" :value="json">
        <div class="space-y-1.5 mb-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex items-center gap-2 group">
                    <button type="button" @click="toggle(index)"
                            class="shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition"
                            :class="item.done ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300'">
                        <svg x-show="item.done" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </button>
                    <span class="flex-1 text-sm" :class="item.done ? 'line-through text-gray-400' : 'text-gray-700'" x-text="item.text"></span>
                    <button type="button" @click="remove(index)" class="text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 p-1 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
        <div class="flex gap-2">
            <input type="text" x-model="newItem" @keydown.enter.prevent="add()" placeholder="Добави точка..."
                   class="flex-1 rounded-lg border-gray-300 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            <button type="button" @click="add()" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200 transition">+</button>
        </div>
    </div>
</div>

<div class="mt-6 flex gap-3" x-data="{ saving: false }">
    <button type="submit" @click="saving = true" :disabled="saving"
            class="flex-1 rounded-xl bg-blue-600 py-3.5 text-sm font-medium text-white active:bg-blue-700 disabled:opacity-60 transition">
        <span x-show="!saving">{{ $submitLabel ?? 'Запази' }}</span>
        <span x-show="saving" x-cloak>Запазване...</span>
    </button>
    <a href="{{ url()->previous() }}" class="rounded-xl border border-gray-200 px-6 py-3.5 text-sm font-medium text-gray-600 active:bg-gray-50 transition">
        Отказ
    </a>
</div>
