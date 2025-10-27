<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDocumentalPermission
{
    /**
     * Maneja la solicitud entrante.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'No autenticado');
        }

        // Verificar si el usuario tiene el permiso
        if (!$this->hasPermission($user, $permission)) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }

        return $next($request);
    }

    /**
     * Verifica si el usuario tiene el permiso específico
     */
    protected function hasPermission($user, string $permission): bool
    {
        // Super admin siempre tiene acceso
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // Verificar permisos específicos
        switch ($permission) {
            case 'documentos.ver':
                return $this->canViewDocuments($user);
            
            case 'documentos.crear':
                return $this->canCreateDocuments($user);
            
            case 'documentos.editar':
                return $this->canEditDocuments($user);
            
            case 'documentos.eliminar':
                return $this->canDeleteDocuments($user);
            
            case 'documentos.compartir':
                return $this->canShareDocuments($user);
            
            case 'documentos.aprobar':
                return $this->canApproveDocuments($user);
            
            case 'secciones.gestionar':
                return $this->canManageSections($user);
            
            case 'carpetas.gestionar':
                return $this->canManageFolders($user);
            
            default:
                return false;
        }
    }

    /**
     * Verifica si es super administrador
     */
    protected function isSuperAdmin($user): bool
    {
        // Verificar roles del sistema
        // TEMPORAL: Permitir acceso si no tiene rol definido (desarrollo)
        if (!isset($user->rol) || empty($user->rol)) {
            return true; // Acceso temporal durante desarrollo
        }
        
        return $user->rol === 'superadmin' 
            || $user->rol === 'administrador'
            || $user->email === 'admin@cldci.com';
    }

    /**
     * Puede ver documentos
     */
    protected function canViewDocuments($user): bool
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
     * Puede crear documentos
     */
    protected function canCreateDocuments($user): bool
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
     * Puede editar documentos
     */
    protected function canEditDocuments($user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario'
        ]);
    }

    /**
     * Puede eliminar documentos
     */
    protected function canDeleteDocuments($user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador'
        ]);
    }

    /**
     * Puede compartir documentos
     */
    protected function canShareDocuments($user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario'
        ]);
    }

    /**
     * Puede aprobar documentos
     */
    protected function canApproveDocuments($user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador'
        ]);
    }

    /**
     * Puede gestionar secciones
     */
    protected function canManageSections($user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador'
        ]);
    }

    /**
     * Puede gestionar carpetas
     */
    protected function canManageFolders($user): bool
    {
        return in_array($user->rol, [
            'superadmin',
            'administrador',
            'coordinador',
            'secretario'
        ]);
    }
}
