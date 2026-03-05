@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Stats -->

<div class="grid gap-6 mb-8
            grid-cols-1
            sm:grid-cols-2
            lg:grid-cols-4">

<div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6 max-w-md w-full">
    <p class="text-sm text-gray-500">Total Posts</p>
    <p class="text-2xl font-semibold text-black mt-2">{{ $stats['total'] }}</p>
</div>

<div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6 max-w-md w-full">
    <p class="text-sm text-gray-500">Images</p>
    <p class="text-2xl font-semibold text-black mt-2">{{ $stats['images'] }}</p>
</div>

<div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6 max-w-md w-full">
    <p class="text-sm text-gray-500">Videos</p>
    <p class="text-2xl font-semibold text-black mt-2">{{ $stats['videos'] }}</p>
</div>

<div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6 max-w-md w-full">
    <p class="text-sm text-gray-500">Drafts</p>
    <p class="text-2xl font-semibold text-black mt-2">{{ $stats['drafts'] }}</p>
</div>


</div>

<!-- Create Button + Search -->

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

<!-- Posts Table -->

<div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">


    <div class="w-full bg-gray-50 md:bg-white p-2 md:p-0" id="posts-container">
        
           
    </div>


</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function(){
    

        loaddata(query='');

        function loaddata(query)
           {
            $.ajax({
                url: "{{ url('dashboard/search') }}",
                type: "GET",
                data: {
                    search: query
                },
                success: function(response){
    
                    console.log("Controller reached");
                    console.log(response);
    
                    $('#posts-container').html(response);
    
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
           }

        $('#search').on('keyup', function(){
    
            let query = $(this).val();
    
            console.log("Typing:", query);
            loaddata(query);
            
          
    
        });
    
    });
    </script>
@endsection
