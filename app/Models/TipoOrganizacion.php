<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoOrganizacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_organizacion';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function organizaciones(): HasMany
    {
        return $this->hasMany(Organizacion::class, 'tipo', 'nombre');
    }
}