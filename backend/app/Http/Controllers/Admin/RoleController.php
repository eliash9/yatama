<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $allPermissions = Permission::orderBy('name')->get();
        return view('admin.roles.index', compact('roles','allPermissions'));
    }

    public function storePermission(Request $request)
    {
        $data = $request->validate(['name' => 'required|string']);
        Permission::findOrCreate($data['name'], 'web');
        return back()->with('status','Permission ditambahkan');
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $data = $request->validate(['permissions' => 'array', 'permissions.*' => 'string']);
        $perms = $data['permissions'] ?? [];
        $role->syncPermissions($perms);
        return back()->with('status','Permission peran diperbarui');
    }
}

