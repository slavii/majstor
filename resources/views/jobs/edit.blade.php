<x-app-layout>
    <x-slot:title>Редакция на задача</x-slot:title>

    <x-page-header title="Редакция: {{ $job->title }}" />

    <x-card class="p-4 md:p-6">
        <form method="POST" action="{{ route('jobs.update', $job) }}">
            @method('PUT')
            @include('jobs._form', ['submitLabel' => 'Запази промените'])
        </form>
    </x-card>
</x-app-layout>
