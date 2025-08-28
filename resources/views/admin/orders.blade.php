@extends('layouts.admin')
@section('content')
    <h1>🛒 Orders Management</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Total</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_name }} <br> <small>{{ $order->email }}</small></td>
                    <td>
                        <ul style="list-style: none; padding: 0;">
                            @foreach($order->orderItems as $item)
                                <li>{{ $item->product->title ?? 'Product not found' }} (x{{ $item->quantity }}) -
                                    ${{ number_format($item->price * $item->quantity, 2) }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>${{ number_format($order->total, 2) }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="#" class="btn-edit"
                            onclick="event.preventDefault(); openModal('editOrderModal{{ $order->id }}')">✏️ Edit Status</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Edit Order Status Modals -->
    @foreach($orders as $order)
        <div id="editOrderModal{{ $order->id }}" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editOrderModal{{ $order->id }}')">&times;</span>
                <h2>Edit Order Status for Order #{{ $order->id }}</h2>
                <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Customer:</label>
                        <p>{{ $order->customer_name }}</p>
                    </div>
                    <div class="form-group">
                        <label>Total:</label>
                        <p>${{ number_format($order->total, 2) }}</p>
                    </div>
                    <div class="form-group">
                        <label for="status">Order Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit">Update Status</button>
                </form>
            </div>
        </div>
    @endforeach
@endsection