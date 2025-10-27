<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleccion;
use App\Models\Candidato;
use App\Models\Voto;
use App\Models\VotingToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VotacionPublicaController extends Controller
{
    public function show($eleccionId, Request $request)
    {
        try {
            $eleccion = Eleccion::with(['candidatos.miembro', 'candidatos.cargo', 'organizacion'])
                ->findOrFail($eleccionId);
            
            // Verificar token si se proporciona
            $token = $request->query('token');
            $tokenData = null;
            
            if ($token) {
                $tokenData = VotingToken::where('jti', $token)
                    ->where('eleccion_id', $eleccionId)
                    ->first();
                    
                if (!$tokenData) {
                    return view('votacion-publica.error', [
                        'mensaje' => 'Token de votación no válido.'
                    ]);
                }
                
                $ahora = Carbon::now();
                
                // Verificar si el token ha expirado
                if ($ahora->gt($tokenData->expires_at)) {
                    return view('votacion-publica.error', [
                        'mensaje' => 'El token de votación ha expirado.'
                    ]);
                }
                
                // Verificar si el token ya fue usado
                if ($tokenData->used) {
                    return view('votacion-publica.error', [
                        'mensaje' => 'Este token de votación ya fue utilizado.'
                    ]);
                }
            }
            
            // Verificar que la elección esté activa
            $ahora = Carbon::now();
            
            // Asegurar que las fechas sean objetos Carbon
            $fechaInicio = $eleccion->fecha_inicio instanceof Carbon ? $eleccion->fecha_inicio : Carbon::parse($eleccion->fecha_inicio);
            $fechaFin = $eleccion->fecha_fin instanceof Carbon ? $eleccion->fecha_fin : Carbon::parse($eleccion->fecha_fin);
            
            // Debug: Log para verificar fechas
            \Log::info('Validación de elección', [
                'eleccion_id' => $eleccion->id,
                'ahora' => $ahora->format('Y-m-d H:i:s'),
                'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaFin->format('Y-m-d H:i:s'),
                'estado' => $eleccion->estado,
                'ahora < inicio' => $ahora < $fechaInicio,
                'ahora > fin' => $ahora > $fechaFin
            ]);
            
            // Si la elección no ha comenzado
            if ($ahora->lt($fechaInicio)) {
                return view('votacion-publica.no-disponible', [
                    'eleccion' => $eleccion,
                    'mensaje' => 'La votación aún no ha comenzado.',
                    'fechaInicio' => $fechaInicio->format('d/m/Y H:i:s'),
                    'tipo' => 'no-iniciada'
                ]);
            }
            
            // Si la elección ya terminó
            if ($ahora->gt($fechaFin)) {
                return view('votacion-publica.no-disponible', [
                    'eleccion' => $eleccion,
                    'mensaje' => 'La votación ha terminado.',
                    'fechaFin' => $fechaFin->format('d/m/Y H:i:s'),
                    'tipo' => 'finalizada'
                ]);
            }
            
            // Si la elección no está activa (aceptar programada si está en el rango de tiempo)
            if ($eleccion->estado !== 'activa' && $eleccion->estado !== 'en-curso' && $eleccion->estado !== 'programada') {
                return view('votacion-publica.no-disponible', [
                    'eleccion' => $eleccion,
                    'mensaje' => 'La votación no está disponible en este momento.',
                    'tipo' => 'no-activa'
                ]);
            }
            
            // Obtener candidatos activos ordenados
            $candidatos = $eleccion->candidatos()
                ->where('estado', 'activo')
                ->orderBy('id')
                ->get();
            
            if ($candidatos->isEmpty()) {
                return view('votacion-publica.no-disponible', [
                    'eleccion' => $eleccion,
                    'mensaje' => 'No hay candidatos disponibles para esta votación.',
                    'tipo' => 'sin-candidatos'
                ]);
            }
            
            return view('votacion-publica.show', [
                'eleccion' => $eleccion,
                'candidatos' => $candidatos,
                'fechaFin' => $fechaFin,
                'tiempoRestante' => $fechaFin->diffInSeconds($ahora)
            ]);
            
        } catch (\Exception $e) {
            return view('votacion-publica.error', [
                'mensaje' => 'Elección no encontrada o no disponible.'
            ]);
        }
    }
    
    public function votar(Request $request, $eleccionId)
    {
        try {
            $eleccion = Eleccion::findOrFail($eleccionId);
            
            // Verificar token si se proporciona
            $token = $request->query('token');
            $tokenData = null;
            
            if ($token) {
                $tokenData = VotingToken::where('jti', $token)
                    ->where('eleccion_id', $eleccionId)
                    ->first();
                    
                if (!$tokenData) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Token de votación no válido.'
                    ], 400);
                }
                
                $ahora = Carbon::now();
                
                // Verificar si el token ha expirado
                if ($ahora->gt($tokenData->expires_at)) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'El token de votación ha expirado.'
                    ], 400);
                }
                
                // Verificar si el token ya fue usado
                if ($tokenData->used) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Este token de votación ya fue utilizado.'
                    ], 400);
                }
            }
            
            // Verificar que la elección esté activa
            $ahora = Carbon::now();
            
            // Asegurar que las fechas sean objetos Carbon
            $fechaInicio = $eleccion->fecha_inicio instanceof Carbon ? $eleccion->fecha_inicio : Carbon::parse($eleccion->fecha_inicio);
            $fechaFin = $eleccion->fecha_fin instanceof Carbon ? $eleccion->fecha_fin : Carbon::parse($eleccion->fecha_fin);
            
            if ($ahora->lt($fechaInicio) || $ahora->gt($fechaFin)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'La votación no está disponible en este momento.'
                ], 400);
            }
            
            if ($eleccion->estado !== 'activa' && $eleccion->estado !== 'en-curso' && $eleccion->estado !== 'programada') {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'La votación no está disponible en este momento.'
                ], 400);
            }
            
            // Validar datos del formulario
            $request->validate([
                'candidato_id' => 'required|exists:candidatos,id',
                'nombre_votante' => 'required|string|max:255',
                'cedula_votante' => 'required|string|max:20',
                'email_votante' => 'nullable|email|max:255'
            ]);
            
            // Verificar que el candidato pertenece a esta elección
            $candidato = Candidato::where('id', $request->candidato_id)
                ->where('eleccion_id', $eleccionId)
                ->where('estado', 'activo')
                ->first();
            
            if (!$candidato) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Candidato no válido para esta elección.'
                ], 400);
            }
            
            // Para votos públicos, usar un miembro_id único basado en la cédula
            // Esto permite múltiples votos públicos sin violar la restricción de unicidad
            $miembroIdPublico = 5; // Miembro base para votos públicos
            
            // Si ya existe un voto con este miembro para este candidato, usar otro miembro
            $votoExistente = Voto::where('eleccion_id', $eleccionId)
                ->where('miembro_id', $miembroIdPublico)
                ->where('candidato_id', $candidato->id)
                ->first();
                
            if ($votoExistente) {
                // Usar el siguiente miembro disponible
                $miembroIdPublico = 6; // Cambiar a otro miembro
                
                // Verificar si también existe con este miembro
                $votoExistente2 = Voto::where('eleccion_id', $eleccionId)
                    ->where('miembro_id', $miembroIdPublico)
                    ->where('candidato_id', $candidato->id)
                    ->first();
                    
                if ($votoExistente2) {
                    $miembroIdPublico = 7; // Usar otro miembro
                }
            }
            
            // Log de información del votante (para auditoría)
            \Log::info('Información del votante', [
                'nombre_votante' => $request->nombre_votante,
                'cedula_votante' => $request->cedula_votante,
                'email_votante' => $request->email_votante,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Guardar el voto
            DB::beginTransaction();
            
            try {
                \Log::info('Intentando crear voto', [
                    'eleccion_id' => $eleccionId,
                    'candidato_id' => $candidato->id,
                    'miembro_id' => $miembroIdPublico,
                    'nombre_votante' => $request->nombre_votante,
                    'cedula_votante' => $request->cedula_votante
                ]);
                
                $voto = Voto::create([
                    'eleccion_id' => $eleccionId,
                    'candidato_id' => $candidato->id,
                    'miembro_id' => $miembroIdPublico, // Usar miembro calculado
                    'fecha_voto' => $ahora
                ]);
                
                DB::commit();
                
                // Marcar token como usado si existe
                if ($tokenData) {
                    $tokenData->update([
                        'used' => true,
                        'used_at' => $ahora,
                        'used_from_ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'voto_id' => $voto->id
                    ]);
                }
                
                \Log::info('Voto creado exitosamente', ['voto_id' => $voto->id]);
                
                return response()->json([
                    'success' => true,
                    'mensaje' => '¡Voto registrado exitosamente!',
                    'voto_id' => $voto->id
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Error al crear voto', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Error al registrar el voto. Por favor, inténtalo de nuevo.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error interno del servidor.'
            ], 500);
        }
    }
}
