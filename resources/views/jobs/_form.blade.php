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

    <div>
        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Насрочена дата</label>
        <input type="date" name="scheduled_date" id="scheduled_date"
               value="{{ old('scheduled_date', isset($job) && $job->scheduled_date ? $job->scheduled_date->format('Y-m-d') : '') }}"
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
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
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Бележки</label>
        <textarea name="notes" id="notes" rows="3"
                  class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Вътрешни бележки...">{{ old('notes', $job->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="flex-1 rounded-lg bg-blue-600 py-3 text-sm font-medium text-white hover:bg-blue-700 active:bg-blue-800 transition">
        {{ $submitLabel ?? 'Запази' }}
    </button>
    <a href="{{ url()->previous() }}" class="rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        Отказ
    </a>
</div>
