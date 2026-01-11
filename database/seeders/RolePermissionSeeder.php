<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permissions
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'view dashboard']);

        // Roles
        $admin = Role::create(['name' => 'admin']);
        $user  = Role::create(['name' => 'user']);

        // Attribution permissions
        $admin->givePermissionTo(Permission::all());
        $user->givePermissionTo('view dashboard');
    }
}
