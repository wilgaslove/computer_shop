<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Categories
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            // Products 
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user  = Role::firstOrCreate(['name' => 'user']);

        // Role Super Admin → tout
        // Role::firstOrCreate(['name' => 'super-admin'])
        //     ->givePermissionTo(Permission::all());


        // Admin → tout
        $admin->syncPermissions($permissions);

        // User → accès limité
        $user->syncPermissions([
            'dashboard.view',
        ]);
    }
}
