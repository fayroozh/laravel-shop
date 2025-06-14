@extends('layouts.admin')

@section('content')
<div class="breadcrumb">
    <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></div>
    <div class="breadcrumb-item active">{{ $reportTitle }}</div>
</div>
    <div class="dashboard-header">
        <h1>📊 Advanced Reports</h1>
    </div>

    <!-- Report Types -->
    <div class="report-types">
        <div class="report-card" onclick="window.location.href='{{ route('admin.reports.sales') }}'">
            <div class="report-icon">💰</div>
            <div class="report-title">Sales Reports</div>
            <div class="report-description">View sales data by period, products, and more</div>
        </div>
        
        <div class="report-card" onclick="window.location.href='{{ route('admin.reports.inventory') }}'">
            <div class="report-icon">📦</div>
            <div class="report-title">Inventory Reports</div>
            <div class="report-description">Track stock levels, movements, and low stock alerts</div>
        </div>
        
        <div class="report-card" onclick="window.location.href='{{ route('admin.reports.employees') }}'">
            <div class="report-icon">👥</div>
            <div class="report-title">Employee Performance</div>
            <div class="report-description">Analyze employee productivity and sales performance</div>
        </div>
        
        <div class="report-card" onclick="window.location.href='{{ route('admin.reports.customers') }}'">
            <div class="report-icon">👤</div>
            <div class="report-title">Customer Reports</div>
            <div class="report-description">View customer purchase history and behavior</div>
        </div>
    </div>

    <style>
        .report-types {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .report-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        
        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .report-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--accent-color);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .report-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .report-description {
            color: var(--text-color);
            opacity: 0.8;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
@endsection