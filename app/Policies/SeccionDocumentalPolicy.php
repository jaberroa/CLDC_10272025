<?php

namespace App\Policies;

use App\Models\SeccionDocumental;
use App\Models\User;

class SeccionDocumentalPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Todos pueden ver la lista
    }

    public function view(User $user, SeccionDocumental $seccion): bool
    {
        return $seccion->activa || $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, SeccionDocumental $seccion): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, SeccionDocumental $seccion): bool
    {
        return $this->isAdmin($user);
    }

    protected function isAdmin(User $user): bool
    {
        return in_array($user->rol, ['superadmin', 'administrador']);
    }
}
