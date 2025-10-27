<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\DocumentoGestion;
use App\Models\SolicitudFirma;
use App\Models\FirmanteDocumento;
use App\Models\AuditoriaDocumento;
use Illuminate\Http\Request;

class FirmasController extends Controller
{
    public function solicitar(Request $request, DocumentoGestion $documento)
    {
        $request->validate([
            'titulo' => 'required|string|max:500',
            'mensaje' => 'nullable|string',
            'tipo' => 'required|in:simple,secuencial,paralelo',
            'fecha_limite' => 'nullable|date|after:today',
            'firmantes' => 'required|array|min:1',
            'firmantes.*.usuario_id' => 'nullable|exists:users,id',
            'firmantes.*.email' => 'required_without:firmantes.*.usuario_id|email',
            'firmantes.*.nombre' => 'required_with:firmantes.*.email|string',
        ]);

        $solicitud = SolicitudFirma::create([
            'documento_id' => $documento->id,
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje,
            'tipo' => $request->tipo,
            'fecha_limite' => $request->fecha_limite,
            'total_firmantes' => count($request->firmantes),
            'creado_por' => auth()->id(),
        ]);

        foreach ($request->firmantes as $index => $firmante) {
            FirmanteDocumento::create([
                'solicitud_id' => $solicitud->id,
                'usuario_id' => $firmante['usuario_id'] ?? null,
                'email' => $firmante['email'] ?? null,
                'nombre' => $firmante['nombre'] ?? null,
                'orden' => $index + 1,
                'estado' => 'pendiente',
            ]);
        }

        $documento->update(['requiere_firma' => true]);

        AuditoriaDocumento::registrar('solicitar_firma', $documento, 'Solicitud de firma creada');

        // TODO: Enviar notificaciones a firmantes

        return back()->with('success', 'Solicitud de firma enviada exitosamente');
    }

    public function verFirma($token)
    {
        $firmante = FirmanteDocumento::with(['solicitud.documento'])
            ->where('token', $token)
            ->where('estado', '!=', 'firmado')
            ->firstOrFail();

        $firmante->update([
            'estado' => 'visto',
            'fecha_visto' => now()
        ]);

        return view('gestion-documental.firmas.firmar', compact('firmante'));
    }

    public function firmar(Request $request, $token)
    {
        $request->validate([
            'firma_imagen' => 'required|string',
            'firma_tipo' => 'required|in:dibujada,texto,certificado',
        ]);

        $firmante = FirmanteDocumento::where('token', $token)->firstOrFail();

        $firmante->firmar([
            'imagen' => $request->firma_imagen,
            'tipo' => $request->firma_tipo,
        ]);

        AuditoriaDocumento::registrar(
            'firmar',
            $firmante->solicitud->documento,
            'Documento firmado',
            ['firmante_id' => $firmante->id]
        );

        return view('gestion-documental.firmas.completado', compact('firmante'));
    }

    public function misPendientes()
    {
        $firmantes = FirmanteDocumento::with(['solicitud.documento'])
            ->where('usuario_id', auth()->id())
            ->where('estado', '!=', 'firmado')
            ->latest()
            ->paginate(20);

        return view('gestion-documental.firmas.mis-pendientes', compact('firmantes'));
    }
}
