

    <table id="adminPostsTable" class="min-w-[800px] w-full text-left text-sm md:text-base">
                <!-- Desktop Header -->
        <thead class="hidden md:table-header-group bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Heading</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Download</th>
            </tr>
        </thead>
    
        <tbody>
    
        @forelse($posts as $post)
    
        <tr class="bg-white hover:bg-gray-50 border-b border-gray-100">
    
            <td class="p-4 font-medium text-gray-900">
                {{ $post->heading }}
            </td>
            
            <td class="p-4 capitalize">
                {{ $post->news_type ?? 'N/A' }}
            </td>
            
            <td class="p-4">
                {{ $post->category ?? '-' }}
            </td>
            
            <td class="p-4">
            
                <button onclick="toggleStatus({{ $post->id }})" id="status-btn-{{ $post->id }}" class="px-2 py-1 rounded-full text-xs border
                    {{ ($post->status ?? 'draft') === 'processed'
                    ? 'bg-green-50 text-green-700 border-green-200'
                    : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                    
                    {{ ucfirst($post->status ?? 'draft') }}
                
                </button>
            
            </td>
            
            <td class="p-4">
                {{ $post->admin->name ?? $post->user->name ?? 'Unknown' }}
            </td>
            
            <td class="p-4 text-gray-500">
                {{ $post->created_at->format('d M Y') }}
            </td>
            
            <td class="p-4">
                <a href="{{ route('admin.post.download',$post->id) }}" class="text-blue-600 hover:underline">
                    Preview
                </a>
            </td>
            
            </tr>
    
        @empty
    
        <tr>
            <td colspan="7" class="text-center p-8 text-gray-500">
                No posts available
            </td>
        </tr>
    
        @endforelse
    
        </tbody>
    
    </table>



<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

