<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $connection = 'pgsql';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'modulo',
        'categoria',
    ];

    /**
     * Roles que tienen este permiso
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso', 'permiso_id', 'rol_id')
            ->withTimestamps();
    }

    /**
     * Usuarios con permiso directo
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_permiso', 'permiso_id', 'user_id')
            ->withPivot('concedido', 'valido_desde', 'valido_hasta', 'motivo')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopePorModulo($query, string $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    public function scopePorCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }
}
