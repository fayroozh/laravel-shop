<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Order;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Feedback;
use App\Models\Role; // إضافة هذا السطر
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardReportExport;


class AdminController extends Controller
{
    public function dashboard()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        
        // Statistics
        $products = Product::count();
        $orders = Order::count();
        $employees = Employee::count();
        $suppliers = Supplier::count();
        $feedback = Feedback::count();
        
        // Monthly statistics
        $productsThisMonth = Product::whereMonth('created_at', $now->month)->count();
        $ordersThisMonth = Order::whereMonth('created_at', $now->month)->count();
        $employeesThisMonth = Employee::whereMonth('created_at', $now->month)->count();
        
        // Revenue calculations
        $totalRevenue = Order::sum('total_amount');
        $revenueThisMonth = Order::whereMonth('created_at', $now->month)->sum('total_amount');
        
        // Chart data
        $monthlyOrders = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->get();
            
        $monthlyRevenue = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->get();
    
        return view('admin.dashboard', compact(
            'products', 'orders', 'employees', 'suppliers', 'feedback',
            'productsThisMonth', 'ordersThisMonth', 'employeesThisMonth',
            'totalRevenue', 'revenueThisMonth', 'monthlyOrders', 'monthlyRevenue'
        ));
    }
    
    public function exportReport($format)
    {
        $data = [
            'products' => \App\Models\Product::count(),
            'orders' => \App\Models\Order::count(),
            'employees' => \App\Models\Employee::count(),
            'suppliers' => \App\Models\Supplier::count(),
            'feedback' => \App\Models\Feedback::count(),
            'totalRevenue' => \App\Models\Order::sum('total_amount') ?? 0,
            'generated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ];
        
        if ($format === 'pdf') {
            // For now, return a simple response until PDF library is installed
            return response()->json($data)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="dashboard-report-' . date('Y-m-d') . '.json"');
        } elseif ($format === 'excel') {
            // For now, return CSV format
            $csv = "Metric,Count\n";
            $csv .= "Products,{$data['products']}\n";
            $csv .= "Orders,{$data['orders']}\n";
            $csv .= "Employees,{$data['employees']}\n";
            $csv .= "Suppliers,{$data['suppliers']}\n";
            $csv .= "Feedback,{$data['feedback']}\n";
            $csv .= "Total Revenue,{$data['totalRevenue']}\n";
            
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="dashboard-report-' . date('Y-m-d') . '.csv"');
        }
        
        return redirect()->back()->with('error', 'Invalid export format');
    }
    
    public function employees() {
    $employees = Employee::with(['user' => function ($query) {
        $query->with('roles');
    }])->get();
    $roles = Role::all(); // الآن سيعمل بشكل صحيح
    return view('admin.employees', compact('employees', 'roles'));
}


    public function products() {
        $products = \App\Models\Product::with('category')->get();
        $categories = \App\Models\Category::all();
        return view('admin.products', compact('products', 'categories'));
    }
    public function updateProduct(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
        ]);
    
        $product = \App\Models\Product::findOrFail($id);
        $product->update($data);
        
        // Log activity
        \App\Services\ActivityLogger::log(
            "Product '{$product->title}' was updated", 
            "📦", 
            $product
        );
    
        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
        ]);
    
        $product = \App\Models\Product::create($data);
        
        // Log activity
        \App\Services\ActivityLogger::log(
            "New product '{$product->title}' was added", 
            "📦", 
            $product
        );
    
        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    public function destroyProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $productName = $product->title;
        $product->delete();
        
        // Log activity
        \App\Services\ActivityLogger::log(
            "Product '{$productName}' was deleted", 
            "🗑️"
        );
        
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function suppliers() {
        $suppliers = \App\Models\Supplier::all();
        return view('admin.suppliers', compact('suppliers'));
    }


    public function orders() {
        $orders = \App\Models\Order::with('user')->get();
        return view('admin.orders', compact('orders'));
    }

    public function feedback() {
        $feedback = \App\Models\Feedback::with('user')->get();
        return view('admin.feedback', compact('feedback'));
    }

    public function categories() {
        $categories = \App\Models\Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function updateOrder(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = \App\Models\Order::findOrFail($id);
        $order->update($data);

        return redirect()->route('admin.orders')->with('success', 'Order status updated successfully');
    }
    public function activities() {
        $activities = \App\Models\Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        return view('admin.activities', compact('activities')); 
    }
    

    public function users() {
        $users = \App\Models\User::all();
        return view('admin.users', compact('users'));
    }

  
    public function reports()
    {
        return view('admin.reports.index');
    }

    public function salesReports()
    {
        // Logic for sales reports
        return view('admin.reports.sales');
    }

    public function inventoryReports()
    {
        // Logic for inventory reports
        return view('admin.reports.inventory');
    }

    public function employeeReports()
    {
        // Logic for employee reports
        return view('admin.reports.employees');
    }
    

    public function customerReports()
    {
        // Logic for customer reports
        return view('admin.reports.customers');
    }


}
