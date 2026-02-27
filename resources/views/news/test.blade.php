@extends('layouts.app')

@section('title', 'Add Category')

@section('content')

<div class="max-w-xl mx-auto">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-black transition">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8">

        <h2 class="text-xl font-semibold text-black mb-6">
            Add Category
        </h2>

        <form action="{{ route('category.store') }}"
              method="POST"
              class="space-y-6">

            @csrf

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name
                </label>

                <input type="text"
                       name="name"
                       placeholder="Enter category name"
                       class="w-full border border-gray-300 rounded-md px-4 py-2
                              focus:outline-none focus:border-black focus:ring-0">
            </div>

            <!-- Slug -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Slug
                </label>

                <input type="text"
                       name="slug"
                       placeholder="Enter slug (e.g. politics-news)"
                       class="w-full border border-gray-300 rounded-md px-4 py-2
                              focus:outline-none focus:border-black focus:ring-0">
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-black text-white py-3 rounded-md
                           hover:bg-gray-800 transition shadow-sm">
                Save Category
            </button>

        </form>

    </div>

</div>

@endsection