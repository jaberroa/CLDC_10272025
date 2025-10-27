<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\DocumentoGestion;
use App\Models\CarpetaDocumental;
use App\Models\SeccionDocumental;
use App\Models\AuditoriaDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentosGestionController extends Controller
{
    public function __construct()
    {
        // El middleware auth ya está aplicado en las rutas
        // $this->middleware('auth');
        // Comentado temporalmente para desarrollo
        // $this->authorizeResource(DocumentoGestion::class, 'documento');
    }

    public function index(Request $request)
    {
        $query = DocumentoGestion::with(['seccion', 'carpeta', 'subidoPor'])
            ->versionActual();

        // Filtros
        if ($request->seccion_id) {
            $query->where('seccion_id', $request->seccion_id);
        }

        if ($request->carpeta_id) {
            $query->where('carpeta_id', $request->carpeta_id);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->buscar) {
            $query->buscar($request->buscar);
        }

        $documentos = $query->latest()->paginate(20);
        $secciones = SeccionDocumental::activas()->get();

        return view('gestion-documental.documentos.index', compact('documentos', 'secciones'));
    }

    public function create(Request $request)
    {
        $seccionId = $request->seccion_id;
        $carpetaId = $request->carpeta_id;

        $secciones = SeccionDocumental::activas()->ordenadas()->get();
        $carpetas = $seccionId 
            ? CarpetaDocumental::where('seccion_id', $seccionId)->activas()->ordenadas()->get()
            : collect();

        return view('gestion-documental.documentos.create', compact('secciones', 'carpetas', 'seccionId', 'carpetaId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seccion_id' => 'required|exists:secciones_documentales,id',
            'carpeta_id' => 'required|exists:carpetas_documentales,id',
            'titulo' => 'required|string|max:500',
            'descripcion' => 'nullable|string',
            'archivo' => 'required|file|max:' . ($request->max_size ?? 51200), // 50MB default
            'estado' => 'nullable|in:borrador,revision,aprobado',
            'confidencial' => 'boolean',
            'nivel_acceso' => 'nullable|in:publico,interno,confidencial,restringido',
            'fecha_documento' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'entidad_tipo' => 'nullable|string',
            'entidad_id' => 'nullable|integer',
        ]);

        $seccion = SeccionDocumental::find($request->seccion_id);
        $archivo = $request->file('archivo');

        // Validar formato y tamaño según sección
        $extension = $archivo->getClientOriginalExtension();
        if (!$seccion->puedeSubirFormato($extension)) {
            return back()->with('error', 'Formato de archivo no permitido para esta sección');
        }

        $tamanoMB = $archivo->getSize() / 1024 / 1024;
        if (!$seccion->puedeSubirTamano($tamanoMB)) {
            return back()->with('error', "El archivo excede el tamaño máximo permitido ({$seccion->max_tamano_archivo_mb} MB)");
        }

        // Generar nombre único
        $nombreArchivo = time() . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;
        
        // Guardar archivo
        $ruta = $archivo->storeAs(
            "documentos_gestion/{$request->seccion_id}/{$request->carpeta_id}",
            $nombreArchivo,
            'public'
        );

        // Crear documento
        $documento = DocumentoGestion::create([
            'seccion_id' => $request->seccion_id,
            'carpeta_id' => $request->carpeta_id,
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo),
            'descripcion' => $request->descripcion,
            'nombre_original' => $archivo->getClientOriginalName(),
            'nombre_archivo' => $nombreArchivo,
            'ruta' => $ruta,
            'extension' => $extension,
            'tipo_mime' => $archivo->getMimeType(),
            'tamano_bytes' => $archivo->getSize(),
            'hash_archivo' => hash_file('sha256', $archivo->getRealPath()),
            'version' => 1,
            'es_version_actual' => true,
            'estado' => $request->estado ?? 'borrador',
            'confidencial' => $request->confidencial ?? false,
            'nivel_acceso' => $request->nivel_acceso ?? 'interno',
            'fecha_documento' => $request->fecha_documento,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'entidad_tipo' => $request->entidad_tipo,
            'entidad_id' => $request->entidad_id,
            'subido_por' => auth()->id(),
            'procesado' => false,
        ]);

        // Actualizar estadísticas de carpeta
        $documento->carpeta->actualizarEstadisticas();

        // Registrar auditoría
        AuditoriaDocumento::registrar('crear', $documento, 'Documento subido');

        // TODO: Procesar documento en background (preview, indexar contenido)

        return redirect()
            ->route('gestion-documental.documentos.show', $documento)
            ->with('success', 'Documento subido exitosamente');
    }

    public function show(DocumentoGestion $documento)
    {
        $documento->load([
            'seccion',
            'carpeta',
            'subidoPor',
            'versiones' => function($query) {
                $query->orderBy('numero_version', 'desc');
            },
            'metadatos.campo',
            'comparticiones' => function($query) {
                $query->activas();
            }
        ]);

        // Incrementar visualizaciones
        $documento->incrementarVisualizaciones();

        return view('gestion-documental.documentos.show', compact('documento'));
    }

    public function edit(DocumentoGestion $documento)
    {
        $secciones = SeccionDocumental::activas()->ordenadas()->get();
        $carpetas = CarpetaDocumental::where('seccion_id', $documento->seccion_id)
            ->activas()
            ->ordenadas()
            ->get();

        return view('gestion-documental.documentos.edit', compact('documento', 'secciones', 'carpetas'));
    }

    public function update(Request $request, DocumentoGestion $documento)
    {
        $request->validate([
            'titulo' => 'required|string|max:500',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|in:borrador,revision,aprobado,archivado,obsoleto',
            'confidencial' => 'boolean',
            'nivel_acceso' => 'nullable|in:publico,interno,confidencial,restringido',
            'fecha_documento' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'fecha_revision' => 'nullable|date',
        ]);

        $datosAnteriores = $documento->only(['titulo', 'descripcion', 'estado', 'nivel_acceso']);
        
        $documento->update([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo),
            'descripcion' => $request->descripcion,
            'estado' => $request->estado ?? $documento->estado,
            'confidencial' => $request->confidencial ?? $documento->confidencial,
            'nivel_acceso' => $request->nivel_acceso ?? $documento->nivel_acceso,
            'fecha_documento' => $request->fecha_documento,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'fecha_revision' => $request->fecha_revision,
            'actualizado_por' => auth()->id(),
        ]);

        AuditoriaDocumento::registrar(
            'editar',
            $documento,
            'Documento actualizado',
            ['datos_anteriores' => $datosAnteriores]
        );

        return redirect()
            ->route('gestion-documental.documentos.show', $documento)
            ->with('success', 'Documento actualizado exitosamente');
    }

    public function destroy(DocumentoGestion $documento)
    {
        $nombreDocumento = $documento->titulo;

        // Registrar auditoría antes de eliminar
        AuditoriaDocumento::registrar('eliminar', $documento, 'Documento eliminado');

        // Eliminar archivo físico
        if (Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->delete($documento->ruta);
        }

        // Eliminar versiones
        foreach ($documento->versiones as $version) {
            if (Storage::disk('public')->exists($version->ruta)) {
                Storage::disk('public')->delete($version->ruta);
            }
        }

        $documento->delete();

        // Actualizar estadísticas de carpeta
        $documento->carpeta->actualizarEstadisticas();

        return redirect()
            ->route('gestion-documental.documentos.index')
            ->with('success', "Documento '{$nombreDocumento}' eliminado exitosamente");
    }

    /**
     * Download document
     */
    public function descargar(DocumentoGestion $documento)
    {
        if (!Storage::disk('public')->exists($documento->ruta)) {
            abort(404, 'Archivo no encontrado');
        }

        $documento->incrementarDescargas();

        AuditoriaDocumento::registrar('descargar', $documento, 'Documento descargado');

        return Storage::disk('public')->download($documento->ruta, $documento->nombre_original);
    }

    /**
     * Preview document
     */
    public function preview(DocumentoGestion $documento)
    {
        if (!Storage::disk('public')->exists($documento->ruta)) {
            abort(404, 'Archivo no encontrado');
        }

        $documento->incrementarVisualizaciones();

        AuditoriaDocumento::registrar('ver', $documento, 'Vista previa del documento');

        return response()->file(Storage::disk('public')->path($documento->ruta));
    }

    /**
     * Duplicate document
     */
    public function duplicar(DocumentoGestion $documento)
    {
        $nuevo = $documento->duplicar();

        AuditoriaDocumento::registrar(
            'duplicar',
            $nuevo,
            'Documento duplicado',
            ['documento_original_id' => $documento->id]
        );

        return redirect()
            ->route('gestion-documental.documentos.show', $nuevo)
            ->with('success', 'Documento duplicado exitosamente');
    }

    /**
     * Move document to another folder
     */
    public function mover(Request $request, DocumentoGestion $documento)
    {
        $request->validate([
            'nueva_carpeta_id' => 'required|exists:carpetas_documentales,id'
        ]);

        $carpetaAnterior = $documento->carpeta;
        $documento->update(['carpeta_id' => $request->nueva_carpeta_id]);

        // Actualizar estadísticas
        $carpetaAnterior->actualizarEstadisticas();
        $documento->carpeta->actualizarEstadisticas();

        AuditoriaDocumento::registrar(
            'mover',
            $documento,
            'Documento movido',
            [
                'carpeta_anterior_id' => $carpetaAnterior->id,
                'carpeta_nueva_id' => $documento->carpeta_id
            ]
        );

        return back()->with('success', 'Documento movido exitosamente');
    }
}
