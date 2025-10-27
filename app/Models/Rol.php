<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'color',
        'nivel',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'nivel' => 'integer',
    ];

    /**
     * Permisos asociados al rol
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'rol_id', 'permiso_id')
            ->withTimestamps();
    }

    /**
     * Usuarios con este rol
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_rol', 'rol_id', 'user_id')
            ->withPivot('asignado_en', 'asignado_por')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenadosPorNivel($query)
    {
        return $query->orderBy('nivel', 'desc');
    }

    /**
     * Verifica si el rol tiene un permiso
     */
    public function tienePermiso(string $permisoSlug): bool
    {
        return $this->permisos()->where('slug', $permisoSlug)->exists();
    }

    /**
     * Asignar múltiples permisos
     */
    public function asignarPermisos(array $permisosIds): void
    {
        $this->permisos()->sync($permisosIds, false);
    }

    /**
     * Remover permisos
     */
    public function removerPermisos(array $permisosIds): void
    {
        $this->permisos()->detach($permisosIds);
    }

    /**
     * Sincronizar permisos (reemplaza todos)
     */
    public function sincronizarPermisos(array $permisosIds): void
    {
        $this->permisos()->sync($permisosIds);
    }
}
