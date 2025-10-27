<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Eleccion extends Model
{
    use HasFactory;

    protected $table = 'elecciones';

    protected $fillable = [
        'organizacion_id',
        'titulo',
        'descripcion',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'votos_totales',
        'votacion_abierta',
        'created_by',
        'quorum_requerido',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'votacion_abierta' => 'boolean',
    ];

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'eleccion_id');
    }

    public function candidatos(): HasMany
    {
        return $this->hasMany(Candidato::class, 'eleccion_id');
    }

    /**
     * Verificar si la elección está activa
     */
    public function estaActiva(): bool
    {
        $now = now();
        $inicio = $this->start_at ?? $this->fecha_inicio;
        $fin = $this->end_at ?? $this->fecha_fin;
        
        if (!$inicio || !$fin) {
            return $this->estado === 'activa';
        }
        
        return $now->between($inicio, $fin) && $this->estado === 'activa';
    }

    /**
     * Verificar si la elección ha finalizado
     */
    public function haFinalizado(): bool
    {
        $fin = $this->end_at ?? $this->fecha_fin;
        
        if (!$fin) {
            return $this->estado === 'finalizada';
        }
        
        return now()->greaterThan($fin);
    }

    /**
     * Verificar si un usuario ya votó
     * Por ahora retorna false hasta que las migraciones estén ejecutadas
     */
    public function usuarioYaVoto($userId): bool
    {
        // TODO: Implementar cuando la tabla voting_tokens esté disponible
        // Por ahora, permitir múltiples tokens para testing
        return false;
        
        /* 
        // Código real (descomentar después de migrar):
        $voterHash = \App\Services\VotingTokenService::generarVoterHash($userId, $this->id);
        return \App\Models\VotingToken::where('eleccion_id', $this->id)
            ->where('voter_hash', $voterHash)
            ->where('used', true)
            ->exists();
        */
    }

    /**
     * Obtener resultados de la elección
     */
    public function obtenerResultados()
    {
        return $this->candidatos()
            ->withCount('votos')
            ->orderBy('votos_count', 'desc')
            ->get()
            ->map(function ($candidato) {
                $totalVotos = $this->votos()->count();
                $porcentaje = $totalVotos > 0 ? round(($candidato->votos_count / $totalVotos) * 100, 2) : 0;
                
                return [
                    'id' => $candidato->id,
                    'nombre' => $candidato->nombre,
                    'cargo' => $candidato->cargo,
                    'votos' => $candidato->votos_count,
                    'porcentaje' => $porcentaje,
                ];
            });
    }

    /**
     * Scope para elecciones activas
     */
    public function scopeActivas($query)
    {
        $now = now();
        return $query->where('estado', 'activa')
            ->where(function ($q) use ($now) {
                $q->where(function ($subQ) use ($now) {
                    $subQ->whereNotNull('start_at')
                        ->whereNotNull('end_at')
                        ->where('start_at', '<=', $now)
                        ->where('end_at', '>=', $now);
                })->orWhere(function ($subQ) {
                    $subQ->whereNull('start_at')
                        ->whereNull('end_at');
                });
            });
    }

    /**
     * Scope para elecciones programadas
     */
    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programada')
            ->where(function ($q) {
                $q->where('start_at', '>', now())
                    ->orWhere('fecha_inicio', '>', now());
            });
    }
}