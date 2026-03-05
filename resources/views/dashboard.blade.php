@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- 1. Stats Grid -->
<!-- Uses grid-cols-1 for mobile, 2 for tablet, 4 for desktop -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Total Posts</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Images</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['images'] }}</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Videos</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['videos'] }}</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Drafts</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['drafts'] }}</p>
    </div>

</div>

<!-- 2. Action Bar -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <!-- Optional: Search bar could go here -->
    <div class="hidden md:block"></div> 

    <!-- Create Button: Full width on mobile, auto width on desktop -->
    <a href="{{ route('posts.create') }}"
       class="block w-full md:w-auto text-center bg-black text-white px-6 py-3 rounded-lg shadow hover:bg-gray-800 transition font-medium text-sm md:text-base">
        + Create New Post
    </a>
</div>

<!-- 3. Responsive Table Container -->
<!-- 
    Mobile: Transparent background (so cards 'float'), no border.
    Desktop: White background, bordered, rounded (standard table look).
-->
<div class="w-full md:bg-white md:border md:border-gray-200 md:shadow-sm md:rounded-lg md:overflow-hidden">

    <table class="w-full text-left text-sm md:text-base border-separate md:border-collapse space-y-4 md:space-y-0">
        
        <!-- HEADER: Hidden on mobile -->
        <thead class="hidden md:table-header-group bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Heading</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right md:text-left">Action</th>
            </tr>
        </thead>
        
        <tbody class="block md:table-row-group">
            @forelse($posts as $post)
            <!-- CARD ROW: Stacked on mobile, Table Row on desktop -->
            <tr class="flex flex-col md:table-row bg-white rounded-xl shadow-sm border border-gray-200 mb-4 md:mb-0 md:border-b md:border-gray-100 md:shadow-none hover:bg-gray-50 transition relative overflow-hidden">
                
                <!-- 1. HEADING -->
                <td class="order-1 md:order-0 block md:table-cell p-5 pb-2 md:p-4 text-gray-900">
                    <div class="text-lg font-bold leading-tight md:text-sm md:font-normal">
                        {{ $post->heading }}
                    </div>
                </td>

                <!-- 2. TYPE -->
                <td class="order-2 md:order-0 block md:table-cell px-5 py-1 md:p-4">
                    <div class="flex justify-between md:block items-center">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Type</span>
                        <span class="capitalize font-medium text-gray-700">{{ $post->latestOutput->output_type ?? 'N/A' }}</span>
                    </div>
                </td>

                <!-- 3. CATEGORY -->
                <td class="order-3 md:order-0 block md:table-cell px-5 py-1 md:p-4">
                    <div class="flex justify-between md:block items-center">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Category</span>
                        <span class="font-medium text-gray-700">{{ $post->category->name ?? '-' }}</span>
                    </div>
                </td>

                <!-- 4. STATUS -->
                <td class="order-4 md:order-0 block md:table-cell px-5 py-1 md:p-4">
                    <div class="flex justify-between md:block items-center">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Status</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border inline-block
                            {{ ($post->status ?? 'draft') === 'processed'
                                ? 'bg-green-50 text-green-700 border-green-200'
                                : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                            {{ ucfirst($post->status ?? 'draft') }}
                        </span>
                    </div>
                </td>

                <!-- 5. CREATED -->
                <td class="order-5 md:order-0 block md:table-cell px-5 py-1 md:p-4">
                    <div class="flex justify-between md:block items-center">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Created</span>
                        <span class="text-gray-500 font-medium">{{ $post->created_at->format('d M Y') }}</span>
                    </div>
                </td>

                <!-- 6. ACTION -->
                <td class="order-last md:order-0 block md:table-cell border-t border-gray-100 md:border-none mt-3 md:mt-0 p-0 md:p-4 bg-gray-50 md:bg-transparent">
                    <a href="{{ route('posts.download', $post->id) }}"
                       class="block w-full py-3 md:py-0 text-center md:text-left text-sm font-semibold text-blue-600 hover:text-blue-800 md:text-black md:underline md:bg-transparent">
                        Preview Output
                    </a>
                </td>

            </tr>
            @empty
                <tr class="flex md:table-row bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-0">
                    <td colspan="6" class="w-full text-center p-8 text-gray-500 block md:table-cell">
                        No posts found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Desktop Pagination: Shows inside the box -->
    <div class="hidden md:block px-4 py-3 border-t border-gray-200 bg-gray-50">
        {{ $posts->links() }}
    </div>

</div>

<!-- Mobile Pagination: Shows outside the box (better for touch scrolling) -->
<div class="md:hidden mt-4 pb-10">
    {{ $posts->links() }}
</div>

@endsection