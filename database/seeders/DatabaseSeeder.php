<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crée d'abord les rôles et permissions
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Boss',
            'email' => 'Wilman@gmail.com',
            'password' => bcrypt('a'),
        ]);

        // Assigner le rôle admin (nécessite spatie/laravel-permission)
        $user->assignRole('admin');
    }


}
