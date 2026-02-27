@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Stats -->
<div class="grid grid-cols-4 gap-6 mb-8">

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500">Total Posts</p>
        <p class="text-2xl font-semibold text-black mt-2">{{ $stats['total'] }}</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500">Images</p>
        <p class="text-2xl font-semibold text-black mt-2">{{ $stats['images'] }}</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500">Videos</p>
        <p class="text-2xl font-semibold text-black mt-2">{{ $stats['videos'] }}</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500">Drafts</p>
        <p class="text-2xl font-semibold text-black mt-2">{{ $stats['drafts'] }}</p>
    </div>

</div>

<!-- Create Button -->
<div class="mb-6">
    <a href="{{ route('posts.create') }}"
       class="inline-block bg-black text-white px-6 py-3 rounded-md shadow hover:bg-gray-800 transition">
        + Create New Post
    </a>
</div>

<!-- Posts Table -->
<div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">

    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
            <th class="p-4 text-sm font-medium text-gray-600">Heading</th>
            <th class="p-4 text-sm font-medium text-gray-600">Type</th>
            <th class="p-4 text-sm font-medium text-gray-600">Category</th>
            <th class="p-4 text-sm font-medium text-gray-600">Status</th>
            <th class="p-4 text-sm font-medium text-gray-600">Created</th>
            <th class="p-4 text-sm font-medium text-gray-600">Action</th>
        </tr>
        </thead>
        <tbody>

        @foreach($posts as $post)
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="p-4">{{ $post->heading }}</td>
                <td class="p-4 capitalize">{{ $post->news_type }}</td>
                <td class="p-4">{{ $post->category->name ?? '-' }}</td>
                <td class="p-4">
                    <span class="px-3 py-1 text-sm rounded-full border
                        {{ $post->status === 'processed' ? 'bg-gray-200 text-black border-gray-300' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                        {{ ucfirst($post->status) }}
                    </span>
                </td>
                <td class="p-4">{{ $post->created_at->format('d M Y') }}</td>
                <td class="p-4">
                    <a href="{{ route('posts.download', $post->id) }}"
                       class="text-sm text-black underline hover:text-gray-600">
                        Download
                    </a>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $posts->links() }}
</div>

@endsection