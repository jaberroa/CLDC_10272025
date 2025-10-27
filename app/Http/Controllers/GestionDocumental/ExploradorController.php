<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\CarpetaDocumental;
use App\Models\DocumentoGestion;
use App\Models\SeccionDocumental;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ExploradorController extends Controller
{
    /**
     * Explorador principal - Vista tipo Google Drive
     */
    public function index(Request $request)
    {
        $carpetaId = $request->carpeta_id;
        $seccionId = $request->seccion_id;
        $vista = $request->vista ?? 'cuadricula'; // cuadricula o lista
        $ordenar = $request->ordenar ?? 'nombre'; // nombre, fecha, tamano
        $busqueda = $request->q;

        // Carpeta actual
        $carpetaActual = $carpetaId ? CarpetaDocumental::findOrFail($carpetaId) : null;
        
        // Si no hay carpeta, mostrar secciones
        if (!$carpetaActual && !$seccionId) {
            return $this->mostrarSecciones($request);
        }

        // Si hay sección pero no carpeta, mostrar carpetas raíz de la sección
        if ($seccionId && !$carpetaActual) {
            return $this->mostrarCarpetasRaiz($seccionId, $request);
        }

        // Obtener subcarpetas de la carpeta actual
        $carpetas = CarpetaDocumental::where('carpeta_padre_id', $carpetaId)
            ->activas()
            ->ordenadas()
            ->get();

        // Obtener documentos de la carpeta actual
        $documentos = DocumentoGestion::where('carpeta_id', $carpetaId)
            ->when($busqueda, function($query) use ($busqueda) {
                $query->where('titulo', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%");
            })
            ->when($ordenar == 'nombre', function($query) {
                $query->orderBy('titulo');
            })
            ->when($ordenar == 'fecha', function($query) {
                $query->latest();
            })
            ->when($ordenar == 'tamano', function($query) {
                $query->orderBy('tamano_bytes', 'desc');
            })
            ->get();

        // Breadcrumb (ruta de navegación)
        $breadcrumb = $this->generarBreadcrumb($carpetaActual);

        // Estadísticas
        $estadisticas = [
            'total_carpetas' => $carpetas->count(),
            'total_archivos' => $documentos->count(),
            'espacio_usado' => $documentos->sum('tamano_bytes'),
            'total_compartidos' => \App\Models\ComparticionDocumento::where('compartido_por', auth()->id())->count(),
        ];

        return view('gestion-documental.explorador.index', compact(
            'carpetaActual',
            'carpetas',
            'documentos',
            'breadcrumb',
            'estadisticas',
            'vista',
            'ordenar'
        ));
    }

    /**
     * Mostrar secciones principales (vista raíz)
     */
    private function mostrarSecciones(Request $request)
    {
        $secciones = SeccionDocumental::activas()->ordenadas()->get();
        $breadcrumb = [];
        $vista = $request->vista ?? 'cuadricula';
        
        $estadisticas = [
            'total_carpetas' => \App\Models\CarpetaDocumental::count(),
            'total_archivos' => DocumentoGestion::count(),
            'espacio_usado' => DocumentoGestion::sum('tamano_bytes'),
            'total_compartidos' => \App\Models\ComparticionDocumento::where('compartido_por', auth()->id())->count(),
        ];

        return view('gestion-documental.explorador.secciones', compact(
            'secciones',
            'vista',
            'breadcrumb',
            'estadisticas'
        ));
    }

    /**
     * Mostrar carpetas raíz de una sección
     */
    private function mostrarCarpetasRaiz($seccionId, Request $request)
    {
        $seccion = SeccionDocumental::findOrFail($seccionId);
        $vista = $request->vista ?? 'cuadricula';
        $ordenar = $request->ordenar ?? 'nombre';
        
        $carpetas = CarpetaDocumental::where('seccion_id', $seccionId)
            ->whereNull('carpeta_padre_id')
            ->activas()
            ->ordenadas()
            ->get();

        $documentos = DocumentoGestion::where('seccion_id', $seccionId)
            ->whereNull('carpeta_id')
            ->get();

        $breadcrumb = [
            ['nombre' => 'Inicio', 'url' => route('gestion-documental.explorador.index')],
            ['nombre' => $seccion->nombre, 'url' => null],
        ];

        $estadisticas = [
            'total_carpetas' => $carpetas->count(),
            'total_archivos' => $documentos->count(),
            'espacio_usado' => $documentos->sum('tamano_bytes'),
            'total_compartidos' => \App\Models\ComparticionDocumento::where('compartido_por', auth()->id())->count(),
        ];

        $carpetaActual = null; // Para evitar undefined variable

        return view('gestion-documental.explorador.index', compact(
            'seccion',
            'carpetas',
            'documentos',
            'breadcrumb',
            'estadisticas',
            'vista',
            'ordenar',
            'carpetaActual'
        ));
    }

    /**
     * Generar breadcrumb de navegación
     */
    private function generarBreadcrumb($carpeta)
    {
        if (!$carpeta) {
            return [
                ['nombre' => 'Inicio', 'url' => route('gestion-documental.explorador.index')],
            ];
        }

        $breadcrumb = [];
        $carpetaActual = $carpeta;

        // Construir breadcrumb desde la carpeta actual hasta la raíz
        while ($carpetaActual) {
            array_unshift($breadcrumb, [
                'nombre' => $carpetaActual->nombre,
                'url' => route('gestion-documental.explorador.index', ['carpeta_id' => $carpetaActual->id]),
                'id' => $carpetaActual->id
            ]);
            $carpetaActual = $carpetaActual->carpetaPadre;
        }

        // Agregar la sección
        if ($carpeta->seccion) {
            array_unshift($breadcrumb, [
                'nombre' => $carpeta->seccion->nombre,
                'url' => route('gestion-documental.explorador.index', ['seccion_id' => $carpeta->seccion_id])
            ]);
        }

        // Agregar inicio
        array_unshift($breadcrumb, [
            'nombre' => 'Inicio',
            'url' => route('gestion-documental.explorador.index')
        ]);

        return $breadcrumb;
    }

    /**
     * Crear nueva carpeta
     */
    public function crearCarpeta(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'carpeta_padre_id' => 'nullable|exists:carpetas_documentales,id',
            'seccion_id' => 'required|exists:secciones_documentales,id',
            'color' => 'nullable|string|max:20',
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

        return response()->json([
            'success' => true,
            'message' => 'Carpeta creada exitosamente',
            'carpeta' => $carpeta
        ]);
    }

    /**
     * Subir archivo
     */
    public function subirArchivo(Request $request)
    {
        try {
            $validated = $request->validate([
                'archivo' => 'required|file|max:102400', // 100MB
                'carpeta_id' => 'nullable|exists:carpetas_documentales,id',
                'seccion_id' => 'required|exists:secciones_documentales,id',
                'titulo' => 'nullable|string|max:255',
            ]);

            $archivo = $request->file('archivo');
            
            // Generar nombre único
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $nombreArchivo = time() . '_' . Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME)) . '.' . $extension;
            
            // Guardar archivo
            $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

            // Crear registro en BD
            $titulo = $validated['titulo'] ?? pathinfo($nombreOriginal, PATHINFO_FILENAME);
            
            $documento = DocumentoGestion::create([
                'titulo' => $titulo,
                'slug' => Str::slug($titulo),
                'nombre_archivo' => $nombreArchivo,
                'nombre_original' => $nombreOriginal,
                'ruta' => $ruta,
                'extension' => $extension,
                'tamano_bytes' => $archivo->getSize(),
                'tipo_mime' => $archivo->getMimeType(),
                'carpeta_id' => $validated['carpeta_id'],
                'seccion_id' => $validated['seccion_id'],
                'subido_por' => auth()->id(),
                'estado' => 'aprobado', // Valores válidos: borrador, revision, aprobado, archivado, obsoleto
                'version' => 1,
                'es_version_actual' => true,
                'procesado' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Archivo subido exitosamente',
                'documento' => $documento
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . json_encode($e->errors())
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error al subir archivo: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al subir archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mover elemento (carpeta o archivo)
     */
    public function mover(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:carpeta,documento',
            'id' => 'required|integer',
            'destino_carpeta_id' => 'nullable|exists:carpetas_documentales,id',
        ]);

        if ($validated['tipo'] == 'carpeta') {
            $carpeta = CarpetaDocumental::findOrFail($validated['id']);
            $carpeta->update(['carpeta_padre_id' => $validated['destino_carpeta_id']]);
            $carpeta->actualizarRutaCompleta();
        } else {
            $documento = DocumentoGestion::findOrFail($validated['id']);
            $documento->update(['carpeta_id' => $validated['destino_carpeta_id']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Elemento movido exitosamente'
        ]);
    }

    /**
     * Renombrar elemento
     */
    public function renombrar(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:carpeta,documento',
            'id' => 'required|integer',
            'nuevo_nombre' => 'required|string|max:255',
        ]);

        if ($validated['tipo'] == 'carpeta') {
            $carpeta = CarpetaDocumental::findOrFail($validated['id']);
            $carpeta->update([
                'nombre' => $validated['nuevo_nombre'],
                'slug' => Str::slug($validated['nuevo_nombre'])
            ]);
            $carpeta->actualizarRutaCompleta();
        } else {
            $documento = DocumentoGestion::findOrFail($validated['id']);
            $documento->update(['titulo' => $validated['nuevo_nombre']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Elemento renombrado exitosamente'
        ]);
    }

    /**
     * Eliminar elemento
     */
    public function eliminar(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:carpeta,documento',
            'id' => 'required|integer',
        ]);

        if ($validated['tipo'] == 'carpeta') {
            $carpeta = CarpetaDocumental::findOrFail($validated['id']);
            
            // Verificar que no tenga contenido
            if ($carpeta->subcarpetas()->count() > 0 || $carpeta->documentos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una carpeta que contiene elementos'
                ], 400);
            }
            
            $carpeta->delete();
        } else {
            $documento = DocumentoGestion::findOrFail($validated['id']);
            
            // Eliminar archivo físico
            if (Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
            
            $documento->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Elemento eliminado exitosamente'
        ]);
    }

    /**
     * Obtener detalles de un elemento
     */
    public function detalles(Request $request)
    {
        $tipo = $request->tipo;
        $id = $request->id;

        if ($tipo == 'carpeta') {
            $elemento = CarpetaDocumental::with(['seccion', 'creadoPor'])->findOrFail($id);
            $elemento->total_items = $elemento->subcarpetas()->count() + $elemento->documentos()->count();
        } else {
            $elemento = DocumentoGestion::with(['seccion', 'carpeta', 'subidoPor'])->findOrFail($id);
        }

        return response()->json([
            'success' => true,
            'elemento' => $elemento
        ]);
    }
}

