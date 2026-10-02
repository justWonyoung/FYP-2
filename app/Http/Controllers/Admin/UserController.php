<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
{
    return view(
        'admin.users.create'
    );
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'role'         => 'required|in:admin,staff,finance',
            'password'     => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status']   = 'active';

        User::create($validated);

        return redirect()->back()->with('success', 'User account created successfully.');
    }

public function edit(User $user)
{
    return view(
        'admin.users.edit',
        compact('user')
    );
}

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $user->getKey() . ',' . $user->getKeyName(),
            'email'        => 'required|email|unique:users,email,' . $user->getKey() . ',' . $user->getKeyName(),
            'phone_number' => 'nullable|string|max:20',
            'role'         => 'required|in:admin,staff,finance',
            'status'       => 'required|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->getKey() === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}