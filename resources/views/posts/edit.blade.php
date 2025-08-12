@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Edit Post</h3>
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>

                    <form id="edit-post-form">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" 
                                   id="title" name="title" value="{{ $post->title }}" required>
                            <div class="invalid-feedback" id="title-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" 
                                      id="content" name="content" rows="5" required>{{ $post->content }}</textarea>
                            <div class="invalid-feedback" id="content-error"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Update Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-post-form');
        const submitBtn = document.getElementById('submit-btn');
        const alertContainer = document.getElementById('alert-container');

        function showAlert(message, type) {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        function clearErrors() {
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(error => {
                error.textContent = '';
            });
        }

        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(fieldName + '-error');
            field.classList.add('is-invalid');
            errorDiv.textContent = message;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            clearErrors();
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

            const formData = new FormData(form);
            const data = {
                title: formData.get('title'),
                content: formData.get('content')
            };

            axios.put('/api/posts/{{ $post->id }}', data)
                .then(function (response) {
                    showAlert('Post updated successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("posts.index") }}';
                    }, 1500);
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 422) {
                        // Validation errors
                        const errors = error.response.data.errors;
                        Object.keys(errors).forEach(field => {
                            showFieldError(field, errors[field][0]);
                        });
                        showAlert('Please fix the validation errors.', 'danger');
                    } else {
                        // Other errors
                        const message = error.response?.data?.message || 'An error occurred while updating the post.';
                        showAlert(message, 'danger');
                    }
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Post';
                });
        });
    });
</script>
@endsection
