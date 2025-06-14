<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Employee;
use App\Models\User;
use App\Models\Order;

class ReportController extends Controller
{
    public function index()
    {
        $reportTitle = 'All Reports';
        return view('admin.reports.index', compact('reportTitle'));
    }
    
    public function sales()
    {
        $reportTitle = 'Sales Reports';
        
        // Get monthly sales data for the chart
        $monthlySales = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
            
        // Format data for chart
        $months = [];
        $salesData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $salesData[] = $monthlySales[$i] ?? 0;
        }
    
        // Get total sales and orders count
        $totalSales = array_sum($salesData);
        $totalOrders = Order::whereYear('created_at', date('Y'))->count();
        
        return view('admin.reports.sales', compact(
            'reportTitle', 'months', 'salesData',
            'totalSales', 'totalOrders'
        ));
    }

    public function employees()
{
    $reportTitle = 'Employee Reports';
    
    // Get employees with performance data
    $employees = Employee::with(['user.roles'])->get();

    // Get departments/positions
    $departments = Employee::distinct()->pluck('position')->toArray();
    
    // Calculate performance metrics
    $highPerformers = 0;
    $averagePerformers = 0;
    $lowPerformers = 0;
    
    foreach ($employees as $employee) {
        $employee->tasks_completed = rand(5, 50); // Temporary random data
        $employee->performance_score = rand(50, 100); // Temporary random data
        
        if ($employee->performance_score > 80) {
            $employee->performance_level = 'high';
            $highPerformers++;
        } elseif ($employee->performance_score > 60) {
            $employee->performance_level = 'average';
            $averagePerformers++;
        } else {
            $employee->performance_level = 'low';
            $lowPerformers++;
        }
    }
    
    return view('admin.reports.employees', compact(
        'reportTitle', 'employees', 'departments',
        'highPerformers', 'averagePerformers', 'lowPerformers'
    ));
}


    public function inventory()
    {
        $reportTitle = 'Inventory Reports';
        $categories = Category::all();
        
        // Get products with stock status
        $products = Product::with('category')->get();
        
        // Calculate stock statistics
        $lowStock = $products->where('stock', '<', 10)->count();
        $normalStock = $products->whereBetween('stock', [10, 50])->count();
        $highStock = $products->where('stock', '>', 50)->count();
        
        // Add stock status to products
        foreach ($products as $product) {
            if ($product->stock < 10) {
                $product->stock_status = 'low';
            } elseif ($product->stock > 50) {
                $product->stock_status = 'high';
            } else {
                $product->stock_status = 'normal';
            }
        }
        
        return view('admin.reports.inventory', compact(
            'reportTitle', 'categories', 'products',
            'lowStock', 'normalStock', 'highStock'
        ));
    }

    public function customers()
    {
        $reportTitle = 'Customer Reports';
        
        // Get users who have placed orders
        $customers = User::whereHas('orders')->get();
        
        // Simulate customer types and purchase data
        $newCustomers = 0;
        $regularCustomers = 0;
        $vipCustomers = 0;
        
        foreach ($customers as $customer) {
            $customer->total_orders = $customer->orders->count();
            $customer->total_spent = $customer->orders->sum('total_amount');
            $customer->last_purchase = $customer->orders->sortByDesc('created_at')->first()->created_at;
            
            // Determine customer type based on orders and amount spent
            if ($customer->total_orders > 10 || $customer->total_spent > 1000) {
                $customer->type = 'vip';
                $vipCustomers++;
            } elseif ($customer->total_orders > 3 || $customer->total_spent > 300) {
                $customer->type = 'regular';
                $regularCustomers++;
            } else {
                $customer->type = 'new';
                $newCustomers++;
            }
        }
        
        return view('admin.reports.customers', compact(
            'reportTitle', 'customers',
            'newCustomers', 'regularCustomers', 'vipCustomers'
        ));
    }
}
