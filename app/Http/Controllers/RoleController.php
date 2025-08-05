<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->get(),
        ]);
    }

    public function edit(Role $role)
    {
        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->syncPermissions($data['permissions']);

        return redirect()->route('admin.roles.index')->with('success', 'Permissions updated');
    }

    public function toggleActive(Request $request, User $user)
    {
    $user->is_active = $request->is_active;
    $user->save();

    return response()->json(['success' => true]);
    }

}
