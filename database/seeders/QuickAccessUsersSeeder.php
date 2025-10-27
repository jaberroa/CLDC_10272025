<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class QuickAccessUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario directiva
        User::firstOrCreate(
            ['email' => 'directiva@cldci.org'],
            [
                'name' => 'Directiva CLDCI',
                'password' => Hash::make('directiva123'),
                'email_verified_at' => now()
            ]
        );

        // Crear usuario admin
        User::firstOrCreate(
            ['email' => 'admin@cldci.org'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now()
            ]
        );

        // Crear usuario secretario
        User::firstOrCreate(
            ['email' => 'secretario@cldci.org'],
            [
                'name' => 'Secretario',
                'password' => Hash::make('secretario123'),
                'email_verified_at' => now()
            ]
        );

        $this->command->info('Usuarios de acceso rápido creados exitosamente.');
    }
}