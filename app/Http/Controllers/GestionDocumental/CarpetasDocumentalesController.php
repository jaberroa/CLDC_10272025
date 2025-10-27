<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\CarpetaDocumental;
use App\Models\SeccionDocumental;
use App\Models\AuditoriaDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarpetasDocumentalesController extends Controller
{
    public function __construct()
    {
        // Middleware aplicado en rutas
    }

    public function index(Request $request)
    {
        $seccionId = $request->seccion_id;
        
        // Obtener carpetas raíz con toda su jerarquía
        $carpetas = CarpetaDocumental::with([
                'seccion', 
                'creadoPor',
                'subcarpetas' => function($query) {
                    $query->activas()->ordenadas()->with(['subcarpetas' => function($q) {
                        $q->activas()->ordenadas()->with(['subcarpetas' => function($q2) {
                            $q2->activas()->ordenadas()->with('subcarpetas');
                        }]);
                    }]);
                }
            ])
            ->whereNull('carpeta_padre_id')
            ->when($seccionId, function($query) use ($seccionId) {
                $query->where('seccion_id', $seccionId);
            })
            ->activas()
            ->ordenadas()
            ->get();
        
        $secciones = SeccionDocumental::activas()->ordenadas()->get();
        
        // Estadísticas
        $estadisticas = [
            'total_carpetas' => CarpetaDocumental::count(),
            'total_documentos' => \App\Models\DocumentoGestion::count(),
            'espacio_usado' => \App\Models\DocumentoGestion::sum('tamano_bytes'),
        ];

        return view('gestion-documental.carpetas.index', compact('carpetas', 'secciones', 'seccionId', 'estadisticas'));
    }

    public function create(Request $request)
    {
        $secciones = SeccionDocumental::activas()->ordenadas()->get();
        $carpetaPadreId = $request->carpeta_padre_id;
        $seccionId = $request->seccion_id;

        return view('gestion-documental.carpetas.create', compact('secciones', 'carpetaPadreId', 'seccionId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seccion_id' => 'required|exists:secciones_documentales,id',
            'carpeta_padre_id' => 'nullable|exists:carpetas_documentales,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'publica' => 'boolean',
            'solo_lectura' => 'boolean',
            'entidad_tipo' => 'nullable|string',
            'entidad_id' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['nombre']);
        
        // Calcular nivel y ruta completa
        if ($validated['carpeta_padre_id']) {
            $carpetaPadre = CarpetaDocumental::find($validated['carpeta_padre_id']);
            $validated['nivel'] = $carpetaPadre->nivel + 1;
            $validated['ruta_completa'] = $carpetaPadre->ruta_completa . '/' . $validated['slug'];
        } else {
            $validated['nivel'] = 1;
            $validated['ruta_completa'] = $validated['slug'];
        }
        
        $validated['activa'] = true;
        $validated['creado_por'] = auth()->id();

        $carpeta = CarpetaDocumental::create($validated);

        AuditoriaDocumento::registrar('crear', $carpeta, 'Carpeta documental creada');

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Carpeta creada exitosamente',
                'carpeta' => $carpeta
            ]);
        }

        return redirect()
            ->route('gestion-documental.carpetas.show', $carpeta)
            ->with('success', 'Carpeta creada exitosamente');
    }

    public function show(CarpetaDocumental $carpeta)
    {
        $carpeta->load([
            'subcarpetas' => function($query) {
                $query->activas()->ordenadas();
            },
            'documentos' => function($query) {
                $query->activos()->versionActual()->with('subidoPor');
            },
            'seccion'
        ]);

        $estadisticas = [
            'total_subcarpetas' => $carpeta->subcarpetas()->count(),
            'total_documentos' => $carpeta->documentos()->count(),
            'tamano_total' => $carpeta->tamano_total_bytes,
        ];

        return view('gestion-documental.carpetas.show', compact('carpeta', 'estadisticas'));
    }

    public function edit(CarpetaDocumental $carpeta)
    {
        $secciones = SeccionDocumental::activas()->ordenadas()->get();
        $carpetasPosibles = CarpetaDocumental::where('seccion_id', $carpeta->seccion_id)
            ->where('id', '!=', $carpeta->id)
            ->ordenadas()
            ->get();

        return view('gestion-documental.carpetas.edit', compact('carpeta', 'secciones', 'carpetasPosibles'));
    }

    public function update(Request $request, CarpetaDocumental $carpeta)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'activa' => 'boolean',
            'publica' => 'boolean',
            'solo_lectura' => 'boolean',
        ]);

        $datosAnteriores = $carpeta->toArray();
        $carpeta->update($validated);
        $carpeta->actualizarRutaCompleta();

        AuditoriaDocumento::registrar(
            'editar',
            $carpeta,
            'Carpeta actualizada',
            ['datos_anteriores' => $datosAnteriores]
        );

        return redirect()
            ->route('gestion-documental.carpetas.show', $carpeta)
            ->with('success', 'Carpeta actualizada exitosamente');
    }

    public function destroy(CarpetaDocumental $carpeta)
    {
        // Verificar si tiene documentos o subcarpetas
        if ($carpeta->documentos()->count() > 0 || $carpeta->subcarpetas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una carpeta que contiene documentos o subcarpetas');
        }

        $nombreCarpeta = $carpeta->nombre;
        
        AuditoriaDocumento::registrar('eliminar', $carpeta, 'Carpeta eliminada');

        $carpeta->delete();

        return redirect()
            ->route('gestion-documental.carpetas.index')
            ->with('success', "Carpeta '{$nombreCarpeta}' eliminada exitosamente");
    }

    /**
     * Move folder to another location
     */
    public function mover(Request $request, CarpetaDocumental $carpeta)
    {
        $request->validate([
            'nueva_carpeta_padre_id' => 'nullable|exists:carpetas_documentales,id'
        ]);

        $ubicacionAnterior = $carpeta->ruta_completa;
        $carpeta->moverA($request->nueva_carpeta_padre_id);

        AuditoriaDocumento::registrar(
            'mover',
            $carpeta,
            'Carpeta movida',
            [
                'ubicacion_anterior' => $ubicacionAnterior,
                'ubicacion_nueva' => $carpeta->ruta_completa
            ]
        );

        return back()->with('success', 'Carpeta movida exitosamente');
    }

    /**
     * Get folder tree (AJAX)
     */
    public function arbol(Request $request)
    {
        $seccionId = $request->seccion_id;
        
        $carpetas = CarpetaDocumental::where('seccion_id', $seccionId)
            ->raiz()
            ->activas()
            ->with('subcarpetas')
            ->ordenadas()
            ->get();

        return response()->json($carpetas);
    }
}
