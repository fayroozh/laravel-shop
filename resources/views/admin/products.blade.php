
@extends('layouts.admin')
@section('content')
    <div class="dashboard-header">
        <h1>📦 Products Management</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('addProductModal')" class="btn-add">➕ Add Product</button>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <table class="styled-table">
            <tr>
                <th>ID</th><th>Title</th><th>Price</th><th>Category</th><th>Stock</th><th>Actions</th>
            </tr>
            @foreach($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->title }}</td>
                    <td>{{ $p->price }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        <button onclick="openModal('editProductModal{{ $p->id }}')" class="btn-edit" title="Edit">✏️ Edit</button>
                        <button onclick="openModal('deleteProductModal{{ $p->id }}')" class="btn-delete" title="Delete">🗑️ Delete</button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    
    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addProductModal')">&times;</span>
            <h2>Add New Product</h2>
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Product Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" value="1">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Images</label>
                    <input type="file" name="images[]" id="images" multiple class="form-control">
                    <div id="image-preview" class="mt-2"></div>
                </div>
                <button type="submit" class="btn-submit">Save</button>
            </form>
        </div>
    </div>

    <!-- Edit Product Modals -->
    @foreach($products as $p)
    <div id="editProductModal{{ $p->id }}" class="modal">
        <div class="modal-content" style="max-height: 80vh; overflow-y: auto; width: 60%;">
            <span class="close" onclick="closeModal('editProductModal{{ $p->id }}')">&times;</span>
            <h2>Edit Product</h2>
            <form method="POST" action="{{ route('admin.products.update', $p->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Product Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $p->title }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $p->description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" step="0.01" value="{{ $p->price }}" required>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" value="{{ $p->stock }}">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $p->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-submit">Update</button>
            </form>
        </div>
    </div>
    @endforeach
    
    <!-- Delete Product Modals -->
    @foreach($products as $p)
    <div id="deleteProductModal{{ $p->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteProductModal{{ $p->id }}')">&times;</span>
            <h2>Delete Product</h2>
            <p>Are you sure you want to delete the product "{{ $p->title }}"?</p>
            <p>This action cannot be undone.</p>
            <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}">
                @csrf
                @method('DELETE')
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteProductModal{{ $p->id }}')">Cancel</button>
                    <button type="submit" class="btn-delete">Delete Product</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        // Image preview before upload
        document.getElementById('images')?.addEventListener('change', function(event) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.match('image.*')) continue;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '4px';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection