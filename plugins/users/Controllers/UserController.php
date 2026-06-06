<?php

namespace Plugins\users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(20);
        return view('users::users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users::users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users|alpha_dash',
            'email'    => 'required|email|unique:users',
            'password' => ['required', Password::min(8)],
            'roles'    => 'array',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->filled('username') ? $request->username : null,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', "User {$user->name} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $roles       = Role::orderBy('name')->get();
        $userRoles   = $user->roles->pluck('name')->toArray();
        return view('users::users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id . '|alpha_dash',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
            'roles'    => 'array',
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->filled('username') ? $request->username : null,
            'email'    => $request->email,
            ...($request->filled('password') ? ['password' => Hash::make($request->password)] : []),
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->route('users.index')->with('success', "User {$user->name} berhasil diupdate.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', "User {$user->name} berhasil dihapus.");
    }
}
