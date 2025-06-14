@extends('layouts.admin')

@section('content')
<div class="breadcrumb">
    <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
    <div class="breadcrumb-item active">{{ $reportTitle ?? 'Employee Performance Report' }}</div>
</div>
<div class="dashboard-header">
    <h1>👥 Employee Performance Reports</h1>
    <div class="dashboard-actions">
        <button class="btn btn-primary" onclick="exportReport('pdf')">📄 Export PDF</button>
        <button class="btn btn-success" onclick="exportReport('excel')">📊 Export Excel</button>
    </div>
</div>

<!-- Advanced Search -->
<div class="search-section card">
    <form action="{{ route('admin.reports.employees') }}" method="GET" class="search-form">
        <div class="search-fields">
            <div class="form-group">
                <label>Department</label>
                <select name="department" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Performance Level</label>
                <select name="performance" class="form-control">
                    <option value="">All Levels</option>
                    <option value="high" {{ request('performance') == 'high' ? 'selected' : '' }}>High Performers</option>
                    <option value="average" {{ request('performance') == 'average' ? 'selected' : '' }}>Average</option>
                    <option value="low" {{ request('performance') == 'low' ? 'selected' : '' }}>Needs Improvement</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
        </div>
    </form>
</div>

<!-- Interactive Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Performance Distribution</h3>
        <canvas id="performanceChart" height="250"></canvas>
    </div>
    <div class="chart-card">
        <h3>Monthly Performance Trends</h3>
        <canvas id="trendsChart" height="250"></canvas>
    </div>
</div>

<!-- Data Table -->
<div class="card data-table">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Tasks Completed</th>
                <th>Performance Score</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees ?? [] as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->department }}</td>
                <td>{{ $employee->tasks_completed }}</td>
                <td>{{ number_format($employee->performance_score, 1) }}</td>
                <td>
                    <span class="badge 
                        @if($employee->performance_level == 'high') badge-success
                        @elseif($employee->performance_level == 'average') badge-warning
                        @else badge-danger
                        @endif">
                        {{ ucfirst($employee->performance_level) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No employees found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($employees instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-3">
            {{ $employees->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function exportReport(format) {
        // Implement export functionality
        alert(`Exporting to ${format} will be implemented here`);
        // You can implement actual export using window.location or AJAX
    }

    // Initialize charts when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Performance Distribution Chart
        const perfChart = document.getElementById('performanceChart');
        if (perfChart) {
            new Chart(perfChart, {
                type: 'doughnut',
                data: {
                    labels: ['High Performers', 'Average', 'Needs Improvement'],
                    datasets: [{
                        data: [
                            {{ $highPerformers ?? 0 }}, 
                            {{ $averagePerformers ?? 0 }}, 
                            {{ $lowPerformers ?? 0 }}
                        ],
                        backgroundColor: ['#2ecc71', '#3498db', '#e74c3c']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Monthly Trends Chart
        const trendsChart = document.getElementById('trendsChart');
        if (trendsChart) {
            new Chart(trendsChart, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels ?? []) !!},
                    datasets: [{
                        label: 'Average Performance Score',
                        data: {!! json_encode($monthlyScores ?? []) !!},
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection