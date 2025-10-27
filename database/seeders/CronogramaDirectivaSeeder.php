<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CronogramaDirectiva;
use App\Models\Organo;
use App\Models\Miembro;
use App\Models\User;
use Carbon\Carbon;

class CronogramaDirectivaSeeder extends Seeder
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

        $eventos = [
            [
                'titulo' => 'Asamblea General Ordinaria',
                'descripcion' => 'Reunión mensual para revisar el estado financiero y tomar decisiones importantes sobre el futuro de la organización.',
                'fecha_inicio' => Carbon::now()->addDays(5),
                'fecha_fin' => Carbon::now()->addDays(5),
                'hora_inicio' => '09:00',
                'hora_fin' => '12:00',
                'lugar' => 'Auditorio Principal - Sede Central',
                'tipo_evento' => 'asamblea',
                'estado' => 'programado',
                'organo_id' => $organos->first()->id,
                'responsable_id' => $miembros->first()->id,
                'observaciones' => 'Se requiere confirmación de asistencia antes del evento.',
                'participantes' => json_encode(['miembros_activos', 'directiva', 'invitados_especiales']),
                'agenda' => json_encode([
                    'Revisión de actas anteriores',
                    'Estado financiero',
                    'Proyectos en curso',
                    'Nuevas propuestas',
                    'Cierre y próximos pasos'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 150
            ],
            [
                'titulo' => 'Capacitación en Liderazgo',
                'descripcion' => 'Taller especializado para desarrollar habilidades de liderazgo en los miembros de la directiva.',
                'fecha_inicio' => Carbon::now()->addDays(10),
                'fecha_fin' => Carbon::now()->addDays(10),
                'hora_inicio' => '14:00',
                'hora_fin' => '18:00',
                'lugar' => 'Sala de Conferencias - Piso 3',
                'tipo_evento' => 'capacitacion',
                'estado' => 'programado',
                'organo_id' => $organos->skip(1)->first()->id ?? $organos->first()->id,
                'responsable_id' => $miembros->skip(1)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Incluye materiales de trabajo y certificado de participación.',
                'participantes' => json_encode(['directiva', 'coordinadores']),
                'agenda' => json_encode([
                    'Introducción al liderazgo',
                    'Técnicas de comunicación',
                    'Resolución de conflictos',
                    'Trabajo en equipo',
                    'Evaluación final'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 25
            ],
            [
                'titulo' => 'Reunión de Comisión Financiera',
                'descripcion' => 'Revisión detallada del presupuesto anual y análisis de gastos del trimestre.',
                'fecha_inicio' => Carbon::now()->addDays(3),
                'fecha_fin' => Carbon::now()->addDays(3),
                'hora_inicio' => '10:00',
                'hora_fin' => '16:00',
                'lugar' => 'Oficina de Finanzas - Piso 2',
                'tipo_evento' => 'reunion',
                'estado' => 'en_curso',
                'organo_id' => $organos->skip(2)->first()->id ?? $organos->first()->id,
                'responsable_id' => $miembros->skip(2)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Reunión privada de la comisión.',
                'participantes' => json_encode(['comision_financiera']),
                'agenda' => json_encode([
                    'Revisión de estados financieros',
                    'Análisis de presupuesto',
                    'Propuestas de inversión',
                    'Control de gastos',
                    'Planificación financiera'
                ]),
                'requiere_confirmacion' => false,
                'cupo_maximo' => 8
            ],
            [
                'titulo' => 'Elecciones de Nueva Directiva',
                'descripcion' => 'Proceso electoral para elegir a los nuevos miembros de la directiva para el próximo período.',
                'fecha_inicio' => Carbon::now()->addDays(15),
                'fecha_fin' => Carbon::now()->addDays(15),
                'hora_inicio' => '08:00',
                'hora_fin' => '17:00',
                'lugar' => 'Gimnasio Principal - Sede Central',
                'tipo_evento' => 'eleccion',
                'estado' => 'programado',
                'organo_id' => $organos->first()->id,
                'responsable_id' => $miembros->skip(3)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Proceso democrático con supervisión externa.',
                'participantes' => json_encode(['miembros_activos', 'observadores']),
                'agenda' => json_encode([
                    'Registro de votantes',
                    'Presentación de candidatos',
                    'Votación',
                    'Conteo de votos',
                    'Proclamación de resultados'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 300
            ],
            [
                'titulo' => 'Taller de Comunicación Digital',
                'descripcion' => 'Capacitación sobre herramientas digitales para mejorar la comunicación interna y externa.',
                'fecha_inicio' => Carbon::now()->addDays(7),
                'fecha_fin' => Carbon::now()->addDays(7),
                'hora_inicio' => '09:00',
                'hora_fin' => '13:00',
                'lugar' => 'Laboratorio de Computación - Piso 1',
                'tipo_evento' => 'capacitacion',
                'estado' => 'completado',
                'organo_id' => $organos->skip(1)->first()->id ?? $organos->first()->id,
                'responsable_id' => $miembros->skip(4)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Se requiere conocimiento básico de computación.',
                'participantes' => json_encode(['personal_administrativo', 'coordinadores']),
                'agenda' => json_encode([
                    'Introducción a herramientas digitales',
                    'Gestión de redes sociales',
                    'Comunicación por email',
                    'Plataformas de videoconferencia',
                    'Evaluación práctica'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 20
            ],
            [
                'titulo' => 'Reunión de Evaluación Trimestral',
                'descripcion' => 'Evaluación del desempeño y cumplimiento de objetivos del trimestre actual.',
                'fecha_inicio' => Carbon::now()->addDays(12),
                'fecha_fin' => Carbon::now()->addDays(12),
                'hora_inicio' => '15:00',
                'hora_fin' => '18:00',
                'lugar' => 'Sala de Juntas - Piso 4',
                'tipo_evento' => 'reunion',
                'estado' => 'programado',
                'organo_id' => $organos->skip(2)->first()->id ?? $organos->first()->id,
                'responsable_id' => $miembros->skip(5)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Reunión estratégica de alto nivel.',
                'participantes' => json_encode(['directiva', 'gerentes']),
                'agenda' => json_encode([
                    'Revisión de objetivos trimestrales',
                    'Análisis de resultados',
                    'Identificación de problemas',
                    'Planificación del próximo trimestre',
                    'Asignación de responsabilidades'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 12
            ],
            [
                'titulo' => 'Ceremonia de Reconocimiento',
                'descripcion' => 'Evento especial para reconocer el trabajo destacado de miembros y colaboradores.',
                'fecha_inicio' => Carbon::now()->addDays(20),
                'fecha_fin' => Carbon::now()->addDays(20),
                'hora_inicio' => '19:00',
                'hora_fin' => '22:00',
                'lugar' => 'Salón de Eventos - Hotel Central',
                'tipo_evento' => 'ceremonia',
                'estado' => 'programado',
                'organo_id' => $organos->first()->id,
                'responsable_id' => $miembros->skip(6)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Evento formal con cena incluida.',
                'participantes' => json_encode(['miembros_destacados', 'familiares', 'invitados_especiales']),
                'agenda' => json_encode([
                    'Cocktail de bienvenida',
                    'Discurso de apertura',
                    'Entrega de reconocimientos',
                    'Cena de gala',
                    'Brindis de cierre'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 200
            ],
            [
                'titulo' => 'Sesión de Planificación Estratégica',
                'descripcion' => 'Reunión intensiva para definir la estrategia organizacional del próximo año.',
                'fecha_inicio' => Carbon::now()->addDays(25),
                'fecha_fin' => Carbon::now()->addDays(26),
                'hora_inicio' => '08:00',
                'hora_fin' => '18:00',
                'lugar' => 'Centro de Convenciones - Zona Norte',
                'tipo_evento' => 'planificacion',
                'estado' => 'programado',
                'organo_id' => $organos->skip(1)->first()->id ?? $organos->first()->id,
                'responsable_id' => $miembros->skip(7)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Evento de dos días con hospedaje incluido.',
                'participantes' => json_encode(['directiva', 'consultores_externos']),
                'agenda' => json_encode([
                    'Análisis del entorno',
                    'Definición de visión y misión',
                    'Establecimiento de objetivos',
                    'Desarrollo de estrategias',
                    'Plan de implementación'
                ]),
                'requiere_confirmacion' => true,
                'cupo_maximo' => 30
            ],
            [
                'titulo' => 'Auditoría Interna',
                'descripcion' => 'Proceso de auditoría para verificar el cumplimiento de procedimientos y normativas.',
                'fecha_inicio' => Carbon::now()->addDays(8),
                'fecha_fin' => Carbon::now()->addDays(10),
                'hora_inicio' => '09:00',
                'hora_fin' => '17:00',
                'lugar' => 'Oficinas Administrativas - Todos los pisos',
                'tipo_evento' => 'auditoria',
                'estado' => 'en_curso',
                'organo_id' => $organos->skip(3)->first()->id ?? $organos->first()->id,
                'responsable_id' => $miembros->skip(8)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Proceso confidencial con acceso restringido.',
                'participantes' => json_encode(['auditores_internos', 'personal_clave']),
                'agenda' => json_encode([
                    'Revisión de documentos',
                    'Entrevistas con personal',
                    'Verificación de procesos',
                    'Análisis de cumplimiento',
                    'Elaboración de informe'
                ]),
                'requiere_confirmacion' => false,
                'cupo_maximo' => 15
            ],
            [
                'titulo' => 'Feria de Proyectos Comunitarios',
                'descripcion' => 'Exposición de proyectos sociales y comunitarios desarrollados por la organización.',
                'fecha_inicio' => Carbon::now()->addDays(30),
                'fecha_fin' => Carbon::now()->addDays(30),
                'hora_inicio' => '10:00',
                'hora_fin' => '16:00',
                'lugar' => 'Parque Central - Plaza Principal',
                'tipo_evento' => 'feria',
                'estado' => 'programado',
                'organo_id' => $organos->first()->id,
                'responsable_id' => $miembros->skip(9)->first()->id ?? $miembros->first()->id,
                'observaciones' => 'Evento público con participación comunitaria.',
                'participantes' => json_encode(['comunidad', 'beneficiarios', 'voluntarios']),
                'agenda' => json_encode([
                    'Inauguración oficial',
                    'Presentación de proyectos',
                    'Actividades interactivas',
                    'Degustación de productos',
                    'Cierre y agradecimientos'
                ]),
                'requiere_confirmacion' => false,
                'cupo_maximo' => 500
            ]
        ];

        foreach ($eventos as $evento) {
            CronogramaDirectiva::create(array_merge($evento, [
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ]));
        }

        $this->command->info('Se crearon ' . count($eventos) . ' eventos de cronograma de directiva.');
    }
}
