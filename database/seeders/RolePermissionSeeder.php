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

            // Hero Slider
            'hero-slider.view',
            'hero-slider.create',
            'hero-slider.edit',
            'hero-slider.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        // Admin → toutes les permissions
        $admin->syncPermissions($permissions);

        // Manager → permissions limitées (à adapter ensuite)
        $manager->syncPermissions([
            'dashboard.view',
            'product.view',
            'product.create',
            'product.edit',
            'category.view',
            'hero-slider.view',
        ]);

        // Customer → aucune permission d'administration
        $customer->syncPermissions([]);
    }
}
