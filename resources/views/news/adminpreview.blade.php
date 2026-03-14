@extends('Admin.layouts.app')

@section('title', 'File Preview')

@section('content')

<!-- Added 'px-4' for side spacing on mobile and 'w-full' to ensure it takes width -->
<div class="w-full max-w-xl mx-auto px-4 md:px-0">

    <!-- Adjusted padding: p-6 on mobile, p-8 on desktop -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 text-center">

        <h2 class="text-xl md:text-2xl font-semibold mb-6 text-gray-800">
            Your File is Ready ✅
        </h2>

        <!-- 
            Image Fixes:
            1. w-full: Makes image responsive (shrinks on small screens).
            2. h-auto: Maintains aspect ratio.
            3. max-w-sm: Prevents it from getting too big on desktop.
        -->
        <div class="flex justify-center">
            <img src="{{ asset($image) }}" 
                 alt="Preview"
                 class="rounded-lg shadow-sm w-full h-auto max-w-xs md:max-w-sm object-contain border border-gray-100">
        </div>

        <a href="{{ asset($image) }}" download
        class="inline-block mt-8 px-6 py-3 bg-black text-white font-medium rounded-lg shadow hover:bg-gray-800 transition transform active:scale-95 w-full sm:w-auto">
        ⬇ Download Image
        </a>

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}"
               class="text-sm text-gray-600 hover:text-black hover:underline transition">
                ← Back to Dashboard
            </a>
        </div>

    </div>

</div>

@endsection