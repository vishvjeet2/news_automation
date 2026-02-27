<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - News Automation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="w-full max-w-md bg-white border border-gray-200 shadow-2xl rounded-xl p-10">

        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-semibold text-black tracking-tight">
                News Automation
            </h1>
            <p class="text-gray-500 mt-2 text-sm">
                Sign in to your account
            </p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 bg-gray-100 border border-gray-300 text-gray-800 rounded-md text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input 
                    type="email"
                    name="email"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                    placeholder="Enter your email"
                >
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input 
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                    placeholder="Enter your password"
                >
            </div>

            <!-- Button -->
            <button 
                type="submit"
                class="w-full bg-black text-white py-2.5 rounded-md font-medium hover:bg-gray-800 transition duration-200 shadow-md"
            >
                Login
            </button>

        </form>

    </div>

</body>
</html>