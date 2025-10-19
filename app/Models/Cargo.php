<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel',
        'es_presidencia',
        'orden_prioridad',
    ];

    protected $casts = [
        'es_presidencia' => 'boolean',
        'orden_prioridad' => 'integer',
    ];

    /**
     * Relación con miembros directivos
     */
    public function miembrosDirectivos(): HasMany
    {
        return $this->hasMany(MiembroDirectivo::class);
    }

    /**
     * Relación con candidatos
     */
    public function candidatos(): HasMany
    {
        return $this->hasMany(Candidato::class);
    }

    /**
     * Scope para cargos presidenciales
     */
    public function scopePresidenciales($query)
    {
        return $query->where('es_presidencia', true);
    }

    /**
     * Scope por nivel
     */
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope ordenados por prioridad
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden_prioridad');
    }
}

