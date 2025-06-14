@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>📦 Inventory Management</h1>
        <div class="dashboard-actions">
            <button onclick="openModal('exportInventoryModal')" class="btn-export">📊 Export Report</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Inventory Filters -->
    <div class="filters-container">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="filters-form">
            <div class="filter-group">
                <label>Category:</label>
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Stock Status:</label>
                <select name="stock_status" class="form-control">
                    <option value="">All</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Search:</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="form-control">
            </div>
            <button type="submit" class="btn-filter">Apply Filters</button>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="{{ $product->stock <= $product->min_stock ? 'low-stock-row' : '' }} {{ $product->stock == 0 ? 'out-of-stock-row' : '' }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->min_stock ?? 0 }}</td>
                    <td>
                        @if($product->stock <= 0)
                            <span class="stock-badge out">Out of Stock</span>
                        @elseif($product->stock <= $product->min_stock)
                            <span class="stock-badge low">Low Stock</span>
                        @else
                            <span class="stock-badge in">In Stock</span>
                        @endif
                    </td>
                    <td>{{ $product->updated_at->diffForHumans() }}</td>
                    <td>
                        <button onclick="openAdjustModal('{{ $product->id }}')" class="btn-action adjust">⚖️</button>
                        <button onclick="openHistoryModal('{{ $product->id }}')" class="btn-action history">📋</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $products->appends(request()->query())->links() }}
    </div>

    <!-- Adjust Stock Modals -->
    @foreach($products as $product)
    <div id="adjustStockModal{{ $product->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('adjustStockModal{{ $product->id }}')">&times;</span>
            <h2>Adjust Stock: {{ $product->title }}</h2>
            <form action="{{ route('admin.inventory.adjust', $product) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Current Stock:</label>
                    <input type="text" value="{{ $product->stock }}" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Adjustment Quantity:</label>
                    <input type="number" name="quantity" class="form-control" required>
                    <small class="form-text">Use positive numbers to add stock, negative to remove.</small>
                </div>
                <div class="form-group">
                    <label>Notes:</label>
                    <textarea name="notes" class="form-control" placeholder="Reason for adjustment"></textarea>
                </div>
                <button type="submit" class="btn-submit">Update Stock</button>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Stock History Modals -->
    @foreach($products as $product)
    <div id="stockHistoryModal{{ $product->id }}" class="modal modal-large">
        <div class="modal-content">
            <span class="close" onclick="closeModal('stockHistoryModal{{ $product->id }}')">&times;</span>
            <h2>Stock History: {{ $product->title }}</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->inventoryMovements()->orderBy('created_at', 'desc')->get() as $movement)
                        <tr>
                            <td>{{ $movement->created_at }}</td>
                            <td>
                                @if($movement->type == 'in')
                                    <span class="movement-badge in">Stock In</span>
                                @else
                                    <span class="movement-badge out">Stock Out</span>
                                @endif
                            </td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ ucfirst($movement->reference_type ?? 'N/A') }}</td>
                            <td>{{ $movement->user->name ?? 'System' }}</td>
                            <td>{{ $movement->notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Export Inventory Modal -->
    <div id="exportInventoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('exportInventoryModal')">&times;</span>
            <h2>Export Inventory Report</h2>
            <form action="{{ route('admin.inventory.export') }}" method="GET">
                <div class="form-group">
                    <label>Report Format:</label>
                    <select name="format" class="form-control">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Include:</label>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input type="checkbox" name="include[]" value="current_stock" id="include_current" checked>
                            <label for="include_current">Current Stock</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="include[]" value="movements" id="include_movements">
                            <label for="include_movements">Stock Movements</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="include[]" value="low_stock" id="include_low">
                            <label for="include_low">Low Stock Items Only</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Date Range:</label>
                    <div class="date-range">
                        <input type="date" name="start_date" class="form-control">
                        <span>to</span>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn-submit">Generate Report</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function openAdjustModal(productId) {
            openModal('adjustStockModal' + productId);
        }
        
        function openHistoryModal(productId) {
            openModal('stockHistoryModal' + productId);
        }
    </script>

    <style>
        .filters-container {
            background-color: var(--bg-color);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filters-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .btn-filter {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .stock-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .stock-badge.in {
            background-color: var(--success-color);
            color: white;
        }
        
        .stock-badge.low {
            background-color: var(--warning-color);
            color: white;
        }
        
        .stock-badge.out {
            background-color: var(--danger-color);
            color: white;
        }
        
        .low-stock-row {
            background-color: rgba(243, 156, 18, 0.1);
        }
        
        .out-of-stock-row {
            background-color: rgba(231, 76, 60, 0.1);
        }
        
        .movement-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .movement-badge.in {
            background-color: var(--success-color);
            color: white;
        }
        
        .movement-badge.out {
            background-color: var(--danger-color);
            color: white;
        }
        
        .modal-large .modal-content {
            width: 80%;
            max-width: 1000px;
        }
        
        .date-range {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
    </style>
@endsection