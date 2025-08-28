@extends('layouts.admin')

@section('content')
    <div class="breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
        <div class="breadcrumb-item active">Sales Report</div>
    </div>

    <div class="dashboard-header">
        <h1>💰 Sales Report</h1>
        <div class="dashboard-actions">
            <button class="btn-export" onclick="exportReport('pdf')">📄 Export PDF</button>
            <button class="btn-export" onclick="exportReport('excel')">📊 Export Excel</button>
        </div>
    </div>

    <!-- Sales Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Daily Sales</div>
            <div class="stat-number">${{ number_format($dailySales, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Monthly Sales</div>
            <div class="stat-number">${{ number_format($monthlySales, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Yearly Sales</div>
            <div class="stat-number">${{ number_format($yearlySales, 2) }}</div>
        </div>
    </div>

    <!-- Charts and Top Products -->
    <div class="reports-grid">
        <div class="chart-card">
            <h3>Sales Last 30 Days</h3>
            <canvas id="salesLast30DaysChart"></canvas>
        </div>
        <div class="top-products-card">
            <h3>Top 5 Selling Products</h3>
            <ul class="top-products-list">
                @forelse($topSellingProducts as $product)
                    <li>
                        <span class="product-title">{{ $product->title }}</span>
                        <span class="product-quantity">{{ $product->quantity_sold }} sold</span>
                    </li>
                @empty
                    <li>No sales data available.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sales Last 30 Days Chart
        new Chart(document.getElementById('salesLast30DaysChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    label: 'Sales Amount',
                    data: {!! json_encode($salesLast30Days) !!},
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    });

    function exportReport(format) {
        // Adjust the URL as needed for your export logic
        const url = "{{ route('admin.reports.export', ['type' => 'sales', 'format' => '__FORMAT__']) }}".replace('__FORMAT__', format);
        window.location.href = url;
    }
</script>
@endpush