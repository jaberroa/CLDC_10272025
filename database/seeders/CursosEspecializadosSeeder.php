<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capacitacion;
use Carbon\Carbon;

class CursosEspecializadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cursosEspecializados = [
            [
                'titulo' => 'Masterclass de Narración Deportiva',
                'descripcion' => 'Técnicas avanzadas de narración deportiva con énfasis en fútbol, béisbol y baloncesto.',
                'fecha_inicio' => Carbon::now()->addDays(5),
                'fecha_fin' => Carbon::now()->addDays(6),
                'lugar' => 'Estudio Deportivo CLDCI',
                'modalidad' => 'presencial',
                'costo' => 3500.00,
                'cupo_maximo' => 10,
                'instructor' => 'Carlos "El Profe" Mendoza - Narrador Deportivo',
                'activo' => true
            ],
            [
                'titulo' => 'Curso de Comunicación en Crisis',
                'descripcion' => 'Manejo de comunicación durante situaciones de crisis y emergencias.',
                'fecha_inicio' => Carbon::now()->addDays(12),
                'fecha_fin' => Carbon::now()->addDays(13),
                'lugar' => 'Centro de Crisis CLDCI',
                'modalidad' => 'mixta',
                'costo' => 2800.00,
                'cupo_maximo' => 15,
                'instructor' => 'Dra. Isabel Rojas - Especialista en Crisis',
                'activo' => true
            ],
            [
                'titulo' => 'Taller de Voz y Dicción Avanzada',
                'descripcion' => 'Perfeccionamiento de la voz, dicción y expresión para locutores profesionales.',
                'fecha_inicio' => Carbon::now()->addDays(20),
                'fecha_fin' => Carbon::now()->addDays(21),
                'lugar' => 'Laboratorio de Voz CLDCI',
                'modalidad' => 'presencial',
                'costo' => 2400.00,
                'cupo_maximo' => 8,
                'instructor' => 'Prof. Roberto Herrera - Fonoaudiólogo',
                'activo' => true
            ],
            [
                'titulo' => 'Seminario de Ética Periodística',
                'descripcion' => 'Principios éticos y responsabilidad social en el ejercicio del periodismo.',
                'fecha_inicio' => Carbon::now()->addDays(25),
                'fecha_fin' => Carbon::now()->addDays(26),
                'lugar' => 'Aula de Ética CLDCI',
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://meet.google.com/etica-cldci',
                'costo' => 1600.00,
                'cupo_maximo' => 40,
                'instructor' => 'Dr. Manuel Ortega - Ético Periodístico',
                'activo' => true
            ],
            [
                'titulo' => 'Workshop de Producción Musical para Radio',
                'descripcion' => 'Creación y producción de música y efectos sonoros para programas radiales.',
                'fecha_inicio' => Carbon::now()->addDays(28),
                'fecha_fin' => Carbon::now()->addDays(29),
                'lugar' => 'Estudio Musical CLDCI',
                'modalidad' => 'presencial',
                'costo' => 3200.00,
                'cupo_maximo' => 12,
                'instructor' => 'Luis "DJ" Martínez - Productor Musical',
                'activo' => true
            ]
        ];

        foreach ($cursosEspecializados as $curso) {
            Capacitacion::create($curso);
        }

        $this->command->info('Cursos especializados creados exitosamente.');
    }
}

