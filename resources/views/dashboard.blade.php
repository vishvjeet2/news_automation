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

    pageLength: 5,
    lengthChange: false,
    ordering: true,

    responsive: false,
    scrollX: false,
    autoWidth: false,

    serverSide: true,
    processing: true,

    ajax: {
        url: "/posts/data",
        type: "GET"
    },

    columns: [
        {
            data: 'heading',
            render: function (data) {
                return `
                    <span class="md:hidden text-xs font-bold text-gray-400 uppercase block mb-1">
                        Heading
                    </span>

                    <span class="font-medium text-gray-900 break-words">
                        ${data}
                    </span>
                `;
            }
        },

        {
            data: 'news_type',
            render: function (data) {
                return `
                    <div class="flex justify-between">

                        <span class="md:hidden font-bold text-gray-600 text-xs uppercase">
                            Type
                        </span>

                        <span class="capitalize">
                            ${data ?? 'N/A'}
                        </span>

                    </div>
                `;
            }
        },

        {
            data: 'category',
            render: function (data) {
                return `
                    <div class="flex justify-between">

                        <span class="md:hidden font-bold text-gray-600 text-xs uppercase">
                            Category
                        </span>

                        <span>
                            ${data ?? '-'}
                        </span>

                    </div>
                `;
            }
        },

        {
            data: 'status',
            render: function (data) {

                let badge = '';

                if (data === 'processed') {
                    badge = `
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium border 
                        bg-green-100 text-green-800 border-green-300">
                            Processed
                        </span>`;
                } else {
                    badge = `
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium border 
                        bg-yellow-100 text-yellow-800 border-yellow-300">
                            Draft
                        </span>`;
                }

                return `
                    <div class="flex justify-between items-center">

                        <span class="md:hidden font-bold text-gray-600 text-xs uppercase">
                            Status
                        </span>

                        ${badge}

                    </div>
                `;
            }
        },

        {
            data: 'date',
            render: function (data) {
                return `
                    <div class="flex justify-between">

                        <span class="md:hidden font-bold text-gray-600 text-xs uppercase">
                            Date
                        </span>

                        <span class="text-gray-500">
                            ${data}
                        </span>

                    </div>
                `;
            }
        },

        {
            data: 'action',
            render: function (data) {
                return `
                    <div class="flex justify-between">

                        <span class="md:hidden font-bold text-gray-600 text-xs uppercase">
                            Action
                        </span>

                        ${data}

                    </div>
                `;
            }
        }

    ],

    createdRow: function (row) {

        $(row).addClass(`
            block md:table-row
            bg-white
            rounded-lg
            shadow-sm
            border
            border-gray-200
            mb-4
            md:mb-0
            md:border-none
            md:shadow-none
            hover:bg-gray-50
        `);

        $('td', row).addClass(`
            block md:table-cell
            p-4
            border-b
            md:border-b-0
            border-gray-100
        `);

    },

    dom:
    "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'f>" +
    "t" +
    "<'flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm'i'p>",

    language: {
        search: "",
        searchPlaceholder: "Search posts...",
        info: "Showing _START_ to _END_ of _TOTAL_ posts",
        paginate: {
            next: "Next >",
            previous: "< Prev"
        }
    }

});

});
</script>
@endsection
