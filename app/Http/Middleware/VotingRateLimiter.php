<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VotingRateLimiter
{
    // Límites por acción
    private const LIMITS = [
        'token_request' => ['max' => 5, 'window' => 60], // 5 requests por minuto
        'vote_submit' => ['max' => 3, 'window' => 60],  // 3 votos por minuto
    ];

    public function handle(Request $request, Closure $next, string $action = 'default')
    {
        $ip = $request->ip();
        $limit = self::LIMITS[$action] ?? ['max' => 10, 'window' => 60];
        
        $cacheKey = "rate_limit:{$action}:{$ip}";
        
        // Verificar en caché (más rápido)
        $attempts = Cache::get($cacheKey, 0);
        
        if ($attempts >= $limit['max']) {
            Log::warning('Rate limit excedido', [
                'action' => $action,
                'ip' => $ip,
                'attempts' => $attempts,
            ]);

            // Registrar en DB para análisis
            DB::table('voting_rate_limits')->insert([
                'ip_address' => $ip,
                'action' => $action,
                'attempted_at' => now(),
                'success' => false,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Demasiados intentos. Por favor, espera un momento.',
                'retry_after' => $limit['window'],
            ], 429);
        }

        // Incrementar contador
        Cache::put($cacheKey, $attempts + 1, $limit['window']);

        // Registrar intento exitoso
        DB::table('voting_rate_limits')->insert([
            'ip_address' => $ip,
            'action' => $action,
            'attempted_at' => now(),
            'success' => true,
        ]);

        return $next($request);
    }
}
