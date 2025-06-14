<?php

namespace App\Reports;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReport extends Report
{
    public function generate()
    {
        // إجمالي المبيعات حسب الفترة
        $totalSales = Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('total_amount');
            
        // عدد الطلبات حسب الفترة
        $orderCount = Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();
            
        // متوسط قيمة الطلب
        $averageOrderValue = $orderCount > 0 ? $totalSales / $orderCount : 0;
        
        // المبيعات حسب اليوم
        $salesByDay = Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total
                ];
            });
            
        // المنتجات الأكثر مبيعاً
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$this->startDate, $this->endDate])
            ->select(
                'products.id',
                'products.title',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.title')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();
            
        return [
            'summary' => [
                'total_sales' => $totalSales,
                'order_count' => $orderCount,
                'average_order_value' => $averageOrderValue,
            ],
            'sales_by_day' => $salesByDay,
            'top_products' => $topProducts,
        ];
    }
}