@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Posts Management</h3>
                    <div>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-users"></i> Users
                        </a>
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Post
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Author</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="posts-table-body">
                                <tr id="loading-row">
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="pagination-container">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const postsTableBody = document.getElementById('posts-table-body');
        const loadingRow = document.getElementById('loading-row');
        const paginationContainer = document.getElementById('pagination-container');
        const alertContainer = document.getElementById('alert-container');

        function showAlert(message, type) {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        function deletePost(postId, postTitle) {
            if (!confirm(`Are you sure you want to delete post "${postTitle}"?`)) {
                return;
            }

            const deleteBtn = document.querySelector(`[data-delete-post="${postId}"]`);
            const originalContent = deleteBtn.innerHTML;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            axios.delete(`/api/posts/${postId}`)
                .then(function (response) {
                    showAlert('Post deleted successfully!', 'success');
                    fetchPosts(); // Reload the table
                })
                .catch(function (error) {
                    const message = error.response?.data?.message || 'An error occurred while deleting the post.';
                    showAlert(message, 'danger');
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalContent;
                });
        }

        function fetchPosts(url = '/api/posts') {
            axios.get(url)
                .then(function (response) {
                    const posts = response.data.data;
                    const links = response.data.links;
                    const meta = response.data.meta;

                    postsTableBody.innerHTML = ''; // Clear existing rows

                    if (posts.length === 0) {
                        postsTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No posts found.</td></tr>';
                    } else {
                        posts.forEach(post => {
                            const row = `
                                <tr>
                                    <td>${post.id}</td>
                                    <td><a href="/posts/${post.id}">${post.title}</a></td>
                                    <td>${post.content.substring(0, 50)}...</td>
                                    <td>${post.author ? post.author.name : 'N/A'}</td>
                                    <td>${new Date(post.created_at).toLocaleString()}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="/posts/${post.id}/edit" class="btn btn-sm btn-warning me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger me-2" 
                                                    data-delete-post="${post.id}" 
                                                    onclick="deletePost(${post.id}, '${post.title}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            postsTableBody.innerHTML += row;
                        });
                    }
                    
                    renderPagination(links, meta);
                })
                .catch(function (error) {
                    console.error('Error fetching posts:', error);
                    const message = error.response?.data?.message || 'Error loading posts.';
                    showAlert(message, 'danger');
                    postsTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Error loading posts.</td></tr>';
                });
        }

        function renderPagination(links, meta) {
            paginationContainer.innerHTML = '';
            
            const nav = document.createElement('nav');
            const ul = document.createElement('ul');
            ul.classList.add('pagination');

            meta.links.forEach(link => {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (link.active) {
                    li.classList.add('active');
                }
                if (!link.url) {
                    li.classList.add('disabled');
                }

                const a = document.createElement('a');
                a.classList.add('page-link');
                a.href = link.url ? link.url : '#';
                a.innerHTML = link.label;

                if (link.url) {
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        fetchPosts(link.url);
                    });
                }

                li.appendChild(a);
                ul.appendChild(li);
            });

            nav.appendChild(ul);
            paginationContainer.appendChild(nav);
        }

        // Make deletePost function globally available
        window.deletePost = deletePost;

        fetchPosts();
    });
</script>
@endsection 