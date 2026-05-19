@csrf

<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Име *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $client->name ?? '') }}" required
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Иван Петров">
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
        <input type="tel" name="phone" id="phone" value="{{ old('phone', $client->phone ?? '') }}"
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="0888 123 456">
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Имейл</label>
        <input type="email" name="email" id="email" value="{{ old('email', $client->email ?? '') }}"
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="ivan@example.com">
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адрес</label>
        <input type="text" name="address" id="address" value="{{ old('address', $client->address ?? '') }}"
               class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="гр. София, ул. Витоша 1">
        <x-input-error :messages="$errors->get('address')" class="mt-1" />
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Бележки</label>
        <textarea name="notes" id="notes" rows="3"
                  class="w-full rounded-lg border-gray-300 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Допълнителна информация...">{{ old('notes', $client->notes ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
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
