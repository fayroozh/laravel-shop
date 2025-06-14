@extends('layouts.admin')

@section('content')
<div class="breadcrumb">
    <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
    <div class="breadcrumb-item active">{{ $reportTitle ?? 'Inventory Report' }}</div>
</div>
<div class="dashboard-header">
    <h1>📦 Inventory Reports</h1>
    <div class="dashboard-actions">
        <button class="btn-export" onclick="exportReport('pdf')">📄 Export PDF</button>
        <button class="btn-export" onclick="exportReport('excel')">📊 Export Excel</button>
    </div>
</div>

<!-- Advanced Search -->
<div class="search-section card">
    <form action="{{ route('admin.reports.inventory') }}" method="GET" class="search-form">
        <div class="search-fields">
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Stock Level</label>
                <select name="stock_level" class="form-control">
                    <option value="">All Levels</option>
                    <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('stock_level') == 'high' ? 'selected' : '' }}>High Stock</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
        </div>
    </form>
</div>

<!-- Interactive Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Stock Level Distribution</h3>
        <canvas id="stockDistributionChart" height="250"></canvas>
    </div>
    <div class="chart-card">
        <h3>Stock Movement Trends</h3>
        <canvas id="stockMovementChart" height="250"></canvas>
    </div>
</div>

<!-- Data Table -->
<div class="card data-table">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Status</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products ?? [] as $product)
            <tr>
                <td>{{ $product->title }}</td>
                <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <span class="badge 
                        @if($product->stock_status == 'low') badge-danger
                        @elseif($product->stock_status == 'normal') badge-primary
                        @else badge-success
                        @endif">
                        {{ ucfirst($product->stock_status) }}
                    </span>
                </td>
                <td>{{ $product->updated_at ? $product->updated_at->format('Y-m-d H:i') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function exportReport(format) {
        // Implement export functionality
        alert(`Exporting to ${format} will be implemented here`);
    }

    // Initialize charts only if elements exist
    document.addEventListener('DOMContentLoaded', function() {
        const stockDistributionCtx = document.getElementById('stockDistributionChart');
        if (stockDistributionCtx) {
            new Chart(stockDistributionCtx, {
                type: 'pie',
                data: {
                    labels: ['Low Stock', 'Normal', 'High Stock'],
                    datasets: [{
                        data: [
                            {{ $lowStock ?? 0 }}, 
                            {{ $normalStock ?? 0 }}, 
                            {{ $highStock ?? 0 }}
                        ],
                        backgroundColor: ['#e74c3c', '#3498db', '#2ecc71']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        const stockMovementCtx = document.getElementById('stockMovementChart');
        if (stockMovementCtx) {
            new Chart(stockMovementCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($movementDates ?? []) !!},
                    datasets: [{
                        label: 'Stock Movement',
                        data: {!! json_encode($movementCounts ?? []) !!},
                        borderColor: '#3498db',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection