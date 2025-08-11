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
                        <h3 class="mb-0">Users Management</h3>
                        <div>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-pen"></i> Posts
                            </a>
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New User
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

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-body">
                                    <tr id="loading-row">
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center" id="pagination-container">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const usersTableBody = document.getElementById('users-table-body');
        const loadingRow = document.getElementById('loading-row');
        const paginationContainer = document.getElementById('pagination-container');

        function fetchUsers(url = '/api/users') {
            axios.get(url)
                .then(function (response) {
                    const users = response.data.data;
                    const links = response.data.links;
                    const meta = response.data.meta;

                    usersTableBody.innerHTML = ''; // Clear existing rows

                    if (users.length === 0) {
                        usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No users found.</td></tr>';
                    } else {
                        users.forEach(user => {
                            const row = `
                                <tr>
                                    <td>${user.id}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>${new Date(user.created_at).toLocaleString()}</td>
                                    <td>${new Date(user.updated_at).toLocaleString()}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="/users/${user.id}" class="btn btn-sm btn-info me-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/users/${user.id}/edit" class="btn btn-sm btn-warning me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/users/${user.id}" method="POST" class="me-2" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <a href="/posts/create?user_id=${user.id}" class="btn btn-sm btn-success">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            usersTableBody.innerHTML += row;
                        });
                    }
                    
                    renderPagination(links, meta);
                })
                .catch(function (error) {
                    console.error('Error fetching users:', error);
                    usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Error loading users.</td></tr>';
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
                        fetchUsers(link.url);
                    });
                }

                li.appendChild(a);
                ul.appendChild(li);
            });

            nav.appendChild(ul);
            paginationContainer.appendChild(nav);
        }

        fetchUsers();
    });
</script>

</body>
</html> 