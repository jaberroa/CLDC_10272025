<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TipoMembresia extends Model
{
    use HasFactory;

    protected $table = 'tipos_membresia';
    protected $connection = 'pgsql';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'color',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Generar slug automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tipoMembresia) {
            if (empty($tipoMembresia->slug)) {
                $tipoMembresia->slug = Str::slug($tipoMembresia->nombre);
            }
        });

        static::updating(function ($tipoMembresia) {
            if ($tipoMembresia->isDirty('nombre') && empty($tipoMembresia->slug)) {
                $tipoMembresia->slug = Str::slug($tipoMembresia->nombre);
            }
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class, 'tipo_membresia');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }
}
