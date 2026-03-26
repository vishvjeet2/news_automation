<!-- resources/views/editor/list.blade.php -->
@extends('layouts.editor')

@section('title', 'My Designs')

@section('content')
<div class="designs-page">
    <header class="designs-header">
        <div class="header-content">
            <h1><i class="fas fa-palette"></i> My Designs</h1>
            <a href="{{ route('editor.index') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Design
            </a>
        </div>
    </header>
    
    <main class="designs-grid-container">
        @if($designs->count() > 0)
            <div class="designs-grid">
                @foreach($designs as $design)
                    <div class="design-card" data-id="{{ $design->id }}">
                        <div class="design-thumbnail">
                            @if($design->thumbnail)
                                <img src="{{ Storage::url($design->thumbnail) }}" alt="{{ $design->name }}">
                            @else
                                <div class="no-thumbnail">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            <div class="design-overlay">
                                <a href="{{ route('editor.edit', $design) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn-delete" onclick="deleteDesign({{ $design->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="design-info">
                            <h3>{{ $design->name }}</h3>
                            <p>{{ $design->width }} x {{ $design->height }}px</p>
                            <small>Updated {{ $design->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="pagination-container">
                {{ $designs->links() }}
            </div>
        @else
            <div class="no-designs">
                <i class="fas fa-folder-open"></i>
                <h2>No designs yet</h2>
                <p>Create your first design to get started!</p>
                <a href="{{ route('editor.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Design
                </a>
            </div>
        @endif
    </main>
</div>

<script>
function deleteDesign(id) {
    if (confirm('Are you sure you want to delete this design?')) {
        fetch(`/editor/design/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.design-card[data-id="${id}"]`).remove();
            }
        });
    }
}
</script>
@endsection