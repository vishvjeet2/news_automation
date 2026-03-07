@extends('Admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<!-- Custom CSS to fix DataTables styling on mobile cards -->
<style>
    /* Remove the default DataTables border on mobile to make cards look clean */
    @media (max-width: 768px) {
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
    }
</style>

<!-- Stats Grid (Unchanged) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Total Posts</p>
        <p id="stat-total" class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Published</p>
        <p id="stat-published" class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['published'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Drafts</p>
        <p id="stat-drafts" class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['drafts'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Users</p>
        <p id="stat-users" class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['users'] }}</p>
    </div>
</div>

<!-- Action Bar -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <a href="{{ route('admin.posts.create') }}"
    class="inline-block bg-black text-white px-6 py-3 rounded-md shadow hover:bg-gray-800 transition">
        + Create New Post
    </a>
</div>

<!-- Table Section -->
<div class="w-full">
    @include('Admin._getnewsadmin')
</div>       

<script>
function toggleStatus(id) {
    fetch('/admin/posts/' + id + '/toggle-status', {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        const btn = document.getElementById(`status-btn-${id}`);
        btn.innerText = data.label;
        
        let drafts = document.getElementById('stat-drafts');
        let published = document.getElementById('stat-published');

        if (data.status === 'processed') {
            btn.className = "px-2.5 py-1 rounded-full text-xs font-medium border bg-green-50 text-green-700 border-green-200";
            drafts.innerText = parseInt(drafts.innerText) - 1;
            published.innerText = parseInt(published.innerText) + 1;
        } else {
            btn.className = "px-2.5 py-1 rounded-full text-xs font-medium border bg-yellow-50 text-yellow-700 border-yellow-200";
            drafts.innerText = parseInt(drafts.innerText) + 1;
            published.innerText = parseInt(published.innerText) - 1;
        }
    });
}
    
$(document).ready(function(){
    $('#adminPostsTable').DataTable({
        pageLength: 5,
        lengthChange: false, // Hides the "Show 10 entries" dropdown for cleaner mobile UI
        ordering: true,
        
        // Disable plugins that conflict with custom CSS
        responsive: false, 
        scrollX: false,
        autoWidth: false,

        // This 'dom' structure ensures the search box and pagination
        // stack vertically on mobile and sit side-by-side on desktop
        dom: "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'f>" +
             "t" +
             "<'flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm'i'p>",
        
        language:{
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