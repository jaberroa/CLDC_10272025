<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\DocumentoGestion;
use App\Models\CarpetaDocumental;
use App\Models\SeccionDocumental;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentoGestion::with(['seccion', 'carpeta', 'subidoPor'])
            ->versionActual();

        // Búsqueda por texto
        if ($request->q) {
            $query->buscar($request->q);
        }

        // Filtros avanzados
        if ($request->seccion_id) {
            $query->where('seccion_id', $request->seccion_id);
        }

        if ($request->carpeta_id) {
            $query->where('carpeta_id', $request->carpeta_id);
        }

        if ($request->extension) {
            $query->where('extension', $request->extension);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->confidencial !== null) {
            $query->where('confidencial', $request->confidencial);
        }

        if ($request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->subido_por) {
            $query->where('subido_por', $request->subido_por);
        }

        // Ordenamiento
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        $documentos = $query->paginate(20)->appends($request->query());

        $secciones = SeccionDocumental::activas()->get();
        $extensiones = DocumentoGestion::select('extension')
            ->distinct()
            ->pluck('extension');

        return view('gestion-documental.busqueda.index', compact(
            'documentos',
            'secciones',
            'extensiones'
        ));
    }

    public function api(Request $request)
    {
        $query = DocumentoGestion::versionActual();

        if ($request->q) {
            $query->buscar($request->q);
        }

        $documentos = $query->limit(10)->get(['id', 'titulo', 'extension', 'ruta']);

        return response()->json($documentos);
    }
}
