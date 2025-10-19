<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionCurso extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id',
        'miembro_id',
        'estado',
        'fecha_inscripcion',
        'monto_pagado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
        'monto_pagado' => 'decimal:2',
    ];

    /**
     * Relación con curso
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Relación con miembro
     */
    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    /**
     * Scope por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para inscripciones activas
     */
    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['inscrito', 'asistio', 'completo']);
    }
}
