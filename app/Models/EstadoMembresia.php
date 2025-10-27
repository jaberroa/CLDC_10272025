<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoMembresia extends Model
{
    use HasFactory;

    protected $table = 'estados_membresia';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->whereIn('nombre', ['activa', 'activo']);
    }

    public function scopeInactivos($query)
    {
        return $query->whereIn('nombre', ['inactiva', 'inactivo', 'suspendida', 'suspendido']);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEsActivoAttribute()
    {
        return in_array(strtolower($this->nombre), ['activa', 'activo']);
    }

    public function getEsInactivoAttribute()
    {
        return in_array(strtolower($this->nombre), ['inactiva', 'inactivo', 'suspendida', 'suspendido']);
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function getEstadosActivos()
    {
        return self::activos()->get();
    }

    public static function getEstadosInactivos()
    {
        return self::inactivos()->get();
    }
}