@extends('layouts.admin')

@section('content')
<div class="breadcrumb">
    <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
    <div class="breadcrumb-item active">{{ $reportTitle }}</div>
</div>
<div class="dashboard-header">
    <h1>💰 Sales Reports</h1>
    <div class="dashboard-actions">
        <button class="btn-export" onclick="exportReport('pdf')">📄 Export PDF</button>
        <button class="btn-export" onclick="exportReport('excel')">📊 Export Excel</button>
    </div>
</div>

<!-- Advanced Search -->
<div class="search-section card">
    <form action="{{ route('admin.reports.sales') }}" method="GET" class="search-form">
        <div class="search-fields">
            <div class="form-group">
                <label>Date Range</label>
                <select name="date_range" class="form-control">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <button type="submit" class="btn-search">🔍 Search</button>
        </div>
    </form>
</div>

<!-- Interactive Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Monthly Sales</h3>
        <canvas id="monthlySalesChart"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Sales Chart
    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Sales Amount',
                data: {!! json_encode($salesData) !!},
                backgroundColor: '#3498db'
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
    
    function exportReport(format) {
        const url = "{{ url('admin/export/report') }}/" + format + "?type=sales";
        window.location.href = url;
    }


</script>
@endpush
@endsection