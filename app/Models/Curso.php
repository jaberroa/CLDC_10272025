<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizacion_id',
        'titulo',
        'descripcion',
        'tipo',
        'modalidad',
        'fecha_inicio',
        'fecha_fin',
        'capacidad_maxima',
        'lugar',
        'estado',
        'costo',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'costo' => 'decimal:2',
    ];

    /**
     * Relación con organización
     */
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    /**
     * Relación con inscripciones
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class);
    }

    /**
     * Relación con miembros inscritos
     */
    public function miembrosInscritos(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class)->with('miembro');
    }

    /**
     * Scope para cursos por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para cursos activos
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['programada', 'en_curso']);
    }

    /**
     * Scope para cursos por modalidad
     */
    public function scopePorModalidad($query, $modalidad)
    {
        return $query->where('modalidad', $modalidad);
    }

    /**
     * Scope para cursos por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para cursos disponibles (con cupos)
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'programada')
                    ->whereRaw('capacidad_maxima > (SELECT COUNT(*) FROM inscripciones_cursos WHERE curso_id = cursos.id)');
    }

    /**
     * Obtener cupos disponibles
     */
    public function getCuposDisponiblesAttribute()
    {
        $inscritos = $this->inscripciones()->count();
        return max(0, $this->capacidad_maxima - $inscritos);
    }

    /**
     * Verificar si hay cupos disponibles
     */
    public function tieneCuposDisponibles()
    {
        return $this->cupos_disponibles > 0;
    }

    /**
     * Obtener porcentaje de ocupación
     */
    public function getPorcentajeOcupacionAttribute()
    {
        if ($this->capacidad_maxima == 0) {
            return 0;
        }
        
        $inscritos = $this->inscripciones()->count();
        return round(($inscritos / $this->capacidad_maxima) * 100, 2);
    }

    /**
     * Obtener estadísticas del curso
     */
    public function getEstadisticasAttribute()
    {
        $inscripciones = $this->inscripciones()->get();
        
        return [
            'total_inscritos' => $inscripciones->count(),
            'asistieron' => $inscripciones->where('estado', 'asistio')->count(),
            'completaron' => $inscripciones->where('estado', 'completo')->count(),
            'ausentes' => $inscripciones->where('estado', 'ausente')->count(),
            'porcentaje_ocupacion' => $this->porcentaje_ocupacion,
            'cupos_disponibles' => $this->cupos_disponibles,
            'recaudacion_total' => $inscripciones->sum('monto_pagado'),
        ];
    }

    /**
     * Inscribir miembro al curso
     */
    public function inscribirMiembro($miembroId, $montoPagado = 0, $observaciones = null)
    {
        if (!$this->tieneCuposDisponibles()) {
            throw new \Exception('No hay cupos disponibles para este curso');
        }

        if ($this->estado !== 'programada') {
            throw new \Exception('El curso no está disponible para inscripciones');
        }

        return $this->inscripciones()->create([
            'miembro_id' => $miembroId,
            'estado' => 'inscrito',
            'fecha_inscripcion' => now(),
            'monto_pagado' => $montoPagado,
            'observaciones' => $observaciones,
        ]);
    }

    /**
     * Marcar asistencia de miembro
     */
    public function marcarAsistencia($miembroId)
    {
        $inscripcion = $this->inscripciones()->where('miembro_id', $miembroId)->first();
        
        if (!$inscripcion) {
            throw new \Exception('El miembro no está inscrito en este curso');
        }

        $inscripcion->update(['estado' => 'asistio']);
        return $inscripcion;
    }

    /**
     * Marcar curso como completado por miembro
     */
    public function marcarCompletado($miembroId)
    {
        $inscripcion = $this->inscripciones()->where('miembro_id', $miembroId)->first();
        
        if (!$inscripcion) {
            throw new \Exception('El miembro no está inscrito en este curso');
        }

        $inscripcion->update(['estado' => 'completo']);
        return $inscripcion;
    }
}

