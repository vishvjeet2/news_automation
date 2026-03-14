@extends('admin.layouts.app')

@section('title', 'Manage Templates')

@section('content')

    <div class="max-w-4xl mx-auto">

        @if (session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8 mb-8">
            <h2 class="text-lg font-semibold text-black mb-6">Add Template</h2>

            <form method="POST" action="{{ route('admin.template.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Template Name</label>
                    <input type="text" name="name"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black"
                        placeholder="e.g. Breaking Khabar, Badi Khabar">

                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Template Image</label>
                    <input type="file" name="template_path" accept="image/*"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black bg-white">

                    <p class="text-xs text-gray-500 mt-1">
                        Accepted formats: JPG, PNG. 
                    </p>

                    @error('template_path')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition">
                    Save Template
                </button>

            </form>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-medium text-gray-600">#</th>
                        <th class="p-4 text-sm font-medium text-gray-600">Name</th>
                        <th class="p-4 text-sm font-medium text-gray-600">Preview</th>
                        <th class="p-4 text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-4 text-sm text-gray-400">{{ $template->id }}</td>
                            <td class="p-4 font-medium text-gray-800">{{ $template->name }}</td>
                            <td class="p-4">
                                @if($template->template_path)
                                    <img src="{{ asset('storage/' . $template->template_path) }}"
                                        alt="{{ $template->name }}"
                                        class="w-40 h-24 object-contain bg-gray-50 rounded-lg border border-gray-200">
                                @else
                                    <span class="text-xs text-gray-400">No image</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.template.delete', $template->id) }}"
                                    onsubmit="return confirm('Delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-red-500 hover:text-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">
                                No templates found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-6">
                {{ $templates->links() }}
            </div>
        </div>

    </div>

@endsection