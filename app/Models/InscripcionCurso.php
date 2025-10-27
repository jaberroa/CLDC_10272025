<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionCurso extends Model
{
    use HasFactory;

    protected $table = 'inscripcion_cursos';

    protected $fillable = [
        'miembro_id',
        'curso_id',
        'fecha_inscripcion',
        'estado',
        'calificacion',
        'certificado_url',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
        'calificacion' => 'decimal:2'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeInscritos($query)
    {
        return $query->where('estado', 'inscrito');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEsInscritoAttribute()
    {
        return $this->estado === 'inscrito';
    }

    public function getEsCompletadoAttribute()
    {
        return $this->estado === 'completado';
    }

    public function getEsCanceladoAttribute()
    {
        return $this->estado === 'cancelado';
    }

    public function getTieneCertificadoAttribute()
    {
        return !empty($this->certificado_url);
    }
}