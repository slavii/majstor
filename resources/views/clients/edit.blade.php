<x-app-layout>
    <x-slot:title>Редакция на клиент</x-slot:title>

    <x-page-header title="Редакция: {{ $client->name }}" />

    <x-card class="p-4 md:p-6">
        <form method="POST" action="{{ route('clients.update', $client) }}">
            @method('PUT')
            @include('clients._form', ['submitLabel' => 'Запази промените'])
        </form>
    </x-card>
</x-app-layout>
