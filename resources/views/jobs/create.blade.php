<x-app-layout>
    <x-slot:title>Нова задача</x-slot:title>

    <x-page-header title="Нова задача" />

    <x-card class="p-4 md:p-6">
        <form method="POST" action="{{ route('jobs.store') }}">
            @include('jobs._form', ['submitLabel' => 'Създай задача'])
        </form>
    </x-card>
</x-app-layout>
