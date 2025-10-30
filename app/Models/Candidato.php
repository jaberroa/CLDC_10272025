<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidato extends Model
{
    use HasFactory;

    protected $table = 'candidatos';
    protected $connection = 'pgsql';

    protected $fillable = [
        'eleccion_id',
        'miembro_id',
        'cargo_id',
        'nombre',
        'cargo',
        'biografia',
        'propuestas',
        'foto',
        'orden',
        'activo',
    ];

    protected $casts = [
        'propuestas' => 'array',
        'activo' => 'boolean',
    ];

    /**
     * Relación con Elección
     */
    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class, 'eleccion_id');
    }

    /**
     * Relación con Miembro
     */
    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'miembro_id');
    }

    /**
     * Relación con Cargo
     */
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    /**
     * Relación con Votos
     */
    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'candidato_id');
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
    public function scopePorCargo($query, $cargo)
    {
        return $query->where('cargo', $cargo);
    }

    /**
     * Scope ordenados
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    /**
     * Obtener total de votos
     */
    public function getTotalVotosAttribute()
    {
        return $this->votos()->count();
    }

    /**
     * Obtener porcentaje de votos
     */
    public function getPorcentajeVotosAttribute()
    {
        $totalVotosEleccion = $this->eleccion->votos()->count();
        
        if ($totalVotosEleccion === 0) {
            return 0;
        }
        
        return round(($this->total_votos / $totalVotosEleccion) * 100, 2);
    }
}
