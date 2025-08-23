<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('admin.employees', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees|unique:users',
            'position' => 'required|string',
            'mobile' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        if ($data['role_id']) {
            $user->roles()->attach($data['role_id']);
        }

        $employee = Employee::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'position' => $data['position'],
            'mobile' => $data['mobile'] ?? null,
            'user_id' => $user->id
        ]);

        ActivityLogger::log("Added new employee: {$employee->name}", "👨‍💼", $employee);

        return redirect()->route('admin.employees')->with('success', 'Employee added successfully');
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|unique:users,email,' . $employee->user_id,
            'position' => 'required|string',
            'mobile' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $employee->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'position' => $data['position'],
            'mobile' => $data['mobile'] ?? $employee->mobile
        ]);

        if ($employee->user) {
            $userUpdate = [
                'name' => $data['name'],
                'email' => $data['email']
            ];
            if (!empty($data['password'])) {
                $userUpdate['password'] = Hash::make($data['password']);
            }
            $employee->user->update($userUpdate);
            $employee->user->roles()->sync([$data['role_id']]);
        }

        ActivityLogger::log("Updated employee: {$employee->name}", "👨‍💼", $employee);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->user) {
            $employee->user->delete();
        }

        ActivityLogger::log("Deleted employee: {$employee->name}", "👨‍💼", $employee);

        $employee->delete();

        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully');
    }
}