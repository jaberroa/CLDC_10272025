<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use App\Models\Organizacion;
use App\Http\Requests\Miembros\StoreMiembroRequest;
use App\Http\Requests\Miembros\UpdateMiembroRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Services\Miembros\MiembroQueryService;
use App\Services\Miembros\MiembroExportService;

class MiembrosController extends Controller
{
    public function __construct(
        protected MiembroQueryService $miembroQueryService,
        protected MiembroExportService $miembroExportService
    ) {
    }

    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'buscar',
            'estado_membresia_id',
            'organizacion_id',
        ]);

        $miembros = $this->miembroQueryService->paginate($filters);
        $estadisticas = $this->miembroQueryService->getEstadisticas();

        $organizaciones = Organizacion::activas()->get();

        return view('miembros.index', compact(
            'miembros',
            'estadisticas',
            'organizaciones'
        ));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        $organizaciones = Organizacion::activas()->get();
        $estadosMembresia = \App\Models\EstadoMembresia::all();

        return view('miembros.create', compact('organizaciones', 'estadosMembresia'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(StoreMiembroRequest $request)
    {
        $data = $request->validated();

        $miembro = new Miembro();
        $miembro->fill([
            'organizacion_id' => $data['organizacion_id'],
            'nombre_completo' => $data['nombre_completo'],
            'email' => $data['email'],
            'cedula' => $data['cedula'],
            'telefono' => $data['telefono'] ?? null,
            'profesion' => $data['profesion'] ?? null,
            'tipo_membresia' => $data['tipo_membresia'],
            'estado_membresia' => $data['estado_membresia'],
            'fecha_ingreso' => $data['fecha_ingreso'],
        ]);
        $miembro->created_by = auth()->user()->name ?? 'admin';

        // Generar número de carnet
        $miembro->numero_carnet = Miembro::generarNumeroCarnet(
            $data['organizacion_id'],
            $data['fecha_ingreso'] ?? null
        );

        // Manejar foto
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreArchivo = 'miembro_' . $miembro->cedula . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $ruta = $foto->storeAs('miembros/fotos', $nombreArchivo, 'public');
            $miembro->foto_url = $ruta;
        }

        $miembro->save();

        return redirect()->route('miembros.index')
            ->with('success', 'Miembro creado exitosamente.');
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit($id)
    {
        $miembro = Miembro::findOrFail($id);
        $organizaciones = Organizacion::activas()->get();
        $estadosMembresia = \App\Models\EstadoMembresia::all();

        return view('miembros.edit', compact('miembro', 'organizaciones', 'estadosMembresia'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(UpdateMiembroRequest $request, $id)
    {
        $data = $request->validated();
        $miembro = Miembro::findOrFail($id);

        $miembro->fill([
            'organizacion_id' => $data['organizacion_id'],
            'nombre_completo' => $data['nombre_completo'],
            'email' => $data['email'],
            'cedula' => $data['cedula'],
            'telefono' => $data['telefono'] ?? null,
            'profesion' => $data['profesion'] ?? null,
            'tipo_membresia' => $data['tipo_membresia'],
            'estado_membresia' => $data['estado_membresia'],
            'fecha_ingreso' => $data['fecha_ingreso'],
        ]);

        // Manejar foto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($miembro->foto_url && Storage::disk('public')->exists($miembro->foto_url)) {
                Storage::disk('public')->delete($miembro->foto_url);
            }

            $foto = $request->file('foto');
            $nombreArchivo = 'miembro_' . $miembro->cedula . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $ruta = $foto->storeAs('miembros/fotos', $nombreArchivo, 'public');
            $miembro->foto_url = $ruta;
        }

        $miembro->save();

        return redirect()->route('miembros.index')
            ->with('success', 'Miembro actualizado exitosamente.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy($id)
    {
        $miembro = Miembro::findOrFail($id);
        
        // Eliminar foto si existe
        if ($miembro->foto_url && Storage::disk('public')->exists($miembro->foto_url)) {
            Storage::disk('public')->delete($miembro->foto_url);
        }

        $miembro->delete();

        return redirect()->route('miembros.index')
            ->with('success', 'Miembro eliminado exitosamente.');
    }

    /**
     * Generar estadísticas del miembro
     */
    private function generarEstadisticas($miembro)
    {
        return [
            'asambleas_asistidas' => 0, // Temporalmente deshabilitado
            'capacitaciones_inscrito' => 0, // Temporalmente deshabilitado
            'elecciones_participado' => 0, // Temporalmente deshabilitado
            'cursos_completados' => 0, // Temporalmente deshabilitado
            'votos_emitidos' => 0, // Temporalmente deshabilitado
            'cargos_actuales' => 0, // Temporalmente deshabilitado
            'años_membresia' => 5,
        ];
    }


    /**
     * Display member profile page.
     */
    public function profile($id)
    {
        $miembro = Miembro::with(['organizacion'])
            ->findOrFail($id);

        // Obtener estadísticas del miembro
        $estadisticas = $this->generarEstadisticas($miembro);

        // Obtener cargos actuales (datos simulados por ahora)
        $cargosActuales = [
            [
                'cargo' => 'Presidente',
                'organo' => 'Junta Directiva Nacional',
                'fecha_inicio' => '2024-01-15'
            ],
            [
                'cargo' => 'Coordinador',
                'organo' => 'Comité de Capacitación',
                'fecha_inicio' => '2024-03-01'
            ]
        ];

        // Obtener historial de asambleas (datos simulados)
        $asambleasHistorial = [
            [
                'titulo' => 'Asamblea General Ordinaria 2024',
                'descripcion' => 'Revisión de actividades y presupuesto anual',
                'fecha' => '2024-03-15',
                'tipo' => 'ordinaria',
                'presente' => true,
                'modalidad' => 'presencial'
            ],
            [
                'titulo' => 'Asamblea Extraordinaria',
                'descripcion' => 'Elección de nueva directiva',
                'fecha' => '2024-06-20',
                'tipo' => 'extraordinaria',
                'presente' => true,
                'modalidad' => 'virtual'
            ],
            [
                'titulo' => 'Asamblea de Evaluación',
                'descripcion' => 'Evaluación de gestión 2024',
                'fecha' => '2024-09-10',
                'tipo' => 'ordinaria',
                'presente' => false,
                'modalidad' => 'hibrida'
            ]
        ];

        // Obtener cursos inscritos (datos reales de la base de datos)
        $cursosInscritos = [];
        try {
            $inscripciones = \App\Models\InscripcionCurso::with('curso')
                ->where('miembro_id', $miembro->id)
                ->get();
            
            foreach ($inscripciones as $inscripcion) {
                $cursosInscritos[] = [
                    'titulo' => $inscripcion->curso->titulo ?? 'Curso de Capacitación',
                    'instructor' => $inscripcion->curso->instructor ?? 'Instructor no especificado',
                    'fecha_inicio' => $inscripcion->curso->fecha_inicio ?? 'N/A',
                    'modalidad' => $inscripcion->curso->modalidad ?? 'presencial',
                    'estado' => $inscripcion->estado ?? 'inscrito',
                    'calificacion' => $inscripcion->calificacion
                ];
            }
        } catch (\Exception $e) {
            // Si hay error, usar datos simulados
            $cursosInscritos = [
                [
                    'titulo' => 'Liderazgo Comunitario Avanzado',
                    'instructor' => 'Dr. Juan Pérez',
                    'fecha_inicio' => '2024-11-15',
                    'modalidad' => 'presencial',
                    'estado' => 'completado',
                    'calificacion' => 95
                ],
                [
                    'titulo' => 'Gestión de Proyectos Sociales',
                    'instructor' => 'Lic. María González',
                    'fecha_inicio' => '2024-12-01',
                    'modalidad' => 'virtual',
                    'estado' => 'inscrito',
                    'calificacion' => null
                ]
            ];
        }

        // Obtener actividad reciente
        $actividadReciente = collect([
            [
                'tipo' => 'asamblea',
                'titulo' => 'Asistencia a Asamblea',
                'descripcion' => 'Participó en asamblea general',
                'fecha' => now()->subDays(5),
                'icono' => 'ri-calendar-line',
                'color' => 'primary'
            ],
            [
                'tipo' => 'capacitacion',
                'titulo' => 'Inscripción en Capacitación',
                'descripcion' => 'Se inscribió en curso de locución',
                'fecha' => now()->subDays(10),
                'icono' => 'ri-graduation-cap-line',
                'color' => 'success'
            ],
            [
                'tipo' => 'eleccion',
                'titulo' => 'Participación Electoral',
                'descripcion' => 'Ejercitó su derecho al voto',
                'fecha' => now()->subDays(15),
                'icono' => 'ri-government-line',
                'color' => 'info'
            ]
        ]);

        return view('miembros.profile', compact(
            'miembro', 
            'estadisticas', 
            'actividadReciente',
            'cargosActuales',
            'asambleasHistorial',
            'cursosInscritos'
        ));
    }

    /**
     * Generate member card (QR Code)
     */
    public function carnet($id)
    {
        $miembro = Miembro::with('organizacion')->findOrFail($id);
        
        // Generar datos para el carnet
        $carnetData = [
            'nombre' => $miembro->nombre_completo,
            'numero_carnet' => $miembro->numero_carnet,
            'organizacion' => $miembro->organizacion->nombre,
            'tipo_membresia' => $miembro->tipo_membresia,
            'fecha_ingreso' => $miembro->fecha_ingreso->format('Y'),
            'foto' => $miembro->foto_url,
        ];

        return view('miembros.carnet', compact('miembro', 'carnetData'));
    }

    /**
     * Get carnet data for AJAX modal
     */
    public function carnetData($id)
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])->findOrFail($id);
        
        return response()->json([
            'id' => $miembro->id,
            'nombre_completo' => $miembro->nombre_completo,
            'numero_carnet' => $miembro->numero_carnet,
            'profesion' => $miembro->profesion,
            'organizacion' => $miembro->organizacion->nombre ?? 'CLDCI Nacional',
            'tipo_membresia' => $miembro->estadoMembresia->nombre ?? 'Activa',
            'fecha_ingreso' => $miembro->fecha_ingreso->format('Y'),
            'valido_hasta' => $miembro->fecha_ingreso->addYears(2)->format('Y'),
            'foto_url' => $miembro->foto_url ? asset($miembro->foto_url) : null,
        ]);
    }

    /**
     * Get members data for API
     */
    public function api(Request $request)
    {
        $filters = $request->only([
            'buscar',
            'tipo_membresia',
            'estado_membresia',
            'organizacion_id',
        ]);

        $perPage = (int) $request->get('per_page', 20);

        $miembros = $this->miembroQueryService->paginateForApi($filters, $perPage);

        return response()->json($miembros);
    }

    /**
     * Get member statistics
     */
    public function estadisticas()
    {
        return response()->json($this->miembroQueryService->getEstadisticas());
    }

    /**
     * Search members
     */
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');

        return response()->json(
            $this->miembroQueryService->search($termino)
        );
    }

    /**
     * Export members to CSV
     */
    public function exportar(Request $request)
    {
        $filters = $request->only([
            'tipo_membresia',
            'estado_membresia',
            'organizacion_id',
        ]);

        return $this->miembroExportService->streamCsv($filters);
    }

    /**
     * Delete multiple members
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:miembros,id'
        ]);

        $selectedIds = $request->input('selected_ids');
        $deletedCount = Miembro::whereIn('id', $selectedIds)->delete();

        return redirect()->route('miembros.index')
            ->with('success', "Se eliminaron {$deletedCount} miembros correctamente.");
    }
}
