<!DOCTYPE html>
<html lang="bg" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Majstor') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased h-full">
    <div class="min-h-full flex flex-col items-center justify-center px-4 py-12">
        <a href="/" class="text-3xl font-bold text-blue-600 mb-8">Majstor</a>
        <div class="w-full max-w-md bg-white shadow-sm border border-gray-200 rounded-xl p-6 sm:p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
