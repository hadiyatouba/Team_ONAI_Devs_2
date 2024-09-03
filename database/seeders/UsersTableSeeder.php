<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        User::factory()->create([
            'role_id' => $roles['ADMIN'],
            'nom' => 'Admin',
            'prenom' => 'User',
            'login' => 'admin11',
            'password' => bcrypt('Sonatel@2024'),
        ]);

        User::factory()->create([
            'role_id' => $roles['CLIENT'],
            'nom' => 'Client',
            'prenom' => 'User',
            'login' => 'client11',
            'password' => bcrypt('Sonatel@2024'),
        ]);
    }
}


