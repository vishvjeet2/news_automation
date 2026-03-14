@extends('layouts.app')

@section('title', 'Create Post')

@section('content')

<div class="max-w-2xl mx-auto">

    @include('news._form', [
        'route' => route('posts.generate'),
        'categories' => $categories,
        'templates' => $templates
    ])

</div>

@endsection