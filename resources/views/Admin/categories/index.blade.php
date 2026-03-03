@extends('admin.layouts.app')

@section('title', 'Manage Categories')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8 mb-8">
        <h2 class="text-lg font-semibold text-black mb-6">Add Category</h2>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-700 mb-2">Name</label>
                <input type="text" name="name"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            </div>

            <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition">
                Save Category
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4 text-sm font-medium text-gray-600">Name</th>
                    <th class="p-4 text-sm font-medium text-gray-600">Slug</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr class="border-b border-gray-100">
                        <td class="p-4">{{ $category->name }}</td>
                        <td class="p-4">{{ $category->slug }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>

</div>

@endsection