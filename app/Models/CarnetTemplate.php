<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarnetTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'template_path',
        'preview_image',
        'configuracion_default',
        'activo',
        'orden'
    ];

    protected $casts = [
        'configuracion_default' => 'array',
        'activo' => 'boolean'
    ];

    public function personalizaciones()
    {
        return $this->hasMany(CarnetPersonalizado::class, 'template_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }
}