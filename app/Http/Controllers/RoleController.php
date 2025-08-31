<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? $validated['display_name'],
        ]);

        $role->permissions()->attach($validated['permissions']);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? $validated['display_name'],
        ]);

        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete Super Admin role');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
