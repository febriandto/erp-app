<?php

namespace Plugins\users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('users::roles.index', compact('roles'));
    }

    public function create()
    {
        return view('users::roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('users.roles.index')
            ->with('success', "Role '{$request->name}' berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        $permissions    = Permission::orderBy('name')->get()->groupBy(fn($p) => explode('.', $p->name)[0]);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('users::roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('users.roles.index')
            ->with('success', "Role '{$role->name}' berhasil diupdate.");
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', "Role '{$role->name}' masih digunakan oleh {$role->users()->count()} user.");
        }

        $role->delete();
        return redirect()->route('users.roles.index')
            ->with('success', "Role '{$role->name}' berhasil dihapus.");
    }
}
