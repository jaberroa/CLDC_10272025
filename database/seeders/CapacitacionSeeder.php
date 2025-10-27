<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capacitacion;
use App\Models\Organizacion;
use App\Models\User;
use Carbon\Carbon;

class CapacitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la primera organización y usuario
        $organizacion = Organizacion::first();
        $usuario = User::first();

        if (!$organizacion || !$usuario) {
            $this->command->warn('No se encontraron organizaciones o usuarios. Ejecute primero los seeders de organizaciones y usuarios.');
            return;
        }

        $capacitaciones = [
            [
                'titulo' => 'Curso de Locución Profesional',
                'descripcion' => 'Capacitación completa en técnicas de locución, dicción y expresión vocal para profesionales de la comunicación.',
                'fecha_inicio' => Carbon::now()->addDays(15),
                'fecha_fin' => Carbon::now()->addDays(17),
                'lugar' => 'Estudio de Grabación CLDCI',
                'modalidad' => 'presencial',
                'costo' => 2500.00,
                'cupo_maximo' => 20,
                'instructor' => 'María González - Locutora Profesional',
                'activo' => true
            ],
            [
                'titulo' => 'Taller de Producción Radiofónica',
                'descripcion' => 'Aprende las técnicas fundamentales de producción y edición de contenido radiofónico.',
                'fecha_inicio' => Carbon::now()->addDays(30),
                'fecha_fin' => Carbon::now()->addDays(32),
                'lugar' => 'Laboratorio de Producción CLDCI',
                'modalidad' => 'presencial',
                'costo' => 1800.00,
                'cupo_maximo' => 15,
                'instructor' => 'Carlos Rodríguez - Productor Ejecutivo',
                'activo' => true
            ],
            [
                'titulo' => 'Seminario de Comunicación Digital',
                'descripcion' => 'Estrategias y herramientas para la comunicación efectiva en medios digitales.',
                'fecha_inicio' => Carbon::now()->addDays(45),
                'fecha_fin' => Carbon::now()->addDays(47),
                'lugar' => 'Aula Virtual CLDCI',
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://meet.google.com/cldci-virtual',
                'costo' => 1200.00,
                'cupo_maximo' => 50,
                'instructor' => 'Ana Martínez - Especialista en Marketing Digital',
                'activo' => true
            ],
            [
                'titulo' => 'Curso de Periodismo Deportivo',
                'descripcion' => 'Especialización en cobertura deportiva, redacción deportiva y análisis de eventos.',
                'fecha_inicio' => Carbon::now()->addDays(60),
                'fecha_fin' => Carbon::now()->addDays(62),
                'lugar' => 'Centro de Entrenamiento CLDCI',
                'modalidad' => 'mixta',
                'costo' => 3000.00,
                'cupo_maximo' => 25,
                'instructor' => 'Roberto Silva - Periodista Deportivo',
                'activo' => true
            ],
            [
                'titulo' => 'Taller de Técnicas de Entrevista',
                'descripcion' => 'Desarrollo de habilidades para realizar entrevistas efectivas en radio y televisión.',
                'fecha_inicio' => Carbon::now()->addDays(75),
                'fecha_fin' => Carbon::now()->addDays(77),
                'lugar' => 'Estudio de Televisión CLDCI',
                'modalidad' => 'presencial',
                'costo' => 2000.00,
                'cupo_maximo' => 18,
                'instructor' => 'Laura Fernández - Conductora de TV',
                'activo' => true
            ]
        ];

        foreach ($capacitaciones as $capacitacion) {
            Capacitacion::create($capacitacion);
        }

        $this->command->info('Capacitaciones creadas exitosamente.');
    }
}