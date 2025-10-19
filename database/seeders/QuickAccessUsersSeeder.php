<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class QuickAccessUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de acceso rápido
        $quickAccessUsers = [
            [
                'name' => 'Administrador CLDCI',
                'email' => 'admin@cldci.org',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de Miembros',
                'email' => 'miembros@cldci.org',
                'password' => Hash::make('miembros123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Directiva CLDCI',
                'email' => 'directiva@cldci.org',
                'password' => Hash::make('directiva123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($quickAccessUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Usuarios de acceso rápido creados exitosamente');
        $this->command->info('📧 admin@cldci.org / admin123');
        $this->command->info('📧 miembros@cldci.org / miembros123');
        $this->command->info('📧 directiva@cldci.org / directiva123');
    }
}
