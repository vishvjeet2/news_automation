@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<!-- 1. Stats Grid -->
<!-- Uses grid-cols-1 for mobile, 2 for tablet, 4 for desktop -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
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
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <!-- Create Button -->
    <a href="{{ route('posts.create') }}"
       class="inline-block bg-black text-white px-6 py-3 rounded-md shadow hover:bg-gray-800 transition">
        + Create New Post
    </a>

</div>

<!-- 3. Responsive Table Container -->
<!-- 
    Mobile: Transparent background (so cards 'float'), no border.
    Desktop: White background, bordered, rounded (standard table look).
-->


    <div class="w-full">

        @include('_getnews')
        
    </div>






<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<script>
    $(document).ready(function () {
    
        $('#postsTable').DataTable({
        
            pageLength: 10,
            lengthChange: false,
            ordering: true,

            responsive: false, 
            scrollX: false,
            autoWidth: false,
            
            dom:
            "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'f>" +
             "t" +
             "<'flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm'i'p>",
            
            language: {
                search: "",
                searchPlaceholder: "Search posts...",
                info: "Showing _START_ to _END_ of _TOTAL_ posts",
                paginate: {
                    next: 'Next >',
                    previous: '< Prev'
                }
            }
        
        });
    
    });
</script>
@endsection
