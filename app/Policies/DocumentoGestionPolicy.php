<?php

namespace App\Policies;

use App\Models\DocumentoGestion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentoGestionPolicy
{
    /**
     * Determina si el usuario puede ver cualquier documento
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario',
            'tesorero',
            'miembro'
        ]);
    }

    /**
     * Determina si el usuario puede ver el documento
     */
    public function view(User $user, DocumentoGestion $documento): bool
    {
        // Super admin y admin pueden ver todo
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // Verificar nivel de acceso del documento
        switch ($documento->nivel_acceso) {
            case 'publico':
                return true;
            
            case 'interno':
                return $user !== null; // Cualquier usuario autenticado
            
            case 'confidencial':
                return $this->canAccessConfidential($user);
            
            case 'restringido':
                return $this->canAccessRestricted($user, $documento);
            
            default:
                return false;
        }
    }

    /**
     * Determina si el usuario puede crear documentos
     */
    public function create(User $user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario',
            'tesorero'
        ]);
    }

    /**
     * Determina si el usuario puede actualizar el documento
     */
    public function update(User $user, DocumentoGestion $documento): bool
    {
        // Super admin puede editar todo
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // El creador puede editar su documento si no está aprobado
        if ($documento->subido_por === $user->id && $documento->estado !== 'aprobado') {
            return true;
        }

        // Coordinadores y secretarios pueden editar
        return in_array($user->rol, ['coordinador', 'secretario']);
    }

    /**
     * Determina si el usuario puede eliminar el documento
     */
    public function delete(User $user, DocumentoGestion $documento): bool
    {
        // Solo super admin y admin
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // El creador puede eliminar si está en borrador
        if ($documento->subido_por === $user->id && $documento->estado === 'borrador') {
            return true;
        }

        return false;
    }

    /**
     * Determina si el usuario puede restaurar el documento
     */
    public function restore(User $user, DocumentoGestion $documento): bool
    {
        return $this->isSuperAdmin($user);
    }

    /**
     * Determina si el usuario puede forzar la eliminación
     */
    public function forceDelete(User $user, DocumentoGestion $documento): bool
    {
        return $this->isSuperAdmin($user);
    }

    /**
     * Determina si el usuario puede descargar el documento
     */
    public function download(User $user, DocumentoGestion $documento): bool
    {
        // Debe poder ver el documento
        if (!$this->view($user, $documento)) {
            return false;
        }

        // Verificar si el documento permite descargas
        if ($documento->comparticiones()->where('usuario_id', $user->id)->exists()) {
            $comparticion = $documento->comparticiones()
                ->where('usuario_id', $user->id)
                ->first();
            
            return $comparticion->puede_descargar;
        }

        return true;
    }

    /**
     * Determina si el usuario puede compartir el documento
     */
    public function share(User $user, DocumentoGestion $documento): bool
    {
        // Super admin puede compartir todo
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // El creador puede compartir sus documentos
        if ($documento->subido_por === $user->id) {
            return true;
        }

        // Coordinadores y secretarios pueden compartir
        return in_array($user->rol, ['coordinador', 'secretario']);
    }

    /**
     * Determina si el usuario puede aprobar el documento
     */
    public function approve(User $user, DocumentoGestion $documento): bool
    {
        // Solo ciertos roles pueden aprobar
        if (!in_array($user->rol, ['superadmin', 'administrador', 'coordinador'])) {
            return false;
        }

        // No puede aprobar sus propios documentos
        if ($documento->subido_por === $user->id) {
            return false;
        }

        // Verificar si tiene una aprobación pendiente asignada
        return $documento->aprobaciones()
            ->where('aprobador_id', $user->id)
            ->where('estado', 'pendiente')
            ->exists();
    }

    /**
     * Determina si el usuario puede mover el documento
     */
    public function move(User $user, DocumentoGestion $documento): bool
    {
        return $this->update($user, $documento);
    }

    /**
     * Determina si el usuario puede duplicar el documento
     */
    public function duplicate(User $user, DocumentoGestion $documento): bool
    {
        return $this->view($user, $documento) && $this->create($user);
    }

    /**
     * Verifica si el usuario puede acceder a documentos confidenciales
     */
    protected function canAccessConfidential(User $user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario'
        ]);
    }

    /**
     * Verifica si el usuario puede acceder a documentos restringidos
     */
    protected function canAccessRestricted(User $user, DocumentoGestion $documento): bool
    {
        // Super admin siempre
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // El creador puede ver
        if ($documento->subido_por === $user->id) {
            return true;
        }

        // Verificar permisos explícitos
        return $documento->permisos()
            ->where('usuario_id', $user->id)
            ->where('puede_ver', true)
            ->exists();
    }

    /**
     * Verifica si es super administrador
     */
    protected function isSuperAdmin(User $user): bool
    {
        return $user->rol === 'superadmin' || $user->rol === 'administrador';
    }
}
