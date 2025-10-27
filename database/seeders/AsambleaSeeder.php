<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asamblea;
use App\Models\Organo;
use App\Models\Miembro;
use App\Models\User;
use Carbon\Carbon;

class AsambleaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener órganos, miembros y usuarios existentes
        $organos = Organo::all();
        $miembros = Miembro::all();
        $users = User::all();

        if ($organos->isEmpty() || $miembros->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay órganos, miembros o usuarios disponibles. Ejecute primero los seeders correspondientes.');
            return;
        }

        $asambleas = [
            [
                'organizacion_id' => 1,
                'titulo' => 'Asamblea General Ordinaria - Enero 2025',
                'descripcion' => 'Reunión mensual para revisar el estado financiero, aprobar el presupuesto anual y tomar decisiones importantes sobre el futuro de la organización.',
                'fecha_convocatoria' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s'),
                'fecha_asamblea' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
                'lugar' => 'Auditorio Principal - Sede Central CLDCI',
                'tipo' => 'ordinaria',
                'modalidad' => 'presencial',
                'quorum_minimo' => 50,
                'estado' => 'convocada',
                'asistentes_count' => 35,
                'quorum_alcanzado' => false
            ],
            [
                'organizacion_id' => 1,
                'titulo' => 'Asamblea Extraordinaria - Proyecto Comunitario',
                'descripcion' => 'Asamblea especial para discutir y aprobar el nuevo proyecto comunitario de desarrollo social en el barrio.',
                'fecha_convocatoria' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'fecha_asamblea' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                'lugar' => 'Centro Comunitario - Barrio Norte',
                'tipo' => 'extraordinaria',
                'modalidad' => 'presencial',
                'quorum_minimo' => 30,
                'estado' => 'convocada',
                'asistentes_count' => 25,
                'quorum_alcanzado' => false
            ],
            [
                'organizacion_id' => 1,
                'titulo' => 'Asamblea Especial - Reforma Estatutaria',
                'descripcion' => 'Asamblea especial para discutir y aprobar las reformas propuestas a los estatutos de la organización.',
                'fecha_convocatoria' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                'fecha_asamblea' => Carbon::now()->addDays(10)->format('Y-m-d H:i:s'),
                'lugar' => 'Salón de Convenciones - Hotel Central',
                'tipo' => 'especial',
                'modalidad' => 'presencial',
                'quorum_minimo' => 75,
                'estado' => 'convocada',
                'asistentes_count' => 60,
                'quorum_alcanzado' => false
            ],
            [
                'organizacion_id' => 1,
                'titulo' => 'Asamblea Ordinaria - Febrero 2025',
                'descripcion' => 'Reunión mensual regular para seguimiento de actividades y toma de decisiones administrativas.',
                'fecha_convocatoria' => Carbon::now()->subDays(3)->format('Y-m-d H:i:s'),
                'fecha_asamblea' => Carbon::now()->addDays(15)->format('Y-m-d H:i:s'),
                'lugar' => 'Auditorio Principal - Sede Central CLDCI',
                'tipo' => 'ordinaria',
                'modalidad' => 'presencial',
                'quorum_minimo' => 40,
                'estado' => 'convocada',
                'asistentes_count' => 32,
                'quorum_alcanzado' => false
            ],
            [
                'organizacion_id' => 1,
                'titulo' => 'Asamblea Extraordinaria - Emergencia',
                'descripcion' => 'Asamblea de emergencia para tratar asuntos urgentes que requieren atención inmediata.',
                'fecha_convocatoria' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                'fecha_asamblea' => Carbon::now()->addDays(20)->format('Y-m-d H:i:s'),
                'lugar' => 'Sala de Juntas - Sede Central',
                'tipo' => 'extraordinaria',
                'modalidad' => 'presencial',
                'quorum_minimo' => 25,
                'estado' => 'convocada',
                'asistentes_count' => 20,
                'quorum_alcanzado' => false
            ]
        ];

        foreach ($asambleas as $asamblea) {
            Asamblea::create(array_merge($asamblea, [
                'created_by' => $users->first()->id,
            ]));
        }

        $this->command->info('Se crearon ' . count($asambleas) . ' asambleas de muestra.');
    }
}