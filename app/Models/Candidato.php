<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidato extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleccion_id',
        'miembro_id',
        'cargo_id',
        'propuesta',
        'biografia',
        'foto_campana',
        'votos_recibidos',
        'activo',
    ];

    protected $casts = [
        'votos_recibidos' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación con elección
     */
    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class);
    }

    /**
     * Relación con miembro
     */
    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    /**
     * Relación con cargo
     */
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    /**
     * Relación con votos
     */
    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }

    /**
     * Scope para candidatos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope por cargo
     */
    public function scopePorCargo($query, $cargoId)
    {
        return $query->where('cargo_id', $cargoId);
    }

    /**
     * Obtener porcentaje de votos
     */
    public function getPorcentajeVotosAttribute()
    {
        $totalVotos = $this->eleccion->votos_totales;
        
        if ($totalVotos == 0) {
            return 0;
        }
        
        return round(($this->votos_recibidos / $totalVotos) * 100, 2);
    }
}
