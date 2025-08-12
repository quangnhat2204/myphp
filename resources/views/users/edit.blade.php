<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Edit User</h3>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <form id="edit-user-form">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" 
                                       id="name" name="name" value="{{ $user->name }}" required>
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" 
                                       id="email" name="email" value="{{ $user->email }}" required>
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-save"></i> Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('edit-user-form');
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
                    name: formData.get('name'),
                    email: formData.get('email')
                };

                axios.put('/api/users/{{ $user->id }}', data)
                    .then(function (response) {
                        showAlert('User updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("users.index") }}';
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
                            const message = error.response?.data?.message || 'An error occurred while updating the user.';
                            showAlert(message, 'danger');
                        }
                    })
                    .finally(function () {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update User';
                    });
            });
        });
    </script>
</body>
</html> 