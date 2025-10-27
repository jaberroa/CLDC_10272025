<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\DocumentoGestion;
use App\Models\AprobacionDocumento;
use App\Models\FlujoAprobacion;
use App\Models\AuditoriaDocumento;
use Illuminate\Http\Request;

class AprobacionesController extends Controller
{
    public function misPendientes()
    {
        $aprobaciones = AprobacionDocumento::with(['documento', 'flujo'])
            ->where('aprobador_id', auth()->id())
            ->pendientes()
            ->latest()
            ->paginate(20);

        return view('gestion-documental.aprobaciones.mis-pendientes', compact('aprobaciones'));
    }

    public function aprobar(Request $request, AprobacionDocumento $aprobacion)
    {
        if ($aprobacion->aprobador_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'comentarios' => 'nullable|string|max:1000'
        ]);

        $aprobacion->aprobar($request->comentarios);

        AuditoriaDocumento::registrar(
            'aprobar',
            $aprobacion->documento,
            'Documento aprobado',
            ['aprobacion_id' => $aprobacion->id]
        );

        // Verificar si todas las aprobaciones están completadas
        $pendientes = $aprobacion->documento->aprobaciones()->pendientes()->count();
        if ($pendientes === 0) {
            $aprobacion->documento->aprobar();
        }

        return back()->with('success', 'Documento aprobado exitosamente');
    }

    public function rechazar(Request $request, AprobacionDocumento $aprobacion)
    {
        if ($aprobacion->aprobador_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'razon_rechazo' => 'required|string|max:1000'
        ]);

        $aprobacion->rechazar($request->razon_rechazo);

        AuditoriaDocumento::registrar(
            'rechazar',
            $aprobacion->documento,
            'Documento rechazado',
            ['razon' => $request->razon_rechazo]
        );

        return back()->with('success', 'Documento rechazado');
    }

    public function historial(DocumentoGestion $documento)
    {
        $aprobaciones = $documento->aprobaciones()
            ->with(['aprobador', 'flujo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gestion-documental.aprobaciones.historial', compact('documento', 'aprobaciones'));
    }
}
