<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Verifica si el usuario tiene un permiso específico
     */
    public static function can(string $permission): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        return (new \App\Http\Middleware\CheckDocumentalPermission())
            ->hasPermission($user, $permission);
    }

    /**
     * Obtiene los roles con sus permisos
     */
    public static function getRolesPermissions(): array
    {
        return [
            'superadmin' => [
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.eliminar',
                'documentos.compartir',
                'documentos.aprobar',
                'secciones.gestionar',
                'carpetas.gestionar',
            ],
            'administrador' => [
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.eliminar',
                'documentos.compartir',
                'documentos.aprobar',
                'secciones.gestionar',
                'carpetas.gestionar',
            ],
            'coordinador' => [
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.compartir',
                'documentos.aprobar',
                'carpetas.gestionar',
            ],
            'secretario' => [
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.compartir',
                'carpetas.gestionar',
            ],
            'tesorero' => [
                'documentos.ver',
                'documentos.crear',
            ],
            'miembro' => [
                'documentos.ver',
            ],
        ];
    }

    /**
     * Obtiene los permisos de un rol específico
     */
    public static function getRolePermissions(string $rol): array
    {
        $permissions = self::getRolesPermissions();
        return $permissions[$rol] ?? [];
    }

    /**
     * Verifica si el usuario tiene acceso a un documento
     */
    public static function canAccessDocument($user, $documento): bool
    {
        // Super admin siempre puede
        if (in_array($user->rol, ['superadmin', 'administrador'])) {
            return true;
        }

        // Verificar nivel de acceso
        switch ($documento->nivel_acceso) {
            case 'publico':
                return true;
            
            case 'interno':
                return $user !== null;
            
            case 'confidencial':
                return in_array($user->rol, ['superadmin', 'administrador', 'coordinador', 'secretario']);
            
            case 'restringido':
                return $documento->subido_por === $user->id 
                    || in_array($user->rol, ['superadmin', 'administrador'])
                    || $documento->permisos()->where('usuario_id', $user->id)->exists();
            
            default:
                return false;
        }
    }
}

