@extends('layouts.app')

@section('title', 'Add Template')

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
            Add Template
        </h2>

        <form action="{{ route('news.templates.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">

            @csrf

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Type
                </label>

                <select name="type"
                        class="w-full border border-gray-300 rounded-md px-4 py-2
                               focus:outline-none focus:border-black focus:ring-0">
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
            </div>

            <!-- File -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select File
                </label>

                <input type="file"
                       name="file"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2
                              focus:outline-none focus:border-black focus:ring-0">
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-black text-white py-3 rounded-md
                           hover:bg-gray-800 transition shadow-sm">
                Save Template
            </button>

        </form>

    </div>

</div>

@endsection