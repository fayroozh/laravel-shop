@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>📊 Dashboard Statistics</h1>
        <div class="dashboard-actions">
            <button onclick="exportReport('pdf')" class="btn-export btn-pdf">📄 Export PDF</button>
            <button onclick="exportReport('excel')" class="btn-export btn-excel">📊 Export Excel</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card products">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-title">Total Products</div>
                <div class="stat-number">{{ $products }}</div>
                <div class="stat-change">+{{ $productsThisMonth ?? 0 }} this month</div>
            </div>
            <a href="{{ route('admin.products') }}" class="stat-link">View Details →</a>
        </div>

        <div class="stat-card orders">
            <div class="stat-icon">🛒</div>
            <div class="stat-content">
                <div class="stat-title">Total Orders</div>
                <div class="stat-number">{{ $orders }}</div>
                <div class="stat-change">+{{ $ordersThisMonth ?? 0 }} this month</div>
            </div>
            <a href="{{ route('admin.orders') }}" class="stat-link">View Details →</a>
        </div>

        <div class="stat-card employees">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-title">Total Employees</div>
                <div class="stat-number">{{ $employees }}</div>
                <div class="stat-change">+{{ $employeesThisMonth ?? 0 }} this month</div>
            </div>
            <a href="{{ route('admin.employees') }}" class="stat-link">View Details →</a>
        </div>

        <div class="stat-card suppliers">
            <div class="stat-icon">🏭</div>
            <div class="stat-content">
                <div class="stat-title">Total Suppliers</div>
                <div class="stat-number">{{ $suppliers }}</div>
                <div class="stat-change">New this month</div>
            </div>
            <a href="{{ route('admin.suppliers') }}" class="stat-link">View Details →</a>
        </div>

        <div class="stat-card feedback">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <div class="stat-title">Total Feedback</div>
                <div class="stat-number">{{ $feedback }}</div>
                <div class="stat-change">+{{ $feedbackThisMonth ?? 0 }} this month</div>
            </div>
            <a href="{{ route('admin.feedback') }}" class="stat-link">View Details →</a>
        </div>

        <div class="stat-card revenue">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <div class="stat-title">Total Revenue</div>
                <div class="stat-number">${{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-change">+${{ number_format($revenueThisMonth, 2) }} this month</div>
            </div>
            <a href="{{ route('admin.orders') }}" class="stat-link">View Details →</a>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="recent-activities">
        <div class="activity-header">
            <h3>🕒 Recent Activities</h3>
            <a href="{{ route('admin.activities') }}" class="view-all">View All</a>
        </div>
        <div class="activity-list">
            @if(isset($recentActivities) && count($recentActivities) > 0)
                @foreach($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">{{ $activity->icon ?? '📋' }}</div>
                        <div class="activity-content">
                            <div class="activity-text">{{ $activity->description }}</div>
                            <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="activity-item">
                    <div class="activity-content">
                        <div class="activity-text">No recent activities</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3>📈 Monthly Orders</h3>
            <canvas id="ordersChart"></canvas>
        </div>
        <div class="chart-card">
            <h3>💰 Monthly Revenue</h3>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Orders Chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyOrders->pluck('month')) !!},
                datasets: [{
                    label: 'Orders',
                    data: {!! json_encode($monthlyOrders->pluck('count')) !!},
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
                    backgroundColor: '#2ecc71',
                    borderColor: '#27ae60',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        function exportReport(format) {
            // The type 'dashboard' is a placeholder. You may need to adjust this depending on
            // which report you intend to export from the main dashboard view.
            let url = `{{ route('admin.reports.export', ['type' => 'dashboard', 'format' => '__FORMAT__']) }}`;
            window.location.href = url.replace('__FORMAT__', format);
        }
    </script>
@endsection