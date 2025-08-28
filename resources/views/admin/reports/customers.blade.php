@extends('layouts.admin')

@section('content')
<div class="breadcrumb">
    <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
    <div class="breadcrumb-item active">{{ $reportTitle ?? 'Customer Report' }}</div>
</div>
<div class="dashboard-header">
    <h1>👤 Customer Reports</h1>
    <div class="dashboard-actions">
        <button class="btn-export" onclick="exportReport('pdf')">📄 Export PDF</button>
        <button class="btn-export" onclick="exportReport('excel')">📊 Export Excel</button>
    </div>
</div>

<!-- Advanced Search -->
<div class="search-section card">
    <form action="{{ route('admin.reports.customers') }}" method="GET" class="search-form">
        <div class="search-fields">
            <div class="form-group">
                <label>Customer Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="new" {{ request('type') == 'new' ? 'selected' : '' }}>New Customers</option>
                    <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Regular Customers</option>
                    <option value="vip" {{ request('type') == 'vip' ? 'selected' : '' }}>VIP Customers</option>
                </select>
            </div>
            <div class="form-group">
                <label>Purchase Frequency</label>
                <select name="frequency" class="form-control">
                    <option value="">All Frequencies</option>
                    <option value="high" {{ request('frequency') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('frequency') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('frequency') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <button type="submit" class="btn-search">🔍 Search</button>
        </div>
    </form>
</div>

<!-- Interactive Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Customer Distribution</h3>
        <canvas id="customerDistributionChart" height="250"></canvas>
    </div>
    <div class="chart-card">
        <h3>Purchase Trends</h3>
        <canvas id="purchaseTrendsChart" height="250"></canvas>
    </div>
</div>

<!-- Data Table -->
<div class="card data-table">
    <table class="styled-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Type</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
                <th>Last Purchase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td class="type-{{ $customer->type }}">{{ ucfirst($customer->type) }}</td>
                <td>{{ $customer->total_orders }}</td>
                <td>${{ number_format($customer->total_spent, 2) }}</td>
                <td>{{ $customer->last_purchase ? $customer->last_purchase->format('Y-m-d') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No customers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
     @if(isset($paginator) && $paginator->hasPages())
        <div class="mt-3">
            {{ $paginator->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function exportReport(format) {
        // Basic export functionality
        alert(`Exporting to ${format} will be implemented here`);
    }

    // Initialize charts when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Customer Distribution Chart
        if (document.getElementById('customerDistributionChart')) {
            new Chart(document.getElementById('customerDistributionChart'), {
                type: 'pie',
                data: {
                    labels: ['New', 'Regular', 'VIP'],
                    datasets: [{
                        data: [
                            {{ $newCustomers ?? 0 }}, 
                            {{ $regularCustomers ?? 0 }}, 
                            {{ $vipCustomers ?? 0 }}
                        ],
                        backgroundColor: ['#3498db', '#2ecc71', '#f1c40f']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Purchase Trends Chart
        if (document.getElementById('purchaseTrendsChart')) {
            new Chart(document.getElementById('purchaseTrendsChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($purchaseDates ?? []) !!},
                    datasets: [{
                        label: 'Purchase Amount',
                        data: {!! json_encode($purchaseAmounts ?? []) !!},
                        borderColor: '#3498db',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    });
</script>
@endpush
@endsection