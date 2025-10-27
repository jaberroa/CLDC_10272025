<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capacitacion;
use Carbon\Carbon;

class ProximosCursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proximosCursos = [
            [
                'titulo' => 'Curso Intensivo de Locución en Vivo',
                'descripcion' => 'Capacitación intensiva de 2 días para dominar las técnicas de locución en transmisiones en vivo.',
                'fecha_inicio' => Carbon::now()->addDays(3),
                'fecha_fin' => Carbon::now()->addDays(4),
                'lugar' => 'Estudio Principal CLDCI',
                'modalidad' => 'presencial',
                'costo' => 1500.00,
                'cupo_maximo' => 12,
                'instructor' => 'Patricia Morales - Locutora Senior',
                'activo' => true
            ],
            [
                'titulo' => 'Workshop de Podcasting Profesional',
                'descripcion' => 'Aprende a crear, producir y distribuir podcasts de calidad profesional.',
                'fecha_inicio' => Carbon::now()->addDays(7),
                'fecha_fin' => Carbon::now()->addDays(8),
                'lugar' => 'Laboratorio de Audio CLDCI',
                'modalidad' => 'presencial',
                'costo' => 2200.00,
                'cupo_maximo' => 16,
                'instructor' => 'Miguel Torres - Productor de Podcasts',
                'activo' => true
            ],
            [
                'titulo' => 'Seminario de Comunicación Corporativa',
                'descripcion' => 'Estrategias de comunicación interna y externa para organizaciones.',
                'fecha_inicio' => Carbon::now()->addDays(10),
                'fecha_fin' => Carbon::now()->addDays(11),
                'lugar' => 'Aula Magna CLDCI',
                'modalidad' => 'mixta',
                'costo' => 1800.00,
                'cupo_maximo' => 30,
                'instructor' => 'Dr. Carmen Vega - Comunicóloga',
                'activo' => true
            ],
            [
                'titulo' => 'Taller de Redacción para Medios',
                'descripcion' => 'Técnicas de redacción clara y efectiva para radio, prensa y digital.',
                'fecha_inicio' => Carbon::now()->addDays(14),
                'fecha_fin' => Carbon::now()->addDays(15),
                'lugar' => 'Sala de Redacción CLDCI',
                'modalidad' => 'presencial',
                'costo' => 1300.00,
                'cupo_maximo' => 20,
                'instructor' => 'José Ramírez - Editor Jefe',
                'activo' => true
            ],
            [
                'titulo' => 'Curso de Técnicas de Moderación',
                'descripcion' => 'Desarrollo de habilidades para moderar debates, foros y eventos públicos.',
                'fecha_inicio' => Carbon::now()->addDays(18),
                'fecha_fin' => Carbon::now()->addDays(19),
                'lugar' => 'Auditorio CLDCI',
                'modalidad' => 'presencial',
                'costo' => 2000.00,
                'cupo_maximo' => 14,
                'instructor' => 'Elena Castillo - Moderadora Profesional',
                'activo' => true
            ]
        ];

        foreach ($proximosCursos as $curso) {
            Capacitacion::create($curso);
        }

        $this->command->info('Próximos cursos creados exitosamente.');
    }
}

