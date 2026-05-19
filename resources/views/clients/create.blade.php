<x-app-layout>
    <x-slot:title>Нов клиент</x-slot:title>

    <x-page-header title="Нов клиент" />

    <x-card class="p-4 md:p-6">
        <form method="POST" action="{{ route('clients.store') }}">
            @include('clients._form', ['submitLabel' => 'Добави клиент'])
        </form>
    </x-card>
</x-app-layout>
