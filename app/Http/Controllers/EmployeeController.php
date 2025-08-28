<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        // جلب الموظفين مع المستخدم والدور
        $employees = Employee::with('user.roles')->get();
        $roles = Role::all();
        return view('admin.employees', compact('employees', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'position' => 'required|string',
            'mobile' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        // إنشاء مستخدم
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ربط الدور بالمستخدم
        $user->roles()->attach($data['role_id']);

        // إنشاء موظف وربطه بالمستخدم
        Employee::create([
            'position' => $data['position'],
            'mobile' => $data['mobile'] ?? null,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee added successfully');
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'position' => 'required|string',
            'mobile' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        // تحديث الموظف (جدول employees)
        $employee->update([
            'position' => $data['position'],
            'mobile' => $data['mobile'] ?? $employee->mobile,
        ]);

        // تحديث المستخدم المرتبط (جدول users)
        $userUpdate = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $userUpdate['password'] = Hash::make($data['password']);
        }

        $employee->user->update($userUpdate);

        // مزامنة الدور
        $employee->user->roles()->sync([$data['role_id']]);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        // حذف المستخدم المرتبط
        if ($employee->user) {
            $employee->user->roles()->detach(); // إزالة أي أدوار مرتبطة
            $employee->user->delete();
        }

        $employee->delete();

        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully');
    }
}