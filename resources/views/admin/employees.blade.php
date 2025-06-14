@extends('layouts.admin')
@section('content')
    <div class="dashboard-header">
        <h1>👨‍💼 Employees Management</h1>
        <div class="dashboard-actions">
            @if(auth()->user()->hasPermission('create_employees'))
                <button onclick="openModal('addEmployeeModal')" class="btn-add">➕ Add Employee</button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <table class="styled-table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>{{ optional($employee->user)->roles->pluck('display_name')->join(', ') ?? 'No Role' }}</td>
                    <td>{{ $employee->mobile }}</td>
                    <td>
                        @if(auth()->user()->hasPermission('edit_employees'))
                            <a href="#" class="btn-edit" onclick="openModal('editEmployeeModal{{ $employee->id }}')" title="Edit">✏️</a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_employees'))
                            <a href="#" class="btn-delete" onclick="openModal('deleteEmployeeModal{{ $employee->id }}')" title="Delete">🗑️</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addEmployeeModal')">&times;</span>
            <h2>Add New Employee</h2>
            <form method="POST" action="{{ route('admin.employees.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                    <input type="text" id="position" name="position" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mobile">Phone Number</label>
                    <input type="text" id="mobile" name="mobile" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select id="role_id" name="role_id" class="form-control" required>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal('addEmployeeModal')" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Add Employee</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modals -->
    @foreach($employees as $employee)
    <div id="editEmployeeModal{{ $employee->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editEmployeeModal{{ $employee->id }}')">&times;</span>
            <h2>Edit Employee</h2>
            <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $employee->name }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position" class="form-control" value="{{ $employee->position }}" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="mobile" class="form-control" value="{{ $employee->mobile }}">
                </div>
                <div class="form-group">
                    <label>Password (Leave empty to keep current)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role_id" class="form-control" required>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}" {{ $employee->user && $employee->user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal('editEmployeeModal{{ $employee->id }}')" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Delete Employee Modals -->
    @foreach($employees as $employee)
    <div id="deleteEmployeeModal{{ $employee->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteEmployeeModal{{ $employee->id }}')">&times;</span>
            <h2>Delete Employee</h2>
            <p>Are you sure you want to delete the employee "{{ $employee->name }}"?</p>
            <p>This action cannot be undone.</p>
            <form method="POST" action="{{ route('admin.employees.destroy', $employee->id) }}">
                @csrf
                @method('DELETE')
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteEmployeeModal{{ $employee->id }}')" >Cancel</button>
                    <button type="submit" class="btn-delete">Delete Employee</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection