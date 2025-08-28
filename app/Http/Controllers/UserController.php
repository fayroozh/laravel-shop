<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * عرض المستخدمين داخل صفحة واحدة (Blade) + JSON للـ React
     */
    public function index(Request $request)
    {
        $users = User::with('roles', 'employee')->latest()->get();
        $roles = Role::all();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users', compact('users', 'roles'));
    }

    /**
     * إرجاع المستخدمين كـ JSON للـ API
     */
    public function apiIndex()
    {
        $users = User::with('roles', 'employee')->latest()->get();
        return response()->json($users);
    }

    /**
     * إظهار نموذج إنشاء مستخدم جديد
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * تخزين مستخدم جديد
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        if (isset($validatedData['role'])) {
            $user->assignRole($validatedData['role']);
            $role = Role::findByName($validatedData['role'], 'web');

            if ($role && in_array($role->name, ['employee', 'sales_manager'])) {
                $employee = Employee::firstOrNew(['user_id' => $user->id]);
                $employee->position = $validatedData['position'];
                $employee->department = $validatedData['department'];
                $employee->mobile = $validatedData['mobile'];
                $employee->save();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '✅ تم إنشاء المستخدم بنجاح',
                'user' => $user
            ], 201);
        }

        return redirect()->route('admin.users')->with('success', '✅ تم إنشاء المستخدم بنجاح');
    }

    /**
     * تحديث مستخدم موجود
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        if (isset($validatedData['role'])) {
            $user->syncRoles([$validatedData['role']]);
            $role = Role::findByName($validatedData['role'], 'web');

            if ($role && in_array($role->name, ['employee', 'sales_manager'])) {
                // تحديث أو إنشاء سجل موظف
                $employee = Employee::firstOrNew(['user_id' => $user->id]);
                $employee->name = $validatedData['name'];
                $employee->email = $validatedData['email'];
                $employee->position = $validatedData['position'];
                $employee->department = $validatedData['department'];
                $employee->mobile = $validatedData['mobile'];
                $employee->role_id = $role->id;
                $employee->save();
            } else {
                // إذا الدور الجديد مو موظف نحذف من employees
                $employee = Employee::where('user_id', $user->id)->first();
                if ($employee) {
                    $employee->delete();
                }
            }
        } else {
            // إذا ما إلو دور → نحذف أي سجل موظف
            $user->syncRoles([]);
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                $employee->delete();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '✅ تم تحديث المستخدم بنجاح',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:light,dark',
        ]);

        $user = $request->user();
        $user->theme = $request->theme;
        $user->save();

        return response()->json(['message' => 'Theme updated successfully.']);
    }

    /**
     * حذف مستخدم
     */
    public function destroy(Request $request, User $user)
    {
        // نحذف الموظف إذا كان مربوط
        $employee = Employee::where('user_id', $user->id)->first();
        if ($employee) {
            $employee->delete();
        }

        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '🗑️ تم حذف المستخدم بنجاح'
            ]);
        }

        return redirect()->route('admin.users')->with('success', '🗑️ تم حذف المستخدم بنجاح');
    }


    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->fill($validatedData);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => '✅ تم تحديث الملف الشخصي بنجاح',
            'user' => $user
        ]);
    }


    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

}