@extends('layouts.app')

@section('title', 'Create Post')

@section('content')

<div class="max-w-2xl mx-auto">

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
            Create Media
        </h2>

        <form id="mediaForm"
              method="POST"
              action="{{ route('posts.generate') }}"
              enctype="multipart/form-data"
              class="space-y-6">

            @csrf

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select News Category
                </label>

                <select id="newscatogary"
                        name="category_id"
                        required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Template -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Template
                </label>

                <select id="templateType"
                        name="template_type"
                        required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                    <option value="">-- Select Template --</option>
                    @foreach ($templetName as $name)
                        <option value="{{ $name->id }}">
                            {{ $name->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Dynamic Fields -->
            <div id="dynamicFields" class="space-y-5"></div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-black text-white py-3 rounded-md hover:bg-gray-800 transition shadow-sm">
                Generate
            </button>

        </form>

    </div>

</div>

<!-- Script Same As Before -->
<script>

function commonFields(){
    return `
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Heading</label>
            <input type="text" name="heading"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
            <div class="text-sm text-red-500 mt-1" id="heading_error"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <input type="text" name="description"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
            <div class="text-sm text-red-500 mt-1" id="description_error"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">City Name</label>
            <input type="text" name="city"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
            <div class="text-sm text-red-500 mt-1" id="city_error"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hashtag</label>
            <input type="text" name="hashtag"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
            <div class="text-sm text-red-500 mt-1" id="hashtag_error"></div>
        </div>
    `;
}

document.getElementById("templateType").addEventListener("change", function(){

    let type = this.value;
    let fields = commonFields();

    if(type === "2"){
        fields += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                <input type="file" name="image"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                <div class="text-sm text-red-500 mt-1" id="image_error"></div>
            </div>
        `;
    }

    if(type === "4"){
        fields += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Video</label>
                <input type="file" name="video"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                <div class="text-sm text-red-500 mt-1" id="video_error"></div>
            </div>
        `;
    }

    document.getElementById("dynamicFields").innerHTML = fields;
});

</script>

@endsection