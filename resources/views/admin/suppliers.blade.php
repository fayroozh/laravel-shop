
@extends('layouts.admin')
@section('content')
    <div class="dashboard-header">
        <h1>🏭 Suppliers Management</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('addSupplierModal')" class="btn-add">➕ Add Supplier</button>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <table class="styled-table">
            <tr>
                <th>ID</th><th>Name</th><th>Company</th><th>Phone</th><th>Actions</th>
            </tr>
            @foreach($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->id }}</td>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->company }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>
                        <a href="#" class="btn-edit" onclick="openModal('editSupplierModal{{ $supplier->id }}')" title="Edit">✏️</a>
                        <a href="#" class="btn-delete" onclick="openModal('deleteSupplierModal{{ $supplier->id }}')" title="Delete">🗑️</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    
    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addSupplierModal')">&times;</span>
            <h2>Add New Supplier</h2>
            <form method="POST" action="{{ route('admin.suppliers.store') }}">
                @csrf
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Company</label>
                    <input type="text" name="company" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal('addSupplierModal')" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Supplier Modals -->
    @foreach($suppliers as $supplier)
    <div id="editSupplierModal{{ $supplier->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editSupplierModal{{ $supplier->id }}')">&times;</span>
            <h2>Edit Supplier</h2>
            <form method="POST" action="{{ route('admin.suppliers.update', $supplier->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                </div>
                <div class="form-group">
                    <label>Company</label>
                    <input type="text" name="company" class="form-control" value="{{ $supplier->company }}" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ $supplier->address }}</textarea>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal('editSupplierModal{{ $supplier->id }}')" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    
    <!-- Delete Supplier Modals -->
    @foreach($suppliers as $supplier)
    <div id="deleteSupplierModal{{ $supplier->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteSupplierModal{{ $supplier->id }}')">&times;</span>
            <h2>Delete Supplier</h2>
            <p>Are you sure you want to delete the supplier "{{ $supplier->name }}"?</p>
            <p>This action cannot be undone.</p>
            <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier->id) }}">
                @csrf
                @method('DELETE')
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteSupplierModal{{ $supplier->id }}')">Cancel</button>
                    <button type="submit" class="btn-delete">Delete Supplier</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection