<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-900 text-center mb-6">Вход в акаунта</h2>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Имейл" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Парола" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Запомни ме</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-700" href="{{ route('password.request') }}">
                    Забравена парола?
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                Вход
            </x-primary-button>
        </div>

        <p class="mt-4 text-center text-sm text-gray-500">
            Нямате акаунт?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">Регистрирайте се</a>
        </p>
    </form>
</x-guest-layout>
