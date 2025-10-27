<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'modalidad',
        'lugar',
        'enlace_virtual',
        'cupo_maximo',
        'costo',
        'instructor',
        'contenido',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'costo' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorModalidad($query, $modalidad)
    {
        return $query->where('modalidad', $modalidad);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('fecha_inicio', '>', now());
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getInscripcionesCountAttribute()
    {
        return $this->inscripciones()->count();
    }

    public function getCuposDisponiblesAttribute()
    {
        return $this->cupo_maximo - $this->inscripciones_count;
    }

    public function getEsVirtualAttribute()
    {
        return $this->modalidad === 'virtual';
    }

    public function getEsPresencialAttribute()
    {
        return $this->modalidad === 'presencial';
    }

    public function getEsHibridaAttribute()
    {
        return $this->modalidad === 'hibrida';
    }
}