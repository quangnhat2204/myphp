@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Post Details</h3>
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>
                    
                    <h1 class="card-title">{{ $post->title }}</h1>
                    <p class="card-text">{{ $post->content }}</p>
                    <hr>
                    <p><strong>Author:</strong> {{ $post->author->name ?? 'N/A' }}</p>
                    <p><strong>Created At:</strong> {{ $post->created_at->format('d M Y, H:i') }}</p>
                    
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Post
                        </a>
                        <button type="button" class="btn btn-danger" id="delete-btn" onclick="deletePost()">
                            <i class="fas fa-trash"></i> Delete Post
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }

    function deletePost() {
        if (!confirm('Are you sure you want to delete this post?')) {
            return;
        }

        const deleteBtn = document.getElementById('delete-btn');
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

        axios.delete('/api/posts/{{ $post->id }}')
            .then(function (response) {
                showAlert('Post deleted successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("posts.index") }}';
                }, 1500);
            })
            .catch(function (error) {
                const message = error.response?.data?.message || 'An error occurred while deleting the post.';
                showAlert(message, 'danger');
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Post';
            });
    }
</script>
@endsection
