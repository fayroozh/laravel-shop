@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>🔐 Roles Management</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('addRoleModal')" class="btn-add">+ Add Role</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Roles Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Display Name</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->display_name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <div class="permission-tags">
                            @foreach($role->permissions->take(3) as $permission)
                                <span class="permission-tag">{{ $permission->display_name }}</span>
                            @endforeach
                            @if($role->permissions->count() > 3)
                                <span class="permission-tag">+{{ $role->permissions->count() - 3 }} more</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <button onclick="openEditModal('{{ $role->id }}')" class="btn-action edit">✏️</button>
                        @if($role->name !== 'super_admin')
                            <button onclick="openDeleteModal('{{ $role->id }}')" class="btn-action delete">🗑️</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addRoleModal')">&times;</span>
            <h2>Add New Role</h2>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Role Name (System Name)</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Display Name</label>
                    <input type="text" name="display_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Permissions</label>
                    <div class="permissions-container">
                        @foreach($permissions as $group => $perms)
                            <div class="permission-group">
                                <h4>{{ ucfirst($group) }}</h4>
                                @foreach($perms as $permission)
                                    <div class="permission-checkbox">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}">
                                        <label for="perm_{{ $permission->id }}">{{ $permission->display_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn-submit">Add Role</button>
            </form>
        </div>
    </div>

    <!-- Edit Role Modals -->
    @foreach($roles as $role)
    <div id="editRoleModal{{ $role->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editRoleModal{{ $role->id }}')">&times;</span>
            <h2>Edit Role: {{ $role->display_name }}</h2>
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text" value="{{ $role->name }}" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Display Name</label>
                    <input type="text" name="display_name" value="{{ $role->display_name }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $role->description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Permissions</label>
                    <div class="permissions-container">
                        @foreach($permissions as $group => $perms)
                            <div class="permission-group">
                                <h4>{{ ucfirst($group) }}</h4>
                                @foreach($perms as $permission)
                                    <div class="permission-checkbox">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                            id="perm_{{ $role->id }}_{{ $permission->id }}" 
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        <label for="perm_{{ $role->id }}_{{ $permission->id }}">{{ $permission->display_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn-submit">Update Role</button>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Delete Role Modals -->
    @foreach($roles as $role)
    @if($role->name !== 'super_admin')
    <div id="deleteRoleModal{{ $role->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteRoleModal{{ $role->id }}')">&times;</span>
            <h2>Delete Role: {{ $role->display_name }}</h2>
            <p>Are you sure you want to delete this role? This action cannot be undone.</p>
            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete Role</button>
                <button type="button" onclick="closeModal('deleteRoleModal{{ $role->id }}')" class="btn-cancel">Cancel</button>
            </form>
        </div>
    </div>
    @endif
    @endforeach

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function openEditModal(roleId) {
            openModal('editRoleModal' + roleId);
        }
        
        function openDeleteModal(roleId) {
            openModal('deleteRoleModal' + roleId);
        }
    </script>

    <style>
        .permission-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .permission-tag {
            background-color: var(--accent-color);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .permissions-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .permission-group {
            margin-bottom: 20px;
        }
        
        .permission-group h4 {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
            color: var(--accent-color);
        }
        
        .permission-checkbox {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .permission-checkbox input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .btn-action {
            padding: 6px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            font-size: 16px;
        }
        
        .btn-action.edit {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-action.delete {
            background-color: var(--danger-color);
            color: white;
        }
    </style>
@endsection