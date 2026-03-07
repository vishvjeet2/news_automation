<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>News Automation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Mobile Top Bar -->
    <div class="md:hidden fixed top-0 left-0 w-full bg-white border-b z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <h2 class="text-lg font-semibold">News Automation</h2>
            <button onclick="toggleSidebar()" class="text-2xl">☰</button>
        </div>
    </div>
    
    <div class="flex min-h-screen">
    
        <!-- Sidebar -->


        <aside id="sidebar"
        class="w-full md:w-64 bg-white border-r border-gray-200
        flex flex-col fixed inset-y-0 left-0
        transform -translate-x-full md:translate-x-0
        transition-transform duration-300 z-50">
            <!-- Mobile Close Button -->
            <div class="md:hidden flex justify-end p-4 border-b">
                <button onclick="toggleSidebar()" class="text-2xl leading-none">
                    ✕
                </button>
            </div>

            <div class="p-6 border-b border-gray-200 hidden md:block">
                <h2 class="text-xl font-semibold text-black">News Automation</h2>
            </div>

            <nav class="flex-1 p-4 space-y-2">

                <!--
                |-------------------------------------------------
                | Dashboard Link
                |-------------------------------------------------
                -->

                <a href="<?php echo e(route('dashboard')); ?>"
                    class="flex items-center gap-3 px-3 py-2 rounded-md transition
                    <?php echo e(request()->routeIs('dashboard')
                        ? 'bg-gray-200 text-black border-l-4 border-black'
                        : 'text-gray-700 hover:bg-gray-100'); ?>">

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

                <a href="<?php echo e(route('posts.create')); ?>"
                    class="flex items-center gap-3 px-3 py-2 rounded-md transition
                    <?php echo e(request()->routeIs('posts.create')
                        ? 'bg-gray-200 text-black border-l-4 border-black'
                        : 'text-gray-700 hover:bg-gray-100'); ?>">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>

                    <span class="sidebar-text">Create Post</span>
                </a>


            </nav>

             <!--
            |-------------------------------------------------
            | Logout Button
            |-------------------------------------------------
            -->
            <div class="p-4 border-t border-gray-200">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
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
        <main class="flex-1 p-6 md:p-8 md:ml-64">

            <!-- Top Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-black">
                    <?php echo $__env->yieldContent('title'); ?>
                </h1>
            </div>
            
            <?php echo $__env->yieldPushContent('scripts'); ?>

            <?php echo $__env->yieldContent('content'); ?>

        </main>

    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
    </script>   

</body>

</html>
<?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/layouts/app.blade.php ENDPATH**/ ?>