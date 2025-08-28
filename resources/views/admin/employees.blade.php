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
                    <td>{{ $employee->user->name }}</td>
                    <td>{{ $employee->user->email }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>{{ optional($employee->user)->roles->pluck('display_name')->join(', ') ?? 'No Role' }}</td>
                    <td>{{ $employee->mobile }}</td>
                    <td>
                        @if(auth()->user()->hasPermission('edit_employees'))
                            <a href="javascript:void(0)" class="btn-edit"
                                onclick="openModal('editEmployeeModal{{ $employee->id }}')" title="Edit">✏️</a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_employees'))
                            <a href="javascript:void(0)" class="btn-delete"
                                onclick="openModal('deleteEmployeeModal{{ $employee->id }}')" title="Delete">🗑️</a>
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
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="mobile" class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role_id" class="form-control" required>
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
                        <input type="text" name="name" class="form-control" value="{{ $employee->user->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $employee->user->email }}" required>
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
                                <option value="{{ $role->id }}" {{ $employee->user && $employee->user->roles->contains($role->id) ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeModal('editEmployeeModal{{ $employee->id }}')"
                            class="btn-cancel">Cancel</button>
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
                        <button type="button" class="btn-cancel"
                            onclick="closeModal('deleteEmployeeModal{{ $employee->id }}')">Cancel</button>
                        <button type="submit" class="btn-delete">Delete Employee</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- JavaScript للتحكم بالمودال --}}
    <script>
        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }
        // إغلاق عند الضغط خارج المودال
        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>

    {{-- CSS بسيط للمودالات --}}
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
@endsection