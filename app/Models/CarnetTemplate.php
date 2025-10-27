<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarnetTemplate extends Model
{
    use HasFactory;

    protected $table = 'carnet_templates';

    protected $fillable = [
        'nombre',
        'descripcion',
        'template_html',
        'template_css',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function personalizados(): HasMany
    {
        return $this->hasMany(CarnetPersonalizado::class, 'template_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getPersonalizacionesCountAttribute()
    {
        return $this->personalizados()->count();
    }
}