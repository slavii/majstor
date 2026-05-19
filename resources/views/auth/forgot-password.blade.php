<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-900 text-center mb-4">Забравена парола</h2>

    <div class="mb-4 text-sm text-gray-600">
        Въведете имейл адреса си и ще ви изпратим линк за нулиране на паролата.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Имейл" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                Изпрати линк за нулиране
            </x-primary-button>
        </div>

        <p class="mt-4 text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Обратно към вход</a>
        </p>
    </form>
</x-guest-layout>
