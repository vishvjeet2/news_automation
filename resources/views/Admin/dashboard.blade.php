@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-6 mb-8">

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
            <p class="text-sm text-gray-500">Total Posts</p>
            <p class="text-2xl font-semibold text-black mt-2">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
            <p class="text-sm text-gray-500">Published</p>
            <p class="text-2xl font-semibold text-black mt-2">{{ $stats['published'] }}</p>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
            <p class="text-sm text-gray-500">Drafts</p>
            <p class="text-2xl font-semibold text-black mt-2">{{ $stats['drafts'] }}</p>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
            <p class="text-sm text-gray-500">Users</p>
            <p class="text-2xl font-semibold text-black mt-2">{{ $stats['users'] }}</p>
        </div>

    </div>

    <!-- Create Button -->
    <div class="mb-6">
        <a href="{{ route('admin.posts.create') }}"
            class="inline-block bg-black text-white px-6 py-3 rounded-md shadow hover:bg-gray-800 transition">
            + Create New Post
        </a>
    </div>

    <!-- Latest Posts Table (All Users) -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">

        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4 text-sm font-medium text-gray-600">Heading</th>
                    <th class="p-4 text-sm font-medium text-gray-600">Type</th>
                    <th class="p-4 text-sm font-medium text-gray-600">Category</th>
                    <th class="p-4 text-sm font-medium text-gray-600">Status</th>
                    <th class="p-4 text-sm font-medium text-gray-600">Created By</th>
                    <th class="p-4 text-sm font-medium text-gray-600">Created</th>
                </tr>
            </thead>
            <tbody>

                @forelse($posts as $post)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="p-4">{{ $post->heading }}</td>
                        <td class="p-4 capitalize">{{ $post->latestOutput->output_type ?? 'N/A' }}</td>
                        <td class="p-4">{{ $post->category->name ?? '-' }}</td>
                        <td class="p-4">
                            <button type="button" onclick="toggleStatus({{ $post->id }})"
                                id="status-btn-{{ $post->id }}"
                                class="px-3 py-1 text-sm rounded-full border
                                {{ ($post->status ?? 'draft') === 'processed'
                                    ? 'bg-green-100 text-green-700 border-green-300'
                                    : 'bg-yellow-100 text-yellow-700 border-yellow-300' }}">
                                {{ ucfirst($post->status ?? 'draft') }}
                            </button>
                        </td>
                        <td>
                            @if ($post->admin)
                                {{ $post->admin->name }}
                            @elseif($post->user)
                                {{ $post->user->name }}
                            @else
                                Unknown
                            @endif
                        </td>
                        <td class="p-4">{{ $post->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-400">
                            No posts available
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $posts->links() }}
    </div>

    <script>
        function toggleStatus(id) {
            fetch('/admin/posts/' + id + '/toggle-status', {
                    method: "POST", // because your route is POST
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const btn = document.getElementById(`status-btn-${id}`);
                    btn.innerText = data.label;

                    if (data.status === 'processed') {
                        btn.className =
                            "px-3 py-1 text-sm rounded-full border bg-green-100 text-green-700 border-green-300";
                    } else {
                        btn.className =
                            "px-3 py-1 text-sm rounded-full border bg-yellow-100 text-yellow-700 border-yellow-300";
                    }
                });
        }
    </script>

@endsection
