<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

    <table id="postsTable" class="min-w-full text-sm text-gray-700">
    
    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
    <tr>
    <th class="px-6 py-3 text-left">Heading</th>
    <th class="px-6 py-3 text-left">Type</th>
    <th class="px-6 py-3 text-left">Category</th>
    <th class="px-6 py-3 text-left">Status</th>
    <th class="px-6 py-3 text-left">Created</th>
    <th class="px-6 py-3 text-left">Download</th>
    </tr>
    </thead>
    
    <tbody class="divide-y divide-gray-100">
    
    @foreach($posts as $post)
    
    <tr class="hover:bg-gray-50 transition">
    
    <td class="px-6 py-4 font-medium text-gray-900">
    {{ $post->heading }}
    </td>
    
    <td class="px-6 py-4">
    <span class="font-medium text-gray-600">
    {{ ucfirst($post->news_type ?? '-') }}
    </span>
    </td>
    
    <td class="px-6 py-4 text-gray-600">
    {{ $post->category ?? '-' }}
    </td>
    
    <td class="px-6 py-4">
    
    @if(($post->status ?? 'draft') == 'processed')
    
    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
    Processed
    </span>
    
    @else
    
    <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
    Draft
    </span>
    
    @endif
    
    </td>
    
    <td class="px-6 py-4 text-gray-500">
    {{ $post->created_at->format('d M Y') }}
    </td>
    
    <td class="px-6 py-4">
    <a href="{{ route('posts.download',$post->id) }}"
    class="text-blue-600 hover:text-blue-800 font-medium underline">
    Preview
    </a>
    </td>
    
    </tr>
    
    @endforeach
    
    </tbody>
    
    </table>
    
    </div>