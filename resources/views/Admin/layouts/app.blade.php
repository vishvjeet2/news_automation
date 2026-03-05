<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - News Automation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    {{-- <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed top-0 left-0 h-screen"> --}}
        {{-- it will mak side bar scrollable in future in case of overflow --}}
        

        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed top-0 left-0 h-screen overflow-y-auto">
        
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-black">
                            Admin Panel
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">News Automation</p>
                    </div>
        
                    <nav class="flex-1 p-4 space-y-2">
        
                        <!--
                        |-------------------------------------------------
                        | Dashboard Link
                        |-------------------------------------------------
                        -->
        
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-md transition
                            {{ request()->routeIs('admin.dashboard')
                                ? 'bg-gray-200 text-black border-l-4 border-black'
                                : 'text-gray-700 hover:bg-gray-100' }}">
        
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10h6v-6h4v6h6V10" />
                            </svg>
        
                            <span class="sidebar-text">Dashboard</span>
                        </a>
        
                        <!--
                        |-------------------------------------------------
                        | Create Post Link
                        |-------------------------------------------------
                        -->
        
                        <a href="{{ route('admin.posts.create') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-md transition
                            {{ request()->routeIs('admin.posts.create')
                                ? 'bg-gray-200 text-black border-l-4 border-black'
                                : 'text-gray-700 hover:bg-gray-100' }}">
        
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
        
                            <span class="sidebar-text">Create Post</span>
                        </a>
        
                        <!--
                        |-------------------------------------------------
                        | Create Category Link
                        |-------------------------------------------------
                        -->
        
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-md transition
                            {{ request()->routeIs('admin.categories.*')
                                ? 'bg-gray-200 text-black border-l-4 border-black'
                                : 'text-gray-700 hover:bg-gray-100' }}">
        
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7" />
                            </svg>
        
                            <span class="sidebar-text">Category</span>
                        </a>
        
                    </nav>
        
                    <!--
                    |-------------------------------------------------
                    | Logout Button
                    |-------------------------------------------------
                    -->
                    <div class="p-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition">
        
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                                </svg>
        
                                <span class="sidebar-text">Logout</span>
                            </button>
                        </form>
                    </div>
        
                </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 ml-64">

        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-black">
                @yield('title')
            </h1>
        </div>

        @yield('content')
        @stack('scripts')

    </main>

</div>

</body>
</html>