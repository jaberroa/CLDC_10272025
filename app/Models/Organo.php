<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organo extends Model
{
    use HasFactory;

    protected $table = 'organos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'nivel',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public function miembrosDirectivos(): HasMany
    {
        return $this->hasMany(MiembroDirectivo::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}


