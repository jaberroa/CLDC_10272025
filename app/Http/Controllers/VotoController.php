<?php

namespace App\Http\Controllers;

use App\Models\Eleccion;
use App\Models\Candidato;
use App\Models\Voto;
use App\Models\AuditoriaVoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Registrar voto
     */
    public function store(Request $request)
    {
        try {
            // Validación de entrada
            $validated = $request->validate([
                'eleccion_id' => 'required|exists:elecciones,id',
                'candidato_id' => 'required|exists:candidatos,id',
            ]);

            $eleccion = Eleccion::findOrFail($validated['eleccion_id']);
            $candidato = Candidato::findOrFail($validated['candidato_id']);
            $userId = Auth::id();

            // 1. Verificar que la elección esté activa
            if (!$eleccion->estaActiva()) {
                AuditoriaVoto::registrar(
                    'intento_voto_eleccion_inactiva',
                    $userId,
                    $eleccion->id,
                    'Elección no está activa o ha finalizado'
                );

                throw ValidationException::withMessages([
                    'eleccion' => 'Esta elección no está activa o ya ha finalizado.'
                ]);
            }

            // 2. Verificar que el candidato pertenezca a la elección
            if ($candidato->eleccion_id !== $eleccion->id) {
                AuditoriaVoto::registrar(
                    'intento_voto_candidato_invalido',
                    $userId,
                    $eleccion->id,
                    "Candidato {$candidato->id} no pertenece a elección {$eleccion->id}"
                );

                throw ValidationException::withMessages([
                    'candidato' => 'El candidato seleccionado no pertenece a esta elección.'
                ]);
            }

            // 3. Verificar que el usuario no haya votado ya
            if ($eleccion->usuarioYaVoto($userId)) {
                AuditoriaVoto::registrar(
                    'intento_voto_duplicado',
                    $userId,
                    $eleccion->id,
                    'Usuario intentó votar más de una vez'
                );

                throw ValidationException::withMessages([
                    'voto' => 'Ya has registrado tu voto en esta elección.'
                ]);
            }

            // 4. Registrar voto en transacción
            DB::beginTransaction();

            $voto = Voto::create([
                'user_id' => $userId,
                'eleccion_id' => $eleccion->id,
                'candidato_id' => $candidato->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Registrar en auditoría
            AuditoriaVoto::registrar(
                'voto_registrado',
                $userId,
                $eleccion->id,
                json_encode([
                    'candidato_id' => $candidato->id,
                    'candidato_nombre' => $candidato->nombre,
                    'cargo' => $candidato->cargo,
                    'hash' => $voto->hash,
                ]),
                $voto->id
            );

            DB::commit();

            // Log del voto
            Log::info('Voto registrado', [
                'voto_id' => $voto->id,
                'user_id' => $userId,
                'eleccion_id' => $eleccion->id,
                'candidato_id' => $candidato->id,
                'hash' => $voto->hash,
                'timestamp' => $voto->created_at,
            ]);

            // Respuesta según tipo de request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Voto registrado exitosamente!',
                    'data' => [
                        'voto_id' => $voto->id,
                        'hash' => $voto->hash,
                        'timestamp' => $voto->created_at->toIso8601String(),
                    ]
                ], 201);
            }

            return redirect()
                ->route('elecciones.index')
                ->with('success', '¡Tu voto ha sido registrado exitosamente! Gracias por participar.');

        } catch (ValidationException $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al registrar voto', [
                'user_id' => Auth::id(),
                'eleccion_id' => $request->eleccion_id ?? null,
                'candidato_id' => $request->candidato_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el voto. Por favor, inténtalo nuevamente.'
                ], 500);
            }

            return back()
                ->with('error', 'Error al registrar el voto. Por favor, inténtalo nuevamente.')
                ->withInput();
        }
    }

    /**
     * Verificar si el usuario ya votó en una elección
     */
    public function verificarVoto(Request $request, $eleccionId)
    {
        $eleccion = Eleccion::findOrFail($eleccionId);
        $userId = Auth::id();

        $yaVoto = $eleccion->usuarioYaVoto($userId);

        if ($request->expectsJson()) {
            return response()->json([
                'ya_voto' => $yaVoto,
                'puede_votar' => !$yaVoto && $eleccion->estaActiva(),
            ]);
        }

        return response()->json(['ya_voto' => $yaVoto]);
    }

    /**
     * Obtener el voto del usuario en una elección (solo hash, no candidato)
     */
    public function miVoto($eleccionId)
    {
        $voto = Voto::where('user_id', Auth::id())
            ->where('eleccion_id', $eleccionId)
            ->first();

        if (!$voto) {
            return response()->json([
                'success' => false,
                'message' => 'No has votado en esta elección'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'hash' => $voto->hash,
                'timestamp' => $voto->created_at->toIso8601String(),
                'verificado' => $voto->verificarIntegridad(),
            ]
        ]);
    }
}
