<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Order;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Feedback;
use App\Models\Role;
use App\Models\User;
use App\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardReportExport;
use Illuminate\Support\Facades\DB;



use Illuminate\Support\Facades\Auth;
class AdminController extends Controller
{
    public function dashboard()
    {
        // Statistics
        $products = Product::count();
        $orders = Order::count();
        $employees = Employee::count();
        $suppliers = Supplier::count();
        $feedback = Feedback::count();
        $totalRevenue = Order::sum('total');
        $totalCustomers = User::whereHas('orders')->count();


        // Monthly stats
        $productsThisMonth = Product::whereMonth('created_at', now()->month)->count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)->count();
        $employeesThisMonth = Employee::whereMonth('created_at', now()->month)->count();
        $feedbackThisMonth = Feedback::whereMonth('created_at', now()->month)->count();
        $revenueThisMonth = Order::whereMonth('created_at', now()->month)->sum('total');

        // Chart data for the last 12 months
        $monthlyOrders = [];
        $monthlyRevenue = [];
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('F Y');
            $months[] = $monthName;

            $ordersInMonth = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            $monthlyOrders[] = (int) $ordersInMonth->clone()->count();
            $monthlyRevenue[] = (float) $ordersInMonth->clone()->sum('total');
        }

        // Data for "Top Selling Products" chart
        $topSellingProducts = Product::withCount([
            'orderItems as quantity_sold' => function ($query) {
                $query->select(DB::raw('sum(quantity)'));
            }
        ])
            ->orderBy('quantity_sold', 'desc')
            ->take(5)
            ->get();

        // Data for "Stock by Category" chart
        $stockByCategory = \App\Models\Category::withCount('products')
            ->withSum('products', 'stock')
            ->get();

        // Recent Activities
        $recentActivities = Activity::latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'products',
            'orders',
            'employees',
            'suppliers',
            'feedback',
            'totalRevenue',
            'totalCustomers',
            'productsThisMonth',
            'ordersThisMonth',
            'employeesThisMonth',
            'feedbackThisMonth',
            'revenueThisMonth',
            'monthlyOrders',
            'monthlyRevenue',
            'months',
            'recentActivities',
            'topSellingProducts',
            'stockByCategory'
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
            'totalRevenue' => \App\Models\Order::sum('total') ?? 0,
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



    public function salesReports()
    {
        // Daily Sales
        $dailySales = Order::whereDate('created_at', Carbon::today())->sum('total');

        // Monthly Sales
        $monthlySales = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        // Yearly Sales
        $yearlySales = Order::whereYear('created_at', Carbon::now()->year)->sum('total');

        // Top 5 Selling Products
        $topSellingProducts = Product::withCount([
            'orderItems as quantity_sold' => function ($query) {
                $query->select(DB::raw('sum(quantity)'));
            }
        ])
            ->orderBy('quantity_sold', 'desc')
            ->take(5)
            ->get();

        // Sales data for the last 30 days (for a chart)
        $salesLast30Days = [];
        $days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            $salesLast30Days[] = (float) Order::whereDate('created_at', $date)->sum('total');
        }

        return view('admin.reports.sales', compact(
            'dailySales',
            'monthlySales',
            'yearlySales',
            'topSellingProducts',
            'salesLast30Days',
            'days'
        ));
    }

    public function inventoryReports()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalStockValue = Product::select(DB::raw('sum(stock * price) as total_value'))->first()->total_value;

        $lowStockProducts = Product::where('stock', '<', 10)->where('stock', '>', 0)->orderBy('stock', 'asc')->get();
        $outOfStockProducts = Product::where('stock', '=', 0)->get();

        $stockByCategory = \App\Models\Category::withSum('products', 'stock')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'stock' => (int) $category->products_sum_stock,
                ];
            });

        return view('admin.reports.inventory', compact(
            'totalProducts',
            'totalStock',
            'totalStockValue',
            'lowStockProducts',
            'outOfStockProducts',
            'stockByCategory'
        ));
    }


    public function employees()
    {
        $employees = Employee::with([
            'user' => function ($query) {
                $query->with('roles');
            }
        ])->get();
        $roles = Role::all(); // الآن سيعمل بشكل صحيح
        return view('admin.employees', compact('employees', 'roles'));
    }


    public function products()
    {
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

    public function suppliers()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('admin.suppliers', compact('suppliers'));
    }


    public function orders()
    {
        $orders = \App\Models\Order::with('user')->get();
        return view('admin.orders', compact('orders'));
    }

    public function feedback()
    {
        $feedback = \App\Models\Feedback::with('user')->get();
        return view('admin.feedback', compact('feedback'));
    }

    public function categories()
    {
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
    public function activities()
    {
        $activities = \App\Models\Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        return view('admin.activities', compact('activities'));
    }


    public function users()
    {
        $users = \App\Models\User::all();
        return view('admin.users', compact('users'));
    }


    public function reports()
    {
        return view('admin.reports.index', ['reportTitle' => 'Advanced Reports']);
    }

    public function employeeReports(Request $request)
    {
        $reportTitle = 'Employee Performance Report';

        // Fetch employees and their user data
        $query = Employee::with('user');

        // Dummy departments for filter
        $departments = ['Sales', 'Marketing', 'Support', 'Development'];

        // Apply filters if any
        if ($request->filled('department')) {
            // This is a dummy filter. In a real app, department would be a DB field.
            // We'll just let it pass for now as we don't have this field.
        }

        $employees = $query->paginate(10);

        // Generate fake performance data for each employee
        $employees->each(function ($employee) {
            $employee->tasks_completed = rand(10, 100);
            $employee->performance_score = round(rand(50, 99) + (rand(0, 9) / 10), 1);

            if ($employee->performance_score >= 90) {
                $employee->performance_level = 'high';
            } elseif ($employee->performance_score >= 70) {
                $employee->performance_level = 'average';
            } else {
                $employee->performance_level = 'low';
            }
        });

        // Filter by performance level after generating scores
        if ($request->filled('performance')) {
            $employees = $employees->filter(function ($employee) use ($request) {
                return $employee->performance_level == $request->input('performance');
            });
        }

        // Dummy data for charts
        $highPerformers = $employees->where('performance_level', 'high')->count();
        $averagePerformers = $employees->where('performance_level', 'average')->count();
        $lowPerformers = $employees->where('performance_level', 'low')->count();

        $monthlyLabels = ['January', 'February', 'March', 'April', 'May', 'June'];
        $monthlyScores = [rand(70, 85), rand(75, 90), rand(80, 95), rand(78, 88), rand(82, 92), rand(85, 98)];

        return view('admin.reports.employees', compact(
            'reportTitle',
            'employees',
            'departments',
            'highPerformers',
            'averagePerformers',
            'lowPerformers',
            'monthlyLabels',
            'monthlyScores'
        ));
    }


    public function customerReports(Request $request)
    {
        $reportTitle = 'Customer Report';

        // Get all user IDs from the employees table to identify who is an employee
        $employeeUserIds = \Illuminate\Support\Facades\DB::table('employees')->pluck('user_id');

        // Use query builder to find users who are not admins and not employees
        $query = \Illuminate\Support\Facades\DB::table('users')
            ->where('is_admin', false)
            ->whereNotIn('id', $employeeUserIds);

        // We can't easily eager load with query builder, so we'll fetch related data manually.
        $users = $query->paginate(10);

        // Manually fetch orders and calculate stats
        $userIds = $users->pluck('id');
        $orders = \App\Models\Order::whereIn('user_id', $userIds)->with('orderItems')->get()->groupBy('user_id');

        $customers = $users->map(function ($user) use ($orders) {
            $userOrders = $orders->get($user->id, collect());

            $user->total_orders = $userOrders->count();
            $user->total_spent = $userOrders->flatMap(function ($order) {
                return $order->orderItems;
            })->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            $user->last_purchase = $userOrders->max('created_at');

            // Dynamically assign customer type based on spending
            if ($user->total_spent > 1000) {
                $user->type = 'vip';
            } elseif ($user->total_orders > 5) {
                $user->type = 'regular';
            } else {
                $user->type = 'new';
            }

            return $user;
        });

        // Filter by type after dynamic assignment
        if ($request->filled('type')) {
            $customers = $customers->filter(function ($customer) use ($request) {
                return $customer->type == $request->input('type');
            });
        }

        // Data for charts
        $newCustomers = $customers->where('type', 'new')->count();
        $regularCustomers = $customers->where('type', 'regular')->count();
        $vipCustomers = $customers->where('type', 'vip')->count();

        // Dummy data for purchase trends
        $purchaseDates = collect([]);
        $purchaseAmounts = collect([]);
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $purchaseDates->push($date->format('M d'));
            $purchaseAmounts->push(rand(100, 1000));
        }

        return view('admin.reports.customers', [
            'reportTitle' => $reportTitle,
            'customers' => $customers,
            'newCustomers' => $newCustomers,
            'regularCustomers' => $regularCustomers,
            'vipCustomers' => $vipCustomers,
            'purchaseDates' => $purchaseDates,
            'purchaseAmounts' => $purchaseAmounts,
            'paginator' => $users // Pass the paginator instance to the view
        ]);
    }


}