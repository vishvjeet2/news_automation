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

    <!-- Search Bar -->
    <input
    type="text"
    id="search"
    placeholder="Search posts..."
    class="w-full sm:w-72 px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black">

</div>

<!-- 3. Responsive Table Container -->
<!-- 
    Mobile: Transparent background (so cards 'float'), no border.
    Desktop: White background, bordered, rounded (standard table look).
-->
<div class="w-full md:bg-white md:border md:border-gray-200 md:shadow-sm md:rounded-lg md:overflow-hidden">

    <div class="w-full bg-gray-50 md:bg-white p-2 md:p-0">

        @include('_getnews')
        
    </div>


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
            
            dom:
            "<'flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4'<'flex items-center'f>>" +
            "t" +
            "<'flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-4'<'text-sm text-gray-500'i><'pagination'p>>",
            
            language: {
                search: "",
                searchPlaceholder: "Search posts...",
                paginate: {
                    previous: "Prev",
                    next: "Next"
                }
            }
        
        });
    
    });
</script>
@endsection
