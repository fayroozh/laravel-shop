@extends('layouts.admin')

@section('content')
    <div class="breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
        <div class="breadcrumb-item active">Inventory Report</div>
    </div>

    <div class="dashboard-header">
        <h1>📦 Inventory Report</h1>
        <div class="dashboard-actions">
            <button class="btn-export" onclick="exportReport('pdf')">📄 Export PDF</button>
            <button class="btn-export" onclick="exportReport('excel')">📊 Export Excel</button>
        </div>
    </div>

    <!-- Inventory Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Products</div>
            <div class="stat-number">{{ $totalProducts }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Stock Units</div>
            <div class="stat-number">{{ $totalStock }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Stock Value</div>
            <div class="stat-number">${{ number_format($totalStockValue, 2) }}</div>
        </div>
    </div>

    <!-- Chart and Lists -->
    <div class="reports-grid">
        <div class="chart-card">
            <h3>Stock by Category</h3>
            <canvas id="stockByCategoryChart"></canvas>
        </div>
        <div class="list-card">
            <h3>Low Stock Products (Less than 10)</h3>
            <ul class="report-list">
                @forelse($lowStockProducts as $product)
                    <li>
                        <span class="product-title">{{ $product->title }}</span>
                        <span class="product-quantity">{{ $product->stock }} units</span>
                    </li>
                @empty
                    <li>All products have sufficient stock.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="reports-grid" style="margin-top: 20px;">
        <div class="list-card full-width">
            <h3>Out of Stock Products</h3>
            <ul class="report-list">
                @forelse($outOfStockProducts as $product)
                    <li>
                        <span class="product-title">{{ $product->title }}</span>
                    </li>
                @empty
                    <li>No products are out of stock.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Stock by Category Chart
        const stockByCategoryData = {!! json_encode($stockByCategory) !!};
        const categoryLabels = stockByCategoryData.map(item => item.name);
        const categoryStock = stockByCategoryData.map(item => item.stock);

        new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Stock Quantity',
                    data: categoryStock,
                    backgroundColor: [
                        '#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6',
                        '#34495e', '#1abc9c', '#e67e22', '#d35400', '#c0392b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });

    function exportReport(format) {
        // Adjust the URL as needed for your export logic
        const url = "{{ route('admin.reports.export', ['type' => 'inventory', 'format' => '__FORMAT__']) }}".replace('__FORMAT__', format);
        window.location.href = url;
    }
</script>
@endpush