@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>👥 customer Management</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('addUserModal')" class="btn-add">➕ Add New customer</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge badge-primary">Admin</span>
                            @else
                                <span class="badge badge-info">customer</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td class="actions">
                            <button onclick="openModal('editUserModal{{ $user->id }}')" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="openModal('deleteUserModal{{ $user->id }}')" class="btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            <h2>Add New customer</h2>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal('addUserModal')" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Add customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
        <div id="editUserModal{{ $user->id }}" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editUserModal{{ $user->id }}')">&times;</span>
                <h2>Edit customer - {{ $user->name }}</h2>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="form-group">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeModal('editUserModal{{ $user->id }}')"
                            class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-submit">Update customer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div id="deleteUserModal{{ $user->id }}" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('deleteUserModal{{ $user->id }}')">&times;</span>
                <h2>Confirm Delete</h2>
                <p>Are you sure you want to delete customer <strong>{{ $user->name }}</strong>?</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                    @csrf
                    @method('DELETE')
                    <div class="form-actions">
                        <button type="button" onclick="closeModal('deleteUserModal{{ $user->id }}')"
                            class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-delete">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach



    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
@endsection