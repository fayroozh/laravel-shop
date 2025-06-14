<?php

namespace App\Reports;

use App\Models\Employee;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeePerformanceReport extends Report
{
    public function generate()
    {
        // أداء الموظفين (افتراضياً أن الطلبات مرتبطة بالموظفين)
        $employeePerformance = DB::table('orders')
            ->join('employees', 'orders.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$this->startDate, $this->endDate])
            ->select(
                'employees.id',
                'users.name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_sales')
            )
            ->groupBy('employees.id', 'users.name')
            ->orderBy('total_sales', 'desc')
            ->get();
            
        // أنشطة الموظفين
        $employeeActivities = DB::table('activities')
            ->join('users', 'activities.user_id', '=', 'users.id')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->whereBetween('activities.created_at', [$this->startDate, $this->endDate])
            ->select(
                'employees.id',
                'users.name',
                DB::raw('COUNT(activities.id) as activity_count')
            )
            ->groupBy('employees.id', 'users.name')
            ->orderBy('activity_count', 'desc')
            ->get();
            
        return [
            'employee_performance' => $employeePerformance,
            'employee_activities' => $employeeActivities,
        ];
    }
}