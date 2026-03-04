@extends('admin.layouts.app')

@section('title', 'Manage Categories')

@section('content')

    <div class="max-w-3xl mx-auto">

        @if (session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8 mb-8">
            <h2 class="text-lg font-semibold text-black mb-6">Add Category</h2>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="categoryName"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">

                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug" id="categorySlug"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">

                    <p class="text-xs text-gray-500 mt-1">
                        Note: Slug must be unique. It will be used in URLs.
                    </p>

                    @error('slug')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition">
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
                    @forelse($categories as $category)
                        <tr class="border-b border-gray-100">
                            <td class="p-4">{{ $category->name }}</td>
                            <td class="p-4">{{ $category->slug }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-6 text-center text-gray-400">
                                No categories found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $categories->links() }}
        </div>

    </div>

@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {

    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');

    if (!nameInput || !slugInput) return;

    nameInput.addEventListener('input', function () {

        let slug = nameInput.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        slugInput.value = slug;
    });

});
</script>