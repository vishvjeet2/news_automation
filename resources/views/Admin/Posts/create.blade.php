@extends('admin.layouts.app')

@section('title', 'Create Post')

@section('content')

<div class="max-w-2xl mx-auto">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-black transition">
            ← Back to Dashboard
        </a>
    </div>


        
        @include('news._form', [
            'route' => route('admin.post.store'),
            'categories' => $categories,
            'templates' => $templates
        ])

   

</div>

@endsection