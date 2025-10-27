<?php

namespace App\Policies;

use App\Models\CarpetaDocumental;
use App\Models\User;

class CarpetaDocumentalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CarpetaDocumental $carpeta): bool
    {
        // Carpetas públicas
        if ($carpeta->publica) {
            return true;
        }

        // Admin puede ver todas
        if ($this->isAdmin($user)) {
            return true;
        }

        // El creador puede ver sus carpetas
        if ($carpeta->creado_por === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario'
        ]);
    }

    public function update(User $user, CarpetaDocumental $carpeta): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // El creador puede editar si no es de solo lectura
        return $carpeta->creado_por === $user->id && !$carpeta->solo_lectura;
    }

    public function delete(User $user, CarpetaDocumental $carpeta): bool
    {
        return $this->isAdmin($user) || $carpeta->creado_por === $user->id;
    }

    protected function isAdmin(User $user): bool
    {
        return in_array($user->rol, ['superadmin', 'administrador']);
    }
}
