<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Roles del usuario
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'user_id', 'rol_id')
            ->withPivot('asignado_en', 'asignado_por')
            ->withTimestamps();
    }

    /**
     * Permisos directos del usuario
     */
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'usuario_permiso', 'user_id', 'permiso_id')
            ->withPivot('concedido', 'valido_desde', 'valido_hasta', 'motivo')
            ->withTimestamps();
    }

    /**
     * Verifica si el usuario tiene un rol
     */
    public function tieneRol(string $rolSlug): bool
    {
        return $this->roles()->where('slug', $rolSlug)->exists();
    }

    /**
     * Verifica si el usuario tiene alguno de los roles
     */
    public function tieneAlgunRol(array $rolesSlug): bool
    {
        return $this->roles()->whereIn('slug', $rolesSlug)->exists();
    }

    /**
     * Verifica si el usuario tiene un permiso (por rol o directo)
     */
    public function tienePermiso(string $permisoSlug): bool
    {
        // Verificar permiso directo
        $permisoDirecto = $this->permisos()
            ->where('slug', $permisoSlug)
            ->wherePivot('concedido', true)
            ->exists();

        if ($permisoDirecto) {
            return true;
        }

        // Verificar por roles
        return $this->roles()
            ->whereHas('permisos', function ($query) use ($permisoSlug) {
                $query->where('slug', $permisoSlug);
            })
            ->exists();
    }

    /**
     * Asignar rol al usuario
     */
    public function asignarRol($rolId, $asignadoPor = null)
    {
        $this->roles()->attach($rolId, [
            'asignado_por' => $asignadoPor,
            'asignado_en' => now(),
        ]);
    }

    /**
     * Remover rol del usuario
     */
    public function removerRol($rolId)
    {
        $this->roles()->detach($rolId);
    }

    /**
     * Sincronizar roles (reemplaza todos)
     */
    public function sincronizarRoles(array $rolesIds)
    {
        $this->roles()->sync($rolesIds);
    }
}
