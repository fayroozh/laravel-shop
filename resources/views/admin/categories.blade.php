@extends('layouts.admin')
@section('content')
    <div class="dashboard-header">
        <h1>🗂️ Categories</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('addCategoryModal')" class="btn-add">➕ Add Category</button>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <table class="styled-table">
            <tr>
                <th>ID</th><th>Name</th><th>Description</th><th>Actions</th>
            </tr>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->description }}</td>
                    <td>
                        <a href="#" class="btn-edit" onclick="openEditModal('{{ $c->id }}', '{{ $c->name }}', '{{ $c->description }}')" title="Edit">✏️</a>
                        <a href="#" class="btn-delete" onclick="openModal('deleteCategoryModal{{ $c->id }}')" title="Delete">🗑️</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addCategoryModal')">&times;</span>
            <h2>Add New Category</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>
                <button type="submit" class="btn-submit">Add Category</button>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editCategoryModal')">&times;</span>
            <h2>Edit Category</h2>
            <form id="editCategoryForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label for="edit_name">Category Name</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="4"></textarea>
                </div>
                <button type="submit" class="btn-submit">Update Category</button>
            </form>
        </div>
    </div>
    
    <!-- Delete Category Modals -->
    @foreach($categories as $c)
    <div id="deleteCategoryModal{{ $c->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteCategoryModal{{ $c->id }}')">&times;</span>
            <h2>Delete Category</h2>
            <p>Are you sure you want to delete the category "{{ $c->name }}"?</p>
            <p>This action cannot be undone and may affect products assigned to this category.</p>
            <form method="POST" action="{{ route('admin.categories.destroy', $c->id) }}">
                @csrf
                @method('DELETE')
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteCategoryModal{{ $c->id }}')">Cancel</button>
                    <button type="submit" class="btn-delete">Delete Category</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function openEditModal(id, name, description) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('editCategoryForm').action = '/admin/categories/' + id;
            openModal('editCategoryModal');
        }
    </script>
@endsection