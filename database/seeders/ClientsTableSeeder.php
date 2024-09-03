<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'surname' => 'Lo',
            'telephone' => '785619116',
            'adresse' => 'Dakar, Sénégal',
            'user_id' => null, // Ou l'ID d'un utilisateur existant
        ]);
    }
}
