<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super admin', 'admin', 'user'];
        foreach ($roles as $r) Role::firstOrCreate(['name' => $r]);

        $permissions = ['manage users', 'edit articles', 'view dashboard'];
        foreach ($permissions as $p) Permission::firstOrCreate(['name' => $p]);

        Role::findByName('admin')->givePermissionTo(['edit articles', 'view dashboard']);
        Role::findByName('user')->givePermissionTo(['view dashboard']);
        Role::findByName('super admin')->givePermissionTo(Permission::all());

        $user = User::first(); // Assign super admin ke user pertama
        $user?->assignRole('super admin');
    }
}
