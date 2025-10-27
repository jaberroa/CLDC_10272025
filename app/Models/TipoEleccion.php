<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TipoEleccion extends Model
{
    use HasFactory;

    protected $table = 'tipos_elecciones';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'color',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Generar slug automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tipoEleccion) {
            if (empty($tipoEleccion->slug)) {
                $tipoEleccion->slug = Str::slug($tipoEleccion->nombre);
            }
        });

        static::updating(function ($tipoEleccion) {
            if ($tipoEleccion->isDirty('nombre') && empty($tipoEleccion->slug)) {
                $tipoEleccion->slug = Str::slug($tipoEleccion->nombre);
            }
        });
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc')->orderBy('nombre', 'asc');
    }

    // Relaciones
    public function elecciones()
    {
        return $this->hasMany(Eleccion::class, 'tipo', 'slug');
    }
}
