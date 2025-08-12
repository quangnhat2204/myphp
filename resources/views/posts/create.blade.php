@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Create New Post</h3>
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>

                    <form id="create-post-form">
                        @if(isset($user_id))
                            <input type="hidden" name="author_id" value="{{ $user_id }}">
                        @endif
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" 
                                   id="title" name="title" required>
                            <div class="invalid-feedback" id="title-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" 
                                      id="content" name="content" rows="5" required></textarea>
                            <div class="invalid-feedback" id="content-error"></div>
                        </div>
                        @if(!isset($user_id))
                        <div class="mb-3">
                            <label for="author_id" class="form-label">Author</label>
                            <select class="form-control" id="author_id" name="author_id" required>
                                <option value="">Select an author</option>
                            </select>
                            <div class="invalid-feedback" id="author_id-error"></div>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Create Post
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
        const form = document.getElementById('create-post-form');
        const submitBtn = document.getElementById('submit-btn');
        const alertContainer = document.getElementById('alert-container');
        const authorSelect = document.getElementById('author_id');

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

        // Load users for author selection if needed
        if (authorSelect) {
            axios.get('/api/users')
                .then(function (response) {
                    const users = response.data.data;
                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        authorSelect.appendChild(option);
                    });
                })
                .catch(function (error) {
                    console.error('Error loading users:', error);
                });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            clearErrors();
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

            const formData = new FormData(form);
            const data = {
                title: formData.get('title'),
                content: formData.get('content'),
                author_id: formData.get('author_id')
            };

            axios.post('/api/posts', data)
                .then(function (response) {
                    showAlert('Post created successfully!', 'success');
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
                        const message = error.response?.data?.message || 'An error occurred while creating the post.';
                        showAlert(message, 'danger');
                    }
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Create Post';
                });
        });
    });
</script>
@endsection
