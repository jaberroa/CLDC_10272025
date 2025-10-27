<?php

namespace App\Http\Controllers;

use App\Models\Eleccion;
use App\Models\User;
use App\Services\VotingTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VotingLinkController extends Controller
{
    protected $tokenService;

    public function __construct(VotingTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Mostrar interfaz para generar links de votación
     */
    public function index(Eleccion $eleccion)
    {
        // Solo para elecciones activas o programadas
        if (!in_array($eleccion->estado, ['activa', 'programada'])) {
            return redirect()
                ->route('elecciones.index')
                ->with('error', 'Solo se pueden generar links para elecciones activas o programadas');
        }

        // Cargar candidatos de la elección con sus relaciones
        $eleccion->load(['candidatos.miembro', 'candidatos.cargo']);

        $usuarios = User::orderBy('name')->get();
        
        return view('elecciones.generar-links', compact('eleccion', 'usuarios'));
    }

    /**
     * Generar link único para la elección (público o privado)
     */
    public function generarLinkEleccion(Request $request, Eleccion $eleccion)
    {
        $request->validate([
            'tipo' => 'required|in:publico,privado',
        ]);

        $esPublico = $request->tipo === 'publico';
        
        try {
            // Generar token único para la elección
            $tokenData = $this->tokenService->generarToken($eleccion->id, null, $esPublico);
            $token = $tokenData['token']; // Extraer el token del array
            
            $url = route('voting.mostrar') . '?token=' . urlencode($token);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'tipo' => $esPublico ? 'Público' : 'Privado',
                'eleccion' => $eleccion->titulo,
                'candidatos_count' => $eleccion->candidatos->count(),
                'expires_at' => \Carbon\Carbon::parse($tokenData['expires_at'])->format('d/m/Y H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generando link de elección', [
                'eleccion_id' => $eleccion->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar token de votación para un usuario específico
     */
    public function generarToken(Request $request, Eleccion $eleccion)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;
        
        // Verificar que el usuario no haya votado ya
        if ($eleccion->usuarioYaVoto($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario ya ha votado en esta elección'
            ], 400);
        }

        // Generar hash anónimo del votante
        $voterHash = VotingTokenService::generarVoterHash($userId, $eleccion->id);

        // Generar token
        $tokenData = $this->tokenService->generarToken($voterHash, $eleccion->id);

        // Construir URL completa
        $votingUrl = url("/vote?token={$tokenData['token']}");

        Log::info('Link de votación generado', [
            'eleccion_id' => $eleccion->id,
            'jti' => $tokenData['jti'],
            'expires_at' => $tokenData['expires_at'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Link de votación generado exitosamente',
            'data' => [
                'url' => $votingUrl,
                'token' => $tokenData['token'],
                'expires_at' => $tokenData['expires_at'],
                'ttl_seconds' => $tokenData['ttl_seconds'],
                'jti' => $tokenData['jti'],
            ]
        ]);
    }

    /**
     * Generar tokens masivos para múltiples usuarios
     */
    public function generarTokensMasivos(Request $request, Eleccion $eleccion)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'link_publico' => 'nullable|boolean',
        ]);

        $linkPublico = $request->link_publico ?? false;
        $links = [];
        $errores = [];

        foreach ($request->user_ids as $userId) {
            try {
                // Verificar que el usuario no haya votado (solo para links privados)
                if (!$linkPublico && $eleccion->usuarioYaVoto($userId)) {
                    $errores[] = [
                        'user_id' => $userId,
                        'message' => 'Usuario ya ha votado'
                    ];
                    continue;
                }

                $voterHash = VotingTokenService::generarVoterHash($userId, $eleccion->id);
                $tokenData = $this->tokenService->generarToken($voterHash, $eleccion->id, $linkPublico);
                
                $usuario = User::find($userId);
                
                $links[] = [
                    'user_id' => $userId,
                    'name' => $usuario->name,
                    'email' => $usuario->email,
                    'url' => url("/vote?token={$tokenData['token']}"),
                    'expires_at' => $tokenData['expires_at'],
                    'tipo' => $linkPublico ? 'publico' : 'privado',
                ];

            } catch (\Exception $e) {
                $errores[] = [
                    'user_id' => $userId,
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($links) . ' links generados exitosamente',
            'data' => [
                'links' => $links,
                'errores' => $errores,
            ]
        ]);
    }

    /**
     * Mostrar interfaz de votación con token
     */
    public function mostrarVotacion(Request $request)
    {
        \Log::info('Acceso a mostrarVotacion', [
            'url' => $request->fullUrl(),
            'query' => $request->query(),
            'method' => $request->method()
        ]);

        $token = $request->query('token');
        
        // Si el token viene como array (token[token]), extraer el valor
        if (is_array($token)) {
            $token = $token['token'] ?? null;
        }

        \Log::info('Token procesado', [
            'token' => $token ? substr($token, 0, 20) . '...' : null,
            'token_type' => gettype($token)
        ]);

        if (!$token) {
            \Log::warning('Token no proporcionado');
            abort(400, 'Token de votación no proporcionado');
        }

        // Verificar token
        $payload = $this->tokenService->verificarToken($token);

        if (!$payload) {
            return view('elecciones.token-invalido', [
                'mensaje' => 'El token de votación es inválido o ha expirado'
            ]);
        }

        // Obtener elección
        $eleccion = Eleccion::with('candidatos.miembro', 'candidatos.cargo')
            ->findOrFail($payload['ballot_id']);

        // Verificar que la elección esté activa
        if (!$eleccion->estaActiva()) {
            return view('elecciones.token-invalido', [
                'mensaje' => 'La elección no está activa en este momento'
            ]);
        }

        return view('elecciones.votar-con-token', compact('eleccion', 'token', 'payload'));
    }

    /**
     * Registrar voto usando token
     */
    public function registrarVoto(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'candidato_id' => 'required|exists:candidatos,id',
        ]);

        $token = $request->token;
        
        // Si el token viene como array (token[token]), extraer el valor
        if (is_array($token)) {
            $token = $token['token'] ?? null;
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de votación no proporcionado'
            ], 400);
        }

        // Verificar token
        $payload = $this->tokenService->verificarToken($token);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ], 401);
        }

        $jti = $payload['jti'];
        $eleccionId = $payload['ballot_id'];
        $candidatoId = $request->candidato_id;

        // Verificar que el candidato pertenece a la elección
        $candidato = \App\Models\Candidato::where('id', $candidatoId)
            ->where('eleccion_id', $eleccionId)
            ->first();

        if (!$candidato) {
            return response()->json([
                'success' => false,
                'message' => 'El candidato no pertenece a esta elección'
            ], 400);
        }

        // Generar hash único para el voto
        $voterHash = \App\Services\VotingTokenService::generarVoterHash(
            $payload['voter_hash'] ?? 'anonymous', 
            $eleccionId
        );

        // Crear el voto
        $voto = \App\Models\Voto::create([
            'user_id' => $payload['voter_hash'] ?? 1, // Usar voter_hash como user_id temporal
            'eleccion_id' => $eleccionId,
            'candidato_id' => $candidatoId,
            'hash' => hash('sha256', $voterHash . $eleccionId . $candidatoId . now()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Actualizar contador de votos del candidato
        $candidato->increment('votos_recibidos');

        // Marcar token como usado (si está en cache)
        try {
            \Cache::forget("voting_token:{$jti}");
        } catch (\Exception $e) {
            Log::warning('No se pudo marcar token como usado', ['jti' => $jti]);
        }

        Log::info('Voto registrado exitosamente', [
            'voto_id' => $voto->id,
            'eleccion_id' => $eleccionId,
            'candidato_id' => $candidatoId,
            'voter_hash' => substr($voterHash, 0, 8) . '...',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Voto registrado exitosamente',
            'vote_hash' => $voto->hash,
            'candidato' => $candidato->miembro->nombre_completo ?? 'N/A',
        ]);
    }
}
