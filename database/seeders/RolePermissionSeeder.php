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


        Permission::create(['name' => 'category.view']);
        Permission::create(['name' => 'category.create']);
        Permission::create(['name' => 'category.edit']);
        Permission::create(['name' => 'category.delete']);


       
        // Roles
        $admin = Role::create(['name' => 'admin']);
        $user  = Role::create(['name' => 'user']);

        
        $admin->givePermissionTo([
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
        ]);

        
        // Attribution permissions
        $admin->givePermissionTo(Permission::all());
        $user->givePermissionTo('view dashboard');
    }
}
