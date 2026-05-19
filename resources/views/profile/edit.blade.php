<x-app-layout>
    <x-slot:title>Профил</x-slot:title>

    <x-page-header title="Профил" />

    <div class="space-y-6">
        <x-card class="p-4 md:p-6">
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <x-card class="p-4 md:p-6">
            @include('profile.partials.update-password-form')
        </x-card>

        <x-card class="p-4 md:p-6">
            @include('profile.partials.delete-user-form')
        </x-card>
    </div>
</x-app-layout>
