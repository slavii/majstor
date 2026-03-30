<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.app_name') }} — {{ __('messages.tagline') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } } }</script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        </style>
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('search') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <circle cx="12" cy="12" r="4" />
                            <line x1="12" y1="2" x2="12" y2="5" />
                            <line x1="12" y1="19" x2="12" y2="22" />
                            <line x1="2" y1="12" x2="5" y2="12" />
                            <line x1="19" y1="12" x2="22" y2="12" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">{{ __('messages.app_name') }}</span>
                </a>

                <div class="flex items-center gap-2">
                    @foreach(['bg' => '🇧🇬', 'ro' => '🇷🇴', 'en' => '🇬🇧'] as $code => $flag)
                        <a href="{{ route('language.switch', $code) }}"
                           class="px-2 py-1 text-sm rounded-md transition-colors {{ app()->getLocale() === $code ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-500 hover:bg-gray-100' }}">
                            {{ $flag }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="mt-12 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                <p>{{ __('messages.powered_by') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
