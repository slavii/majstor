<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-900 text-center mb-4">Потвърждение</h2>

    <div class="mb-4 text-sm text-gray-600">
        Моля, въведете паролата си, за да продължите.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Парола" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                Потвърди
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
