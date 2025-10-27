<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Capacitacion extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'modalidad',
        'enlace_virtual',
        'cupo_maximo',
        'costo',
        'instructor',
        'contenido',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'costo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Usar ID auto-incrementable por defecto

    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionCapacitacion::class, 'curso_id');
    }
}