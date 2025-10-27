<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleccion;
use App\Models\VotingToken;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class VotingTokenController extends Controller
{
    public function generarTokenPublico($eleccionId)
    {
        try {
            $eleccion = Eleccion::findOrFail($eleccionId);
            
            // Verificar que la elección esté activa
            $ahora = Carbon::now();
            $fechaInicio = $eleccion->fecha_inicio instanceof Carbon ? $eleccion->fecha_inicio : Carbon::parse($eleccion->fecha_inicio);
            $fechaFin = $eleccion->fecha_fin instanceof Carbon ? $eleccion->fecha_fin : Carbon::parse($eleccion->fecha_fin);
            
            if ($ahora->lt($fechaInicio) || $ahora->gt($fechaFin)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'La elección no está disponible en este momento.'
                ], 400);
            }
            
            // Generar token único
            $jti = Str::uuid()->toString();
            $voterHash = Hash::make($jti . $eleccionId . $ahora->timestamp);
            $tokenSignature = Hash::make($eleccionId . $jti . $ahora->timestamp);
            
            // Crear token con expiración de 1 hora
            $token = VotingToken::create([
                'jti' => $jti,
                'voter_hash' => $voterHash,
                'eleccion_id' => $eleccionId,
                'token_signature' => $tokenSignature,
                'used' => false,
                'issued_at' => $ahora,
                'expires_at' => $ahora->copy()->addHour(),
                'used_at' => null,
                'used_from_ip' => null,
                'user_agent' => null,
                'voto_id' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'token' => $jti,
                'url' => url("/votar/{$eleccionId}?token={$jti}"),
                'expires_at' => $token->expires_at->format('Y-m-d H:i:s'),
                'mensaje' => 'Token generado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al generar el token: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function generarTokenPrivado($eleccionId, Request $request)
    {
        try {
            $eleccion = Eleccion::findOrFail($eleccionId);
            
            // Verificar que la elección esté activa
            $ahora = Carbon::now();
            $fechaInicio = $eleccion->fecha_inicio instanceof Carbon ? $eleccion->fecha_inicio : Carbon::parse($eleccion->fecha_inicio);
            $fechaFin = $eleccion->fecha_fin instanceof Carbon ? $eleccion->fecha_fin : Carbon::parse($eleccion->fecha_fin);
            
            if ($ahora->lt($fechaInicio) || $ahora->gt($fechaFin)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'La elección no está disponible en este momento.'
                ], 400);
            }
            
            // Validar datos del usuario
            $request->validate([
                'cedula_votante' => 'required|string|max:20',
                'nombre_votante' => 'required|string|max:255'
            ]);
            
            // Verificar si ya existe un token para esta cédula en esta elección
            $tokenExistente = VotingToken::where('eleccion_id', $eleccionId)
                ->where('voter_hash', Hash::make($request->cedula_votante))
                ->where('used', false)
                ->where('expires_at', '>', $ahora)
                ->first();
                
            if ($tokenExistente) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Ya existe un token activo para esta cédula en esta elección.'
                ], 400);
            }
            
            // Generar token único
            $jti = Str::uuid()->toString();
            $voterHash = Hash::make($request->cedula_votante . $eleccionId . $ahora->timestamp);
            $tokenSignature = Hash::make($eleccionId . $jti . $ahora->timestamp);
            
            // Crear token con expiración de 30 minutos
            $token = VotingToken::create([
                'jti' => $jti,
                'voter_hash' => $voterHash,
                'eleccion_id' => $eleccionId,
                'token_signature' => $tokenSignature,
                'used' => false,
                'issued_at' => $ahora,
                'expires_at' => $ahora->copy()->addMinutes(30),
                'used_at' => null,
                'used_from_ip' => null,
                'user_agent' => null,
                'voto_id' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'token' => $jti,
                'url' => url("/votar/{$eleccionId}?token={$jti}"),
                'expires_at' => $token->expires_at->format('Y-m-d H:i:s'),
                'mensaje' => 'Token privado generado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al generar el token: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function validarToken($eleccionId, $token)
    {
        try {
            $tokenData = VotingToken::where('jti', $token)
                ->where('eleccion_id', $eleccionId)
                ->first();
                
            if (!$tokenData) {
                return response()->json([
                    'valid' => false,
                    'mensaje' => 'Token no válido'
                ], 404);
            }
            
            $ahora = Carbon::now();
            
            // Verificar si el token ha expirado
            if ($ahora->gt($tokenData->expires_at)) {
                return response()->json([
                    'valid' => false,
                    'mensaje' => 'Token expirado'
                ], 400);
            }
            
            // Verificar si el token ya fue usado
            if ($tokenData->used) {
                return response()->json([
                    'valid' => false,
                    'mensaje' => 'Token ya utilizado'
                ], 400);
            }
            
            return response()->json([
                'valid' => true,
                'token' => $tokenData,
                'mensaje' => 'Token válido'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'mensaje' => 'Error al validar el token: ' . $e->getMessage()
            ], 500);
        }
    }
}