@extends('layouts.admin')

@push('styles')
<style>
    /* Custom styles for roles page */
    .role-name {
        background-color: var(--primary-color);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    .permission-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .permission-tag {
        background-color: var(--secondary-color);
        color: white;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 0.9em;
    }

    .permission-tag.more {
        background-color: var(--accent-color);
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding-top: 15px;
        margin-top: 20px;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
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
        <div class="card">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>System Name</th>
                        <th>Display Name</th>
                        <th>Description</th>
                        <th>Permissions</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td><span class="role-name">{{ $role->name }}</span></td>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ $role->description }}</td>
                        <td>
                            <div class="permission-tags">
                                @foreach($role->permissions as $permission)
                                    <span class="permission-tag">{{ $permission->display_name }}</span>
                                @endforeach
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No roles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addRoleModal')">&times;</span>
            <h2><i class="fas fa-plus-circle"></i> Add New Role</h2>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Role Name (System Name)</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g., 'editor'" required>
                    <small>This is the name used in the system (lowercase, no spaces).</small>
                </div>
                <div class="form-group">
                    <label for="display_name">Display Name</label>
                    <input type="text" id="display_name" name="display_name" class="form-control" placeholder="e.g., 'Content Editor'" required>
                    <small>This is the human-readable name.</small>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="A brief description of the role."></textarea>
                </div>
                <div class="form-group">
                    <label>Permissions</label>
                    <div class="permissions-container">
                        @foreach($permissions as $group => $perms)
                            <div class="permission-group">
                                <h4>{{ ucfirst($group) }}</h4>
                                @foreach($perms as $permission)
                                    <div class="permission-checkbox">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_add_{{ $permission->id }}">
                                        <label for="perm_add_{{ $permission->id }}">{{ $permission->display_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-submit">Add Role</button>
                    <button type="button" onclick="closeModal('addRoleModal')" class="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Role Modals -->
    @foreach($roles as $role)
    <div id="editRoleModal{{ $role->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editRoleModal{{ $role->id }}')">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Role: {{ $role->display_name }}</h2>
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Role Name (System Name)</label>
                    <input type="text" value="{{ $role->name }}" class="form-control" readonly disabled>
                </div>
                <div class="form-group">
                    <label for="display_name_{{ $role->id }}">Display Name</label>
                    <input type="text" id="display_name_{{ $role->id }}" name="display_name" value="{{ $role->display_name }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description_{{ $role->id }}">Description</label>
                    <textarea id="description_{{ $role->id }}" name="description" class="form-control">{{ $role->description }}</textarea>
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
                                            id="perm_edit_{{ $role->id }}_{{ $permission->id }}" 
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        <label for="perm_edit_{{ $role->id }}_{{ $permission->id }}">{{ $permission->display_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-submit">Update Role</button>
                    <button type="button" onclick="closeModal('editRoleModal{{ $role->id }}')" class="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Delete Role Modals -->
    @foreach($roles as $role)
    @if($role->name !== 'super_admin')
    <div id="deleteRoleModal{{ $role->id }}" class="modal">
        <div class="modal-content narrow">
            <span class="close" onclick="closeModal('deleteRoleModal{{ $role->id }}')">&times;</span>
            <h2><i class="fas fa-trash-alt"></i> Delete Role</h2>
            <p>Are you sure you want to delete the role: <strong>{{ $role->display_name }}</strong>? This action cannot be undone.</p>
            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-footer">
                    <button type="submit" class="btn-danger">Delete Role</button>
                    <button type="button" onclick="closeModal('deleteRoleModal{{ $role->id }}')" class="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endforeach

@endsection

@push('scripts')
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
@endpush

@push('styles')
<style>
    .role-name {
        background-color: #f0f0f0;
        padding: 3px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.9rem;
    }
    .permission-tags .more {
        background-color: #6c757d;
    }
    .modal-content.narrow {
        max-width: 500px;
    }
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
</style>
@endpush