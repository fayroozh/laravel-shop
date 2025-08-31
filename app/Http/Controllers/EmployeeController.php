<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('role')->get();
        $roles = Role::all();

        return view('admin.employees', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ]);

        Employee::create($data);

        return redirect()->route('admin.employees')->with('success', 'Employee created successfully');
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position' => 'required|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ]);

        $employee->update($data);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully');
    }
}