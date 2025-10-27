<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\SeccionDocumental;
use App\Models\AuditoriaDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeccionesDocumentalesController extends Controller
{
    public function __construct()
    {
        // Middleware aplicado en rutas
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $secciones = SeccionDocumental::with(['creadoPor', 'actualizadoPor'])
            ->ordenadas()
            ->paginate(20);

        return view('gestion-documental.secciones.index', compact('secciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gestion-documental.secciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:secciones_documentales,slug',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'orden' => 'nullable|integer|min:0',
            'visible_menu' => 'boolean',
            'requiere_aprobacion' => 'boolean',
            'permite_versionado' => 'boolean',
            'permite_compartir_externo' => 'boolean',
            'max_tamano_archivo_mb' => 'nullable|integer|min:1|max:500',
            'formatos_permitidos' => 'nullable|array',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nombre']);
        $validated['creado_por'] = auth()->id();
        $validated['activa'] = true;

        $seccion = SeccionDocumental::create($validated);

        // Registrar auditoría
        AuditoriaDocumento::registrar('crear', $seccion, 'Sección documental creada');

        return redirect()
            ->route('gestion-documental.secciones.index')
            ->with('success', 'Sección creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(SeccionDocumental $seccion)
    {
        $seccion->load([
            'carpetas' => function($query) {
                $query->raiz()->activas()->ordenadas();
            },
            'camposMetadatos' => function($query) {
                $query->activos()->ordenados();
            }
        ]);

        $estadisticas = [
            'total_carpetas' => $seccion->carpetas()->count(),
            'total_documentos' => $seccion->documentos()->count(),
            'tamano_total' => $seccion->documentos()->sum('tamano_bytes'),
        ];

        return view('gestion-documental.secciones.show', compact('seccion', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeccionDocumental $seccion)
    {
        return view('gestion-documental.secciones.edit', compact('seccion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeccionDocumental $seccion)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:secciones_documentales,slug,' . $seccion->id,
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'orden' => 'nullable|integer|min:0',
            'activa' => 'boolean',
            'visible_menu' => 'boolean',
            'requiere_aprobacion' => 'boolean',
            'permite_versionado' => 'boolean',
            'permite_compartir_externo' => 'boolean',
            'max_tamano_archivo_mb' => 'nullable|integer|min:1|max:500',
            'formatos_permitidos' => 'nullable|array',
        ]);

        $validated['actualizado_por'] = auth()->id();

        $datosAnteriores = $seccion->toArray();
        $seccion->update($validated);

        // Registrar auditoría
        AuditoriaDocumento::registrar(
            'editar',
            $seccion,
            'Sección documental actualizada',
            ['datos_anteriores' => $datosAnteriores]
        );

        return redirect()
            ->route('gestion-documental.secciones.index')
            ->with('success', 'Sección actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeccionDocumental $seccion)
    {
        // Verificar si tiene documentos
        if ($seccion->documentos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una sección con documentos');
        }

        $nombreSeccion = $seccion->nombre;
        
        // Registrar auditoría antes de eliminar
        AuditoriaDocumento::registrar('eliminar', $seccion, 'Sección documental eliminada');

        $seccion->delete();

        return redirect()
            ->route('gestion-documental.secciones.index')
            ->with('success', "Sección '{$nombreSeccion}' eliminada exitosamente");
    }

    /**
     * Toggle active status
     */
    public function toggleActiva(SeccionDocumental $seccion)
    {
        $seccion->update([
            'activa' => !$seccion->activa,
            'actualizado_por' => auth()->id()
        ]);

        $estado = $seccion->activa ? 'activada' : 'desactivada';
        
        AuditoriaDocumento::registrar('cambiar_estado', $seccion, "Sección {$estado}");

        return back()->with('success', "Sección {$estado} exitosamente");
    }

    /**
     * Reorder sections
     */
    public function reordenar(Request $request)
    {
        $request->validate([
            'orden' => 'required|array',
            'orden.*' => 'required|integer|exists:secciones_documentales,id'
        ]);

        foreach ($request->orden as $index => $seccionId) {
            SeccionDocumental::where('id', $seccionId)->update(['orden' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Orden actualizado']);
    }
}
