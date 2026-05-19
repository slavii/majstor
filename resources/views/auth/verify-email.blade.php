<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-900 text-center mb-4">Потвърдете имейла си</h2>

    <div class="mb-4 text-sm text-gray-600">
        Благодарим за регистрацията! Моля, потвърдете имейл адреса си като кликнете на линка, който ви изпратихме. Ако не сте получили имейла, ще ви изпратим нов.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Нов линк за потвърждение беше изпратен на вашия имейл адрес.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Изпрати отново
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                Изход
            </button>
        </form>
    </div>
</x-guest-layout>
