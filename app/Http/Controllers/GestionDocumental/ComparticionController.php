<?php

namespace App\Http\Controllers\GestionDocumental;

use App\Http\Controllers\Controller;
use App\Models\DocumentoGestion;
use App\Models\ComparticionDocumento;
use App\Models\AuditoriaDocumento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ComparticionController extends Controller
{
    public function compartir(Request $request, DocumentoGestion $documento)
    {
        $request->validate([
            'tipo' => 'required|in:interno,enlace,email',
            'usuario_id' => 'required_if:tipo,interno|exists:users,id',
            'permiso' => 'nullable|in:ver,descargar,editar',
            'email' => 'required_if:tipo,email|email',
            'password' => 'nullable|string|min:4',
            'fecha_expiracion' => 'nullable|date',
            'max_accesos' => 'nullable|integer|min:1',
            'mensaje' => 'nullable|string',
            'notificar' => 'boolean',
        ]);

        // Mapear tipo de compartición
        $tipoMapeado = $request->tipo === 'enlace' ? 'publico' : 
                      ($request->tipo === 'email' ? 'externo' : 'interno');

        $data = [
            'documento_id' => $documento->id,
            'tipo' => $tipoMapeado,
            'compartido_por' => auth()->id(),
            'puede_ver' => true,
            'mensaje' => $request->mensaje,
            'fecha_expiracion' => $request->fecha_expiracion,
            'max_accesos' => $request->max_accesos,
        ];

        // Configurar según tipo
        if ($request->tipo === 'interno') {
            $data['usuario_id'] = $request->usuario_id;
            $data['puede_descargar'] = in_array($request->permiso, ['descargar', 'editar']);
            $data['puede_comentar'] = $request->permiso === 'editar';
        } elseif ($request->tipo === 'enlace') {
            $data['puede_descargar'] = true;
            $data['puede_comentar'] = false;
            if ($request->password) {
                $data['password_hash'] = Hash::make($request->password);
            }
        } elseif ($request->tipo === 'email') {
            $data['email_externo'] = $request->email;
            $data['puede_descargar'] = true;
            $data['puede_comentar'] = false;
        }

        $comparticion = ComparticionDocumento::create($data);

        $documento->increment('total_compartidos');

        AuditoriaDocumento::registrar('compartir', $documento, 'Documento compartido');

        // TODO: Enviar notificaciones si $request->notificar es true

        $response = [
            'success' => true,
            'message' => 'Documento compartido exitosamente'
        ];

        // Si es enlace público, devolver la URL
        if ($request->tipo === 'enlace') {
            $response['enlace'] = url('/documentos/compartido/' . $comparticion->token);
        }

        return response()->json($response);
    }

    public function verCompartido($token)
    {
        $comparticion = ComparticionDocumento::with('documento')
            ->where('token', $token)
            ->activas()
            ->firstOrFail();

        // Verificar password si es necesario
        if ($comparticion->password_hash) {
            if (!session()->has('comparticion_' . $comparticion->id . '_autenticado')) {
                return view('gestion-documental.comparticion.password', compact('comparticion'));
            }
        }

        $comparticion->registrarAcceso();

        return view('gestion-documental.comparticion.ver', compact('comparticion'));
    }

    public function verificarPassword(Request $request, $token)
    {
        $request->validate(['password' => 'required']);

        $comparticion = ComparticionDocumento::where('token', $token)->firstOrFail();

        if (!Hash::check($request->password, $comparticion->password_hash)) {
            return back()->with('error', 'Contraseña incorrecta');
        }

        session(['comparticion_' . $comparticion->id . '_autenticado' => true]);

        return redirect()->route('gestion-documental.comparticion.ver', $token);
    }

    public function revocar(ComparticionDocumento $comparticion)
    {
        $comparticion->update(['activa' => false]);

        AuditoriaDocumento::registrar(
            'revocar_comparticion',
            $comparticion->documento,
            'Compartición revocada'
        );

        return back()->with('success', 'Compartición revocada exitosamente');
    }
}
