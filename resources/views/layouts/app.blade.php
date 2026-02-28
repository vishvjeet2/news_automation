<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>News Automation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">

            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-black">
                    News Automation
                </h2>
            </div>

            <nav class="flex-1 p-4 space-y-2">

                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-2 rounded-md transition
                    {{ request()->routeIs('dashboard') 
                    ? 'bg-gray-200 text-black font-medium border-l-4 border-black' 
                    : 'text-gray-700 hover:bg-gray-100' }}">
                    Dashboard
                </a>

                <a href="{{ route('posts.create') }}"
                    class="block px-4 py-2 rounded-md transition
                    {{ request()->routeIs('posts.create')
                    ? 'bg-gray-200 text-black font-medium border-l-4 border-black'
                    : 'text-gray-700 hover:bg-gray-100' }}">
                    Create Post
                </a>

                <a href="{{ route('view.category.store') }}"
                    class="block px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition">
                    Categories
                </a>

                <a href="{{ route('templates.index') }}"
                    class="block px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition">
                    Templates
                </a>

            </nav>

            <div class="p-4 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition">
                        Logout
                    </button>
                </form>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">

            <!-- Top Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-black">
                    @yield('title')
                </h1>
            </div>
            
            @stack('scripts')

            @yield('content')

        </main>

    </div>

</body>

</html>
