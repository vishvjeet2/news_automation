<table class="w-full text-left text-sm md:text-base border-separate md:border-collapse space-y-4 md:space-y-0">
            
    <!-- HEADER: Hidden on mobile -->
    <thead class="hidden md:table-header-group bg-gray-50 border-b border-gray-200">
        <tr>
            <th class="p-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Heading</th>
            <th class="p-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Type</th>
            <th class="p-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Category</th>
            <th class="p-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="p-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Created</th>
            <th class="p-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Action</th>
        </tr>
    </thead>
    
    <tbody class="block md:table-row-group" >
        @foreach($posts as $post)
<!-- CARD ROW: Flex container on mobile, Table row on desktop -->
<tr class="flex flex-col md:table-row bg-white rounded-xl shadow-sm border border-gray-200 mb-4 md:mb-0 md:border-b md:border-gray-100 md:shadow-none hover:bg-gray-50 transition relative overflow-hidden">
    
    <!-- 1. HEADING (Card Title) -->
    <td class="order-1 md:order-none block md:table-cell p-5 pb-2 md:p-4 text-gray-900">
        <!-- Mobile: Large Title, No Label -->
        <div class="text-lg font-bold leading-tight md:text-sm md:font-normal">
            {{ $post->heading }}
        </div>
    </td>

    <!-- 2. META DATA WRAPPER (Mobile only visual grouping) -->
    <!-- We used 'contents' to allow cells to behave normally on desktop but we style individual cells for mobile -->

    <!-- TYPE -->
    <td class="order-2 md:order-none block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Type</span>
            <span class="capitalize font-medium text-gray-700">{{ $post->latestOutput->output_type ?? 'N/A' }}</span>
        </div>
    </td>

    <!-- CATEGORY -->
    <td class="order-3 md:order-none block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Category</span>
            <span class="font-medium text-gray-700">{{ $post->category->name ?? '-' }}</span>
        </div>
    </td>

    <!-- STATUS -->
    <td class="order-4 md:order-none block md:table-cell px-5 py-1 md:p-4">
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

    <!-- CREATED -->
    <td class="order-5 md:order-none block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider md:hidden">Created</span>
            <span class="text-gray-500 font-medium">{{ $post->created_at->format('d M Y') }}</span>
        </div>
    </td>

    <!-- 6. ACTION (Card Footer) -->
    <td class="order-last md:order-none block md:table-cell border-t border-gray-100 md:border-none mt-3 md:mt-0 p-0 md:p-4 bg-gray-50 md:bg-transparent">
        <a href="{{ route('posts.download', $post->id) }}"
        class="block w-full py-3 md:py-0 text-center md:text-left text-sm font-semibold text-blue-600 hover:text-blue-800 md:text-black md:underline md:bg-transparent">
            Preview Output
        </a>
    </td>

</tr>
@endforeach
</tbody>
</table>   

<!--pagination-->
<div class="mt-6">
    {{ $posts->links() }}
</div>