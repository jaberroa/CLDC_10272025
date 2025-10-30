<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TiposOrganizacionSeeder;
use Database\Seeders\OrganizacionesSeeder;
use Database\Seeders\MiembrosSeeder;
use Database\Seeders\RolesPermisosSeeder;
use Database\Seeders\CldciInitialDataSeeder;
use Database\Seeders\CldciDataSeeder;
use Database\Seeders\NoticiasSeeder;
use Database\Seeders\CapacitacionSeeder;
use Database\Seeders\CuotasSeeder;
use Database\Seeders\EleccionesSeeder;
use Database\Seeders\DirectivaSeeder;
use Database\Seeders\AsambleaSeeder;
use Database\Seeders\CronogramaDirectivaSeeder;
use Database\Seeders\CarnetTemplateSeeder;
use Database\Seeders\SeccionesDocumentalesSeeder;
use Database\Seeders\QuickAccessUsersSeeder;

class RunAllSeeders extends Command
{
    protected $signature = 'seeders:run-all';
    protected $description = 'Ejecutar todos los seeders disponibles';

    public function handle()
    {
        $this->info('Iniciando ejecución de todos los seeders...');

        $seeders = [
            'TiposOrganizacionSeeder' => TiposOrganizacionSeeder::class,
            'OrganizacionesSeeder' => OrganizacionesSeeder::class,
            'MiembrosSeeder' => MiembrosSeeder::class,
            'RolesPermisosSeeder' => RolesPermisosSeeder::class,
            'CldciInitialDataSeeder' => CldciInitialDataSeeder::class,
            'CldciDataSeeder' => CldciDataSeeder::class,
            'NoticiasSeeder' => NoticiasSeeder::class,
            'CapacitacionSeeder' => CapacitacionSeeder::class,
            'CuotasSeeder' => CuotasSeeder::class,
            'EleccionesSeeder' => EleccionesSeeder::class,
            'DirectivaSeeder' => DirectivaSeeder::class,
            'AsambleaSeeder' => AsambleaSeeder::class,
            'CronogramaDirectivaSeeder' => CronogramaDirectivaSeeder::class,
            'CarnetTemplateSeeder' => CarnetTemplateSeeder::class,
            'SeccionesDocumentalesSeeder' => SeccionesDocumentalesSeeder::class,
            'QuickAccessUsersSeeder' => QuickAccessUsersSeeder::class,
        ];

        foreach ($seeders as $name => $seederClass) {
            try {
                $this->info("Ejecutando: $name");
                $this->call('db:seed', ['--class' => $seederClass]);
                $this->info("✅ $name completado");
            } catch (\Exception $e) {
                $this->error("❌ Error en $name: " . $e->getMessage());
            }
        }

        $this->info('Todos los seeders han sido ejecutados.');
    }
}

