<!DOCTYPE html>
<html lang="bg" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Majstor — Бизнес асистент за майстори</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-white">
    <div class="min-h-full flex flex-col">
        {{-- Header --}}
        <header class="px-6 py-4 flex items-center justify-between max-w-5xl mx-auto w-full">
            <span class="text-2xl font-bold text-blue-600">Majstor</span>
            <div class="flex gap-3">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 px-3 py-2">Вход</a>
                <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2">Регистрация</a>
            </div>
        </header>

        {{-- Hero --}}
        <main class="flex-1 flex items-center">
            <div class="max-w-5xl mx-auto px-6 py-16 md:py-24 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                    Организирай бизнеса си<br>
                    <span class="text-blue-600">без сложни системи</span>
                </h1>
                <p class="mt-6 text-lg text-gray-500 max-w-2xl mx-auto">
                    Majstor е прост инструмент за майстори, електротехници, водопроводчици и строители. Управлявай клиенти, задачи и оферти от телефона си.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}" class="rounded-xl bg-blue-600 px-8 py-4 text-base font-semibold text-white hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                        Започни безплатно
                    </a>
                    <a href="{{ route('login') }}" class="rounded-xl border-2 border-gray-200 px-8 py-4 text-base font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50 transition">
                        Вече имам акаунт
                    </a>
                </div>

                {{-- Features --}}
                <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                    <div class="p-6 rounded-xl bg-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Клиенти</h3>
                        <p class="text-sm text-gray-500 mt-2">Всичките ви клиенти на едно място. Телефони, адреси, история.</p>
                    </div>
                    <div class="p-6 rounded-xl bg-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.193-.14 1.743"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Задачи</h3>
                        <p class="text-sm text-gray-500 mt-2">Следете всяка задача от заявка до завършване. Снимки, коментари, цени.</p>
                    </div>
                    <div class="p-6 rounded-xl bg-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">AI Помощник</h3>
                        <p class="text-sm text-gray-500 mt-2">Опишете заявка и получете оферта, списък с материали и задачи за секунди.</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center py-6 text-sm text-gray-400">
            &copy; {{ date('Y') }} Majstor. Всички права запазени.
        </footer>
    </div>
</body>
</html>
