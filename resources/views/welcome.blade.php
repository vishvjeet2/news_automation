<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>News Automation</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <header class="w-full p-4 flex justify-end bg-white shadow">
        @if (Route::has('login'))
            <a href="{{ route('login') }}"
               class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Login
            </a>
        @endif
    </header>

    <!-- Main Content -->
    <main class="flex flex-1 items-center justify-center">
        <h1 class="text-4xl font-bold text-gray-800">
            Welcome to News Automation
        </h1>
    </main>

</body>
</html>