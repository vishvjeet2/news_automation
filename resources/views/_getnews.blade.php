    <div class="w-full">

        <table id="postsTable" class="w-full text-left text-sm ">
        
        <thead class=" hidden md:table-header-group bg-gray-50 border-b border-gray-200">
        <tr>
        <th class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Heading</th>
        <th class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
        <th class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
        <th class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
        <th class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
        <th class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Download</th>
        </tr>
        </thead>
        
        <tbody class="md:bg-white md:divide-y md:divide-gray-100 block md:table-row-group">
        
        @foreach($posts as $post)
        
        <!-- 
            \---------------------------------------------------------------------------------------------------
            \ ROW: On mobile, it's a Card (block, border, shadow, margin). On desktop, it's a table-row.
            \---------------------------------------------------------------------------------------------------
         -->
        <tr class="block md:table-row ng-white rounded-lg shadow-sm border border-gray-200 mb-4 md:mb-0 md:border-none md:shadow-none hover:bg-gray-50 ">
        
            <!--
                \----------------------------------------
                \ Column 1- Heading 
                \----------------------------------------
            -->
            <td class="block md:table-cell p-4 md:p-4 border-b md:border-b-0 border-gray-100">
                <span class="md:hidden text-xs font-bold text-gray-400 uppercase block mb-1">Heading</span>
                <span class="font-medium text-gray-900">{{ $post->heading }}</span>
            </td>

            <!--
               \----------------------------------------
               \ Column 2- Type 
               \----------------------------------------
            -->
        
            <td class="block md:table-cell p-4 md:p-4 border-b md:border-b-0 border-gray-100 flex justify-between md:table-cell">
                <span class="md:hidden font-bold text-gray-600 text-xs uppercase">Type:</span>
                <span class="capitalize">{{ $post->news_type ?? 'N/A' }}</span>
            </td>   

            <!--
               \----------------------------------------
               \ Column 3- Category 
               \----------------------------------------
            -->
        
                <td class="block md:table-cell p-4 md:p-4 border-b md:border-b-0 border-gray-100 flex justify-between md:table-cell">
                    <span class="md:hidden font-bold text-gray-600 text-xs uppercase">Category:</span>
                    <span>{{ $post->category ?? '-' }}</span>
                </td>

            <!--
               \----------------------------------------
               \ Column 4- status 
               \----------------------------------------
            -->
        
        <td class="block md:table-cell p-4 md:p-4 border-b md:border-b-0 border-gray-100 flex justify-between md:table-cell items-center">
            <span class="md:hidden font-bold text-gray-600 text-xs uppercase">Status:</span>
        @if(($post->status ?? 'draft') == 'processed')
        
        <span class="px-2.5 py-1 rounded-full text-xs font-medium border">
        Processed
        </span>
        
        @else
        
        <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-yellow-50 text-yellow-700 border-yellow-200">
        Draft
        </span>
        
        @endif
        
        </td>

        <!--
               \----------------------------------------
               \ Column 5- status 
               \----------------------------------------
            -->
        
            <td class="block md:table-cell p-4 md:p-4 border-b md:border-b-0 border-gray-100 flex justify-between md:table-cell">
                <span class="md:hidden font-bold text-gray-600 text-xs uppercase">Date:</span>
                <span class="text-gray-500">{{ $post->created_at->format('d M Y') }}</span>
            </td>

            <!--
               \----------------------------------------
               \ Column 5- status 
               \----------------------------------------
            -->
        
        <td class="block md:table-cell p-4 md:p-4 border-gray-100 flex justify-between md:table-cell">
            <span class="md:hidden font-bold text-gray-600 text-xs uppercase">Action:</span>
        <a href="{{ route('posts.download',$post->id) }}"
            class="text-blue-600 hover:underline">
        Preview
        </a>
        </td>
        
        </tr>
        
        
        @endforeach
        
        </tbody>
        
        </table>
        
    </div>
