<?php

namespace App\Services;

use App\Models\VotingToken;
use App\Models\Eleccion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VotingTokenService
{
    private const TTL_MINUTES = 120; // 120 minutos (2 horas) de validez
    private const SIGNING_ALGORITHM = 'sha256';
    
    /**
     * Generar token de votación de un solo uso
     */
    public function generarToken($voterHash, $eleccionId, $publico = false): array
    {
        $jti = (string) Str::uuid();
        $iat = now();
        $exp = now()->addMinutes(self::TTL_MINUTES);

        $payload = [
            'jti' => $jti,
            'voter_hash' => $voterHash,
            'ballot_id' => $eleccionId,
            'iat' => $iat->timestamp,
            'exp' => $exp->timestamp,
            'typ' => 'voting_token',
            'public' => $publico, // Agregar flag de público
        ];

        // Generar token JWT firmado
        $token = $this->crearJWT($payload);
        
        // TODO: Persistir en DB cuando la migración esté ejecutada
        /*
        $votingToken = VotingToken::create([
            'jti' => $jti,
            'voter_hash' => $voterHash,
            'eleccion_id' => $eleccionId,
            'token_signature' => $this->firmarToken($token),
            'used' => false,
            'issued_at' => $iat,
            'expires_at' => $exp,
        ]);
        */

        // Cachear en Redis/Cache con TTL
        try {
            Cache::put(
                "voting_token:{$jti}", 
                [
                    'used' => false, 
                    'payload' => $payload,
                    'token_signature' => $this->firmarToken($token),
                    'voter_hash' => $voterHash,
                    'eleccion_id' => $eleccionId,
                ],
                self::TTL_MINUTES * 60
            );
        } catch (\Exception $e) {
            // Si falla el cache, continuar (el token JWT aún funciona)
            Log::warning('No se pudo cachear el token', ['error' => $e->getMessage()]);
        }

        Log::info('Token de votación generado', [
            'jti' => $jti,
            'voter_hash' => substr($voterHash, 0, 8) . '...',
            'eleccion_id' => $eleccionId,
            'expires_at' => $exp->toIso8601String(),
        ]);

        return [
            'token' => $token,
            'jti' => $jti,
            'expires_at' => $exp->toIso8601String(),
            'ttl_seconds' => self::TTL_MINUTES * 60,
        ];
    }

    /**
     * Verificar y decodificar token
     */
    public function verificarToken(string $token): ?array
    {
        try {
            // Decodificar JWT
            $payload = $this->decodificarJWT($token);
            
            if (!$payload) {
                Log::warning('Payload nulo al decodificar JWT');
                return null;
            }

            $jti = $payload['jti'] ?? null;
            if (!$jti) {
                Log::warning('JTI no encontrado en payload');
                return null;
            }

            // DEBUG: Log de timestamps
            $now = time();
            $exp = $payload['exp'] ?? 0;
            $iat = $payload['iat'] ?? 0;
            
            Log::info('Verificación de token', [
                'jti' => substr($jti, 0, 8),
                'iat' => date('Y-m-d H:i:s', $iat),
                'exp' => date('Y-m-d H:i:s', $exp),
                'now' => date('Y-m-d H:i:s', $now),
                'is_expired' => ($exp < $now),
                'time_remaining' => ($exp - $now) . ' segundos',
            ]);

            // Verificar en caché primero (más rápido)
            $cached = Cache::get("voting_token:{$jti}");
            if ($cached && isset($cached['used']) && $cached['used']) {
                Log::warning('Intento de reusar token (detectado en cache)', [
                    'jti' => $jti,
                    'ip' => request()->ip(),
                ]);
                return null;
            }

            // TODO: Verificar en DB cuando la tabla esté disponible
            /*
            $dbToken = VotingToken::find($jti);
            
            if (!$dbToken) {
                Log::warning('Token no encontrado en DB', ['jti' => $jti]);
                return null;
            }

            if (!$dbToken->esValido()) {
                Log::warning('Token inválido o expirado', [
                    'jti' => $jti,
                    'used' => $dbToken->used,
                    'expires_at' => $dbToken->expires_at,
                ]);
                return null;
            }

            // Verificar firma
            if ($dbToken->token_signature !== $this->firmarToken($token)) {
                Log::error('Token con firma inválida', ['jti' => $jti]);
                return null;
            }
            */
            
            // Validar expiración
            if ($exp < $now) {
                Log::warning('Token expirado', [
                    'jti' => $jti,
                    'expired_since' => ($now - $exp) . ' segundos',
                ]);
                return null;
            }

            Log::info('Token válido', ['jti' => substr($jti, 0, 8)]);
            return $payload;

        } catch (\Exception $e) {
            Log::error('Error al verificar token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Marcar token como usado (operación atómica)
     */
    public function marcarComoUsado(string $jti, int $votoId): bool
    {
        try {
            // Intentar marcar en Cache primero
            $redisKey = "voting_token:{$jti}";
            
            // Usar lock solo si está disponible
            try {
                $lock = Cache::lock("voting_token_lock:{$jti}", 5);
                $locked = $lock->get(function () use ($redisKey, $jti, $votoId) {
                    
                    $cached = Cache::get($redisKey);
                    if (!$cached || (isset($cached['used']) && $cached['used'])) {
                        return false;
                    }

                    // Marcar como usado en Cache
                    Cache::put($redisKey, array_merge($cached, ['used' => true]), 3600);

                    // TODO: Marcar en DB cuando esté disponible
                    /*
                    $updated = VotingToken::marcarComoUsado(
                        $jti,
                        $votoId,
                        request()->ip(),
                        request()->userAgent()
                    );

                    if (!$updated) {
                        // Rollback en Cache si falla en DB
                        Cache::put($redisKey, $cached, 3600);
                        return false;
                    }
                    */

                    return true;
                });
            } catch (\Exception $e) {
                // Si falla el lock, intentar marcar directamente
                $cached = Cache::get($redisKey);
                if (!$cached || (isset($cached['used']) && $cached['used'])) {
                    return false;
                }
                Cache::put($redisKey, array_merge($cached, ['used' => true]), 3600);
                $locked = true;
            }

            if ($locked) {
                Log::info('Token marcado como usado exitosamente', [
                    'jti' => $jti,
                    'voto_id' => $votoId,
                ]);
            } else {
                Log::warning('Intento de usar token ya usado o bloqueado', [
                    'jti' => $jti,
                ]);
            }

            return $locked;
            
        } catch (\Exception $e) {
            Log::error('Error al marcar token como usado', [
                'jti' => $jti,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Crear JWT manualmente (sin dependencias externas)
     */
    private function crearJWT(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
            'kid' => $this->getActiveKeyId(),
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac(
            self::SIGNING_ALGORITHM,
            "{$headerEncoded}.{$payloadEncoded}",
            $this->getSigningKey(),
            true
        );
        
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return "{$headerEncoded}.{$payloadEncoded}.{$signatureEncoded}";
    }

    /**
     * Decodificar JWT
     */
    private function decodificarJWT(string $token): ?array
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verificar firma
        $expectedSignature = hash_hmac(
            self::SIGNING_ALGORITHM,
            "{$headerEncoded}.{$payloadEncoded}",
            $this->getSigningKey(),
            true
        );

        $expectedSignatureEncoded = $this->base64UrlEncode($expectedSignature);

        if (!hash_equals($expectedSignatureEncoded, $signatureEncoded)) {
            Log::warning('Firma JWT inválida');
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        // NO verificar expiración aquí, eso se hace en verificarToken()
        // para poder loggear el JTI

        return $payload;
    }

    /**
     * Firmar token para almacenar en DB
     */
    private function firmarToken(string $token): string
    {
        return hash_hmac(self::SIGNING_ALGORITHM, $token, $this->getSigningKey());
    }

    /**
     * Obtener clave de firma activa
     */
    private function getSigningKey(): string
    {
        // En producción, obtener de tabla signing_keys
        // Por ahora usar APP_KEY + salt
        return config('app.key') . ':voting_token_v1';
    }

    /**
     * Obtener ID de clave activa
     */
    private function getActiveKeyId(): string
    {
        return 'key_v1_' . date('Y_m');
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Generar hash de votante (anonimizado)
     */
    public static function generarVoterHash($userId, $eleccionId): string
    {
        $salt = config('app.key') . ':voter_hash';
        return hash('sha256', "{$userId}|{$eleccionId}|{$salt}");
    }

    /**
     * Limpiar tokens expirados
     */
    public function limpiarTokensExpirados(): int
    {
        $deleted = VotingToken::where('expires_at', '<', now()->subDays(7))->delete();
        
        Log::info("Tokens expirados limpiados: {$deleted}");
        
        return $deleted;
    }
}

