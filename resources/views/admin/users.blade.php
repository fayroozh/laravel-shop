@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>👥 User Management</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('addUserModal')" class="btn-add">➕ Add User</button>
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
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Account Type</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->is_admin)
                            Admin
                        @elseif($user->isEmployee())
                            Employee
                        @else
                            Regular User
                        @endif
                    </td>
                    <td>{{ $user->created_at?->format('Y-m-d') ?? 'N/A' }}</td>

                    <td>
                        <button onclick="openModal('editUserModal{{ $user->id }}')" class="btn-edit">✏️ Edit</button>
                        <button onclick="openModal('deleteUserModal{{ $user->id }}')" class="btn-delete">🗑️ Delete</button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            <h2>Add New User</h2>
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
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required id="addRoleSelect">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @if($role->name === 'employee') data-is-employee="true" @endif>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee fields -->
                <div id="addEmployeeFields" style="display:none;">
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" name="position" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="mobile" class="form-control">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="closeModal('addUserModal')" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
        <div id="editUserModal{{ $user->id }}" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editUserModal{{ $user->id }}')">&times;</span>
                <h2>Edit User - {{ $user->name }}</h2>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Name</label>
                        <input id="editName{{ $user->id }}" type="text" name="name" class="form-control" required
                            value="{{ old('name', $user->name) }}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input id="editEmail{{ $user->id }}" type="email" name="email" class="form-control" required
                            value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="form-group">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select id="editRoleSelect{{ $user->id }}" name="role" class="form-control" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    data-is-employee="{{ $role->name === 'employee' ? 'true' : 'false' }}"
                                    @if($role->name == $user->roles->pluck('name')->first()) selected @endif>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee fields -->
                    <div id="editEmployeeFields{{ $user->id }}" style="display: none;">
                        <div class="form-group">
                            <label>Position</label>
                            <input id="editPosition{{ $user->id }}" type="text" name="position" class="form-control"
                                value="{{ old('position', $user->employee?->position ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <input id="editDepartment{{ $user->id }}" type="text" name="department" class="form-control"
                                value="{{ old('department', $user->employee?->department ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input id="editPhone{{ $user->id }}" type="text" name="mobile" class="form-control"
                                value="{{ old('mobile', $user->employee?->mobile ?? '') }}">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" onclick="closeModal('editUserModal{{ $user->id }}')"
                            class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Delete User Modals -->
    @foreach($users as $user)
        <div id="deleteUserModal{{ $user->id }}" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('deleteUserModal{{ $user->id }}')">&times;</span>
                <h2>Confirm Delete</h2>
                <p>Are you sure you want to delete user <strong>{{ $user->name }}</strong>?</p>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Add User Modal role select
            const addRoleSelect = document.getElementById('addRoleSelect');
            const addEmployeeFields = document.getElementById('addEmployeeFields');
            if (addRoleSelect) {
                addRoleSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const isEmployee = selectedOption.getAttribute('data-is-employee') === 'true';
                    addEmployeeFields.style.display = isEmployee ? 'block' : 'none';
                });
            }

            // For each edit modal, handle employee fields toggle
            @foreach($users as $user)
                (function () {
                    const roleSelect = document.getElementById('editRoleSelect{{ $user->id }}');
                    const employeeFields = document.getElementById('editEmployeeFields{{ $user->id }}');
                    if (roleSelect && employeeFields) {
                        function toggleEmployeeFields() {
                            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                            const isEmployee = selectedOption.getAttribute('data-is-employee') === 'true';
                            employeeFields.style.display = isEmployee ? 'block' : 'none';
                        }
                        roleSelect.addEventListener('change', toggleEmployeeFields);
                        // initial call to set correct visibility on page load
                        toggleEmployeeFields();
                    }
                })();
            @endforeach
            });

        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.style.display = 'block';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.style.display = 'none';
        }
    </script>
@endsection