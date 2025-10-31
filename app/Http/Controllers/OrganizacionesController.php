<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use App\Models\TipoOrganizacion;
use App\Http\Requests\Organizaciones\StoreOrganizacionRequest;
use App\Http\Requests\Organizaciones\UpdateOrganizacionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Services\Organizaciones\OrganizacionQueryService;
use App\Services\Organizaciones\OrganizacionExportService;

class OrganizacionesController extends Controller
{
    protected OrganizacionQueryService $organizacionQueryService;

    public function __construct(OrganizacionQueryService $organizacionQueryService)
    {
        $this->organizacionQueryService = $organizacionQueryService;
    }

    /**
     * Display a listing of organizations.
     */
    public function index(Request $request)
    {
        // Obtener filtros de la solicitud
        $filters = $request->only([
            'buscar',
            'tipo',
            'estado',
            'con_miembros'
        ]);

        // Obtener organizaciones paginadas desde la base de datos
        $perPage = (int) $request->get('per_page', 25);
        $organizaciones = $this->organizacionQueryService->paginate($filters, $perPage);
        
        // Cargar la relación de miembros para evitar consultas N+1
        $organizaciones->loadCount('miembros');

        // Obtener estadísticas reales desde la base de datos
        $estadisticas = $this->organizacionQueryService->getEstadisticas();

        // Obtener tipos de organización desde la base de datos o usar valores por defecto
        $tiposOrganizacion = TipoOrganizacion::all();
        if ($tiposOrganizacion->isEmpty()) {
            $tiposOrganizacion = collect([
                (object)['id' => 1, 'nombre' => 'nacional', 'descripcion' => 'Organización Nacional'],
                (object)['id' => 2, 'nombre' => 'seccional', 'descripcion' => 'Seccional Provincial'],
                (object)['id' => 3, 'nombre' => 'seccional_internacional', 'descripcion' => 'Seccional Internacional'],
                (object)['id' => 4, 'nombre' => 'diaspora', 'descripcion' => 'Diáspora'],
            ]);
        }

        return view('organizaciones.index', compact(
            'organizaciones',
            'estadisticas',
            'tiposOrganizacion'
        ));
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        // Tipos de organización estáticos
        $tiposOrganizacion = collect([
            (object)['id' => 1, 'nombre' => 'nacional', 'descripcion' => 'Organización Nacional'],
            (object)['id' => 2, 'nombre' => 'seccional', 'descripcion' => 'Seccional Provincial'],
            (object)['id' => 3, 'nombre' => 'seccional_internacional', 'descripcion' => 'Seccional Internacional'],
            (object)['id' => 4, 'nombre' => 'diaspora', 'descripcion' => 'Diáspora'],
        ]);

        return view('organizaciones.create', compact('tiposOrganizacion'));
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(StoreOrganizacionRequest $request)
    {
        $data = $request->validated();

        $organizacion = new Organizacion();
        $organizacion->fill([
            'nombre' => $data['nombre'],
            'codigo' => $data['codigo'],
            'tipo' => $data['tipo'],
            'estado' => $data['estado'] ?? 'activa',
            'descripcion' => $data['descripcion'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        // Manejar logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $nombreArchivo = 'organizacion_' . $organizacion->codigo . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $ruta = $logo->storeAs('organizaciones/logos', $nombreArchivo, 'public');
            $organizacion->logo_url = $ruta;
        }

        $organizacion->save();

        return redirect()->route('organizaciones.index')
            ->with('success', 'Organización creada exitosamente.');
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit($id)
    {
        $organizacion = Organizacion::findOrFail($id);
        
        // Obtener tipos de organización o crear datos por defecto si no existen
        $tiposOrganizacion = TipoOrganizacion::all();
        
        // Si no hay tipos de organización, crear algunos por defecto
        if ($tiposOrganizacion->isEmpty()) {
            $tiposOrganizacion = collect([
                (object)['id' => 1, 'nombre' => 'nacional', 'descripcion' => 'Organización Nacional'],
                (object)['id' => 2, 'nombre' => 'seccional', 'descripcion' => 'Seccional Provincial'],
                (object)['id' => 3, 'nombre' => 'seccional_internacional', 'descripcion' => 'Seccional Internacional'],
                (object)['id' => 4, 'nombre' => 'diaspora', 'descripcion' => 'Diáspora'],
            ]);
        }

        return view('organizaciones.edit', compact('organizacion', 'tiposOrganizacion'));
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(UpdateOrganizacionRequest $request, $id)
    {
        $data = $request->validated();
        $organizacion = Organizacion::findOrFail($id);

        // Preparar los datos a actualizar
        $updateData = [
            'nombre' => $data['nombre'],
            'codigo' => $data['codigo'],
            'tipo' => $data['tipo'],
            'estado' => $data['estado'],
            'descripcion' => $data['descripcion'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
        ];

        // Manejar logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($organizacion->logo_url && Storage::disk('public')->exists($organizacion->logo_url)) {
                Storage::disk('public')->delete($organizacion->logo_url);
            }

            $logo = $request->file('logo');
            $nombreArchivo = 'organizacion_' . $organizacion->codigo . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $ruta = $logo->storeAs('organizaciones/logos', $nombreArchivo, 'public');
            $updateData['logo_url'] = $ruta;
        }

        // Actualizar usando update() directamente para asegurar que todos los campos se actualicen
        $organizacion->update($updateData);

        return redirect()->route('organizaciones.index')
            ->with('success', 'Organización actualizada exitosamente.');
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy($id)
    {
        $organizacion = Organizacion::findOrFail($id);
        
        // Verificar si tiene miembros asociados
        if ($organizacion->miembros()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la organización porque tiene miembros asociados.'
                ], 400);
            }
            return redirect()->route('organizaciones.index')
                ->with('error', 'No se puede eliminar la organización porque tiene miembros asociados.');
        }
        
        // Eliminar logo si existe
        if ($organizacion->logo_url && Storage::disk('public')->exists($organizacion->logo_url)) {
            Storage::disk('public')->delete($organizacion->logo_url);
        }

        $organizacion->delete();

        // Responder JSON para solicitudes AJAX
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Organización eliminada exitosamente'
            ]);
        }

        return redirect()->route('organizaciones.index')
            ->with('success', 'Organización eliminada exitosamente.');
    }

    /**
     * Display organization profile page.
     */
    public function profile($id)
    {
        $organizacion = Organizacion::with(['miembros', 'asambleas', 'elecciones', 'cursos'])
            ->findOrFail($id);

        // Obtener estadísticas de la organización
        $estadisticas = [
            'total_miembros' => $organizacion->miembros()->count(),
            'miembros_activos' => $organizacion->miembros()->activos()->count(),
            'miembros_vencidos' => $organizacion->miembros()->vencidos()->count(),
            'total_asambleas' => $organizacion->asambleas()->count(),
            'asambleas_activas' => $organizacion->asambleas()->where('estado', 'convocada')->count(),
            'total_elecciones' => $organizacion->elecciones()->count(),
            'elecciones_activas' => $organizacion->elecciones()->where('estado', 'en_proceso')->count(),
            'total_cursos' => $organizacion->cursos()->count(),
            'cursos_activos' => $organizacion->cursos()->where('estado', 'activo')->count(),
        ];

        // Obtener miembros más activos
        $miembrosActivos = $organizacion->miembros()
            ->activos()
            ->with(['organizacion'])
            ->limit(10)
            ->get()
            ->map(function ($miembro) {
                return [
                    'id' => $miembro->id,
                    'nombre' => $miembro->nombre_completo,
                    'numero_carnet' => $miembro->numero_carnet,
                    'fecha_ingreso' => $miembro->fecha_ingreso,
                    'años_membresia' => $miembro->años_membresia,
                    'foto_url' => $miembro->foto_url ?? 'assets/images/avatar/avatar-1.jpg'
                ];
            });

        // Obtener actividad reciente
        $actividadReciente = collect([
            [
                'tipo' => 'miembro',
                'titulo' => 'Nuevo Miembro Registrado',
                'descripcion' => 'Se registró un nuevo miembro en la organización',
                'fecha' => now()->subDays(2),
                'icono' => 'ri-user-add-line',
                'color' => 'success'
            ],
            [
                'tipo' => 'asamblea',
                'titulo' => 'Asamblea Programada',
                'descripcion' => 'Se programó una nueva asamblea',
                'fecha' => now()->subDays(5),
                'icono' => 'ri-calendar-line',
                'color' => 'info'
            ],
            [
                'tipo' => 'eleccion',
                'titulo' => 'Elección Iniciada',
                'descripcion' => 'Se inició un proceso electoral',
                'fecha' => now()->subDays(7),
                'icono' => 'ri-government-line',
                'color' => 'warning'
            ]
        ]);

        return view('organizaciones.profile', compact(
            'organizacion', 
            'estadisticas', 
            'actividadReciente',
            'miembrosActivos'
        ));
    }

    /**
     * Get organizations data for API
     */
    public function api(Request $request)
    {
        $filters = $request->only([
            'buscar',
            'tipo',
            'estado',
        ]);

        $perPage = (int) $request->get('per_page', 20);

        $organizaciones = $this->organizacionQueryService->paginateForApi($filters, $perPage);

        return response()->json($organizaciones);
    }

    /**
     * Get organization statistics
     */
    public function estadisticas()
    {
        return response()->json($this->organizacionQueryService->getEstadisticas());
    }

    /**
     * Search organizations
     */
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');

        return response()->json(
            $this->organizacionQueryService->search($termino)
        );
    }

    /**
     * Export organizations to CSV
     */
    public function exportar(Request $request)
    {
        $filters = $request->only([
            'tipo',
            'estado',
        ]);

        return $this->organizacionExportService->streamCsv($filters);
    }

    /**
     * Delete multiple organizations
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:organizaciones,id'
        ]);

        $selectedIds = $request->input('selected_ids');
        
        // Verificar que no tengan miembros asociados
        $organizacionesConMiembros = Organizacion::whereIn('id', $selectedIds)
            ->whereHas('miembros')
            ->count();
            
        if ($organizacionesConMiembros > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden eliminar organizaciones que tienen miembros asociados.'
                ], 400);
            }
            return redirect()->route('organizaciones.index')
                ->with('error', 'No se pueden eliminar organizaciones que tienen miembros asociados.');
        }
        
        $deletedCount = Organizacion::whereIn('id', $selectedIds)->delete();

        // Responder JSON para solicitudes AJAX
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deletedCount} organizaciones correctamente",
                'deleted_count' => $deletedCount
            ]);
        }

        return redirect()->route('organizaciones.index')
            ->with('success', "Se eliminaron {$deletedCount} organizaciones correctamente.");
    }

    /**
     * Activate organization
     */
    public function activate($id)
    {
        $organizacion = Organizacion::findOrFail($id);
        $organizacion->update(['estado' => 'activa']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Organización activada exitosamente'
            ]);
        }

        return redirect()->route('organizaciones.index')
            ->with('success', 'Organización activada exitosamente.');
    }

    /**
     * Deactivate organization
     */
    public function deactivate($id)
    {
        $organizacion = Organizacion::findOrFail($id);
        $organizacion->update(['estado' => 'inactiva']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Organización desactivada exitosamente'
            ]);
        }

        return redirect()->route('organizaciones.index')
            ->with('success', 'Organización desactivada exitosamente.');
    }
}
