
@extends('layouts.admin')
@section('content')
    <h1>🛒 Orders Management</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <table class="styled-table">
        <tr>
            <th>ID</th><th>Customer</th><th>Product</th><th>Quantity</th><th>Status</th><th>Actions</th>
        </tr>
        @foreach($orders as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->user->name ?? 'Guest' }}</td>
                <td>{{ $o->product_name ?? '-' }}</td>
                <td>{{ $o->quantity }}</td>
                <td>{{ $o->status }}</td>
                <td>
                    <a href="#" class="btn-edit" onclick="openModal('editOrderModal{{ $o->id }}')">✏️ Edit Status</a>
                </td>
            </tr>
        @endforeach
    </table>
    
    <!-- Edit Order Status Modals -->
    @foreach($orders as $o)
    <div id="editOrderModal{{ $o->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editOrderModal{{ $o->id }}')">&times;</span>
            <h2>Edit Order Status</h2>
            <form method="POST" action="{{ route('admin.orders.update', $o->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="status">Order Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending" {{ $o->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $o->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $o->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $o->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $o->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Customer:</label>
                    <p>{{ $o->user->name ?? 'Guest' }}</p>
                </div>
                <div class="form-group">
                    <label>Product:</label>
                    <p>{{ $o->product_name ?? '-' }}</p>
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <p>{{ $o->quantity }}</p>
                </div>
                <button type="submit" class="btn-submit">Update Status</button>
            </form>
        </div>
    </div>
    @endforeach
@endsection