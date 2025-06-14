<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogger;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();    
        $roles = Role::all();     
        return view('admin.users', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        $user->assignRole($validatedData['role']);

        // إنشاء سجل موظف إذا كان الدور موظف أو مشرف
        if (in_array($validatedData['role'], ['employee', 'admin'])) {
            Employee::create([
                'user_id' => $user->id,
                'position' => $request->input('position', 'Staff'),
                'department' => $request->input('department', 'General'),
                'phone' => $request->input('phone')
            ]);
        }

        return response()->json(['message' => 'تم إنشاء المستخدم بنجاح', 'user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name'
        ]);

        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email']
        ]);

        // تحديث الدور
        $user->syncRoles([$validatedData['role']]);

        // التعامل مع سجل الموظف
        if (in_array($validatedData['role'], ['employee', 'admin'])) {
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'position' => $request->input('position', 'Staff'),
                    'department' => $request->input('department', 'General'),
                    'phone' => $request->input('phone')
                ]
            );
        } else {
            // حذف سجل الموظف إذا تم تغيير الدور إلى مستخدم عادي
            Employee::where('user_id', $user->id)->delete();
        }

        return response()->json(['message' => 'تم تحديث المستخدم بنجاح', 'user' => $user]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account');
        }

        $userName = $user->name;
        $user->employee()->delete(); // حذف الموظف إذا كان موظفًا
        $user->delete();

        ActivityLogger::log("Deleted user '{$userName}'", "🗑️");

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}
