@extends('layouts.app')

@section('title', 'File Preview')

@section('content')

<div class="w-full max-w-xl mx-auto md:px-0">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:8 text-center">

        <h2 class="text-xl md:text-2xl font-semibold mb-6 text-gray-800">
            Your File is Ready ✅
        </h2>
        
        <div class="flex justify-center">

            <img src="{{ asset($image) }}" 
            alt="preview" 
            class=" rounded-lg shadow-sm w-full h-auto max-w-xs md:max-w-sm objext-contain border border-gray-100">
        </div>

        <a href="{{ asset($image) }}" download
        class="inline-block mt-8 px-6 py-3 bg-black text-white rounded-lg shadow-lg shadow hover:bg-gray-800 transition transform active:scale-95 w-full sm:w-auto">
        ⬇ Download
        </a>

        <div class="mt-6">
            <a href="{{ route('dashboard') }}"
               class="text-sm text-gray-600 hover:text-black  hover:underline transition">
                ← Back to Dashboard
            </a>
        </div>

    </div>

</div>

@endsection