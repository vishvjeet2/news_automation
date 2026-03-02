@extends('layouts.app')

@section('title', 'File Preview')

@section('content')

<div class="max-w-xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">

        <h2 class="text-xl font-semibold mb-6">
            Your File is Ready ✅
        </h2>

        <img src="{{ asset($image) }}"
             class="mx-auto rounded-lg shadow-sm max-w-sm">

        <a href="{{ asset($image) }}" download
           class="inline-block mt-6 px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition">
            ⬇ Download
        </a>

        <div class="mt-6">
            <a href="{{ route('dashboard') }}"
               class="text-gray-600 hover:underline">
                ← Back to Dashboard
            </a>
        </div>

    </div>

</div>

@endsection