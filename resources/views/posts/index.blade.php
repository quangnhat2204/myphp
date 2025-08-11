<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
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
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const postsTableBody = document.getElementById('posts-table-body');
        const loadingRow = document.getElementById('loading-row');
        const paginationContainer = document.getElementById('pagination-container');

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
                                            <form action="/posts/${post.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

        fetchPosts();
    });
</script>

</body>
</html> 