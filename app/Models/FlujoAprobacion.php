<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlujoAprobacion extends Model
{
    use HasFactory;

    protected $table = 'flujos_aprobacion';

    protected $fillable = [
        'seccion_id', 'nombre', 'descripcion', 'tipo', 'min_aprobadores',
        'requiere_todos', 'permite_delegar', 'dias_respuesta',
        'escalar_no_respuesta', 'escalacion_usuarios', 'activo', 'creado_por'
    ];

    protected $casts = [
        'requiere_todos' => 'boolean',
        'permite_delegar' => 'boolean',
        'escalar_no_respuesta' => 'boolean',
        'escalacion_usuarios' => 'array',
        'activo' => 'boolean',
    ];

    public function seccion()
    {
        return $this->belongsTo(SeccionDocumental::class, 'seccion_id');
    }

    public function aprobadores()
    {
        return $this->hasMany(AprobadorFlujo::class, 'flujo_id')->orderBy('orden');
    }

    public function aprobaciones()
    {
        return $this->hasMany(AprobacionDocumento::class, 'flujo_id');
    }
}
