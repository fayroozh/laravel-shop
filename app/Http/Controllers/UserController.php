<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::all();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users', compact('users', 'roles'));
    }

    public function apiIndex()
    {
        $users = User::with('roles')->latest()->get();
        return response()->json($users);
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // By default, new users are customers
        $user->assignRole('customer');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'customer created successfully',
                'user' => $user
            ], 201);
        }

        return redirect()->route('admin.users')->with('success', 'customer created successfully');
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'customer updated successfully',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'customer updated successfully');
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

    public function destroy(Request $request, User $user)
    {
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'customer deleted successfully'
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'customer deleted successfully');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'firstName' => 'sometimes|required|string|max:255',
            'lastName' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8'
        ]);

        if (isset($validatedData['firstName']) && isset($validatedData['lastName'])) {
            $user->name = $validatedData['firstName'] . ' ' . $validatedData['lastName'];
        }

        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully.', 'user' => $user]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}