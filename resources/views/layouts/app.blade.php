<!DOCTYPE html>
<html lang="bg" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Majstor') }} — {{ $title ?? 'Табло' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900" x-data="{ mobileMenu: false }">

    {{-- Top bar --}}
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between px-4 h-14 max-w-5xl mx-auto">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600 tracking-tight">Majstor</a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1">
                <x-nav-item href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">Табло</x-nav-item>
                <x-nav-item href="{{ route('clients.index') }}" :active="request()->routeIs('clients.*')">Клиенти</x-nav-item>
                <x-nav-item href="{{ route('jobs.index') }}" :active="request()->routeIs('jobs.*')">Задачи</x-nav-item>
                <x-nav-item href="{{ route('ai.index') }}" :active="request()->routeIs('ai.*')">AI Помощник</x-nav-item>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-red-500">Изход</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="max-w-5xl mx-auto px-4 mt-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-5xl mx-auto px-4 mt-3">
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main content --}}
    <main class="max-w-5xl mx-auto px-4 py-6 pb-24 md:pb-6">
        {{ $slot }}
    </main>

    {{-- Mobile bottom navigation --}}
    <nav class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-gray-200 md:hidden">
        <div class="flex justify-around py-2">
            <x-mobile-nav href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                </x-slot:icon>
                Табло
            </x-mobile-nav>
            <x-mobile-nav href="{{ route('clients.index') }}" :active="request()->routeIs('clients.*')">
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </x-slot:icon>
                Клиенти
            </x-mobile-nav>
            <x-mobile-nav href="{{ route('jobs.index') }}" :active="request()->routeIs('jobs.*')">
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.193-.14 1.743"/></svg>
                </x-slot:icon>
                Задачи
            </x-mobile-nav>
            <x-mobile-nav href="{{ route('ai.index') }}" :active="request()->routeIs('ai.*')">
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                </x-slot:icon>
                AI
            </x-mobile-nav>
        </div>
    </nav>

</body>
</html>
