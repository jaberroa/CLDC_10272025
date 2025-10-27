<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoMetadato extends Model
{
    use HasFactory;

    protected $table = 'campos_metadatos';

    protected $fillable = [
        'seccion_id',
        'nombre',
        'etiqueta',
        'tipo',
        'descripcion',
        'opciones',
        'requerido',
        'multiple',
        'valor_defecto',
        'placeholder',
        'validacion',
        'orden',
        'activo',
        'buscable',
        'visible_listado',
        'creado_por',
    ];

    protected $casts = [
        'opciones' => 'array',
        'requerido' => 'boolean',
        'multiple' => 'boolean',
        'activo' => 'boolean',
        'buscable' => 'boolean',
        'visible_listado' => 'boolean',
    ];

    public function seccion()
    {
        return $this->belongsTo(SeccionDocumental::class, 'seccion_id');
    }

    public function valores()
    {
        return $this->hasMany(ValorMetadato::class, 'campo_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function scopeBuscables($query)
    {
        return $query->where('buscable', true);
    }
}
