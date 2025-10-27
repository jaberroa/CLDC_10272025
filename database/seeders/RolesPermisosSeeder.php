<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Permisos
        $permisos = [
            // Miembros
            ['nombre' => 'Ver Miembros', 'slug' => 'miembros.ver', 'modulo' => 'miembros', 'categoria' => 'lectura'],
            ['nombre' => 'Crear Miembros', 'slug' => 'miembros.crear', 'modulo' => 'miembros', 'categoria' => 'escritura'],
            ['nombre' => 'Editar Miembros', 'slug' => 'miembros.editar', 'modulo' => 'miembros', 'categoria' => 'escritura'],
            ['nombre' => 'Eliminar Miembros', 'slug' => 'miembros.eliminar', 'modulo' => 'miembros', 'categoria' => 'escritura'],
            
            // Cuotas
            ['nombre' => 'Ver Cuotas', 'slug' => 'cuotas.ver', 'modulo' => 'cuotas', 'categoria' => 'lectura'],
            ['nombre' => 'Crear Cuotas', 'slug' => 'cuotas.crear', 'modulo' => 'cuotas', 'categoria' => 'escritura'],
            ['nombre' => 'Editar Cuotas', 'slug' => 'cuotas.editar', 'modulo' => 'cuotas', 'categoria' => 'escritura'],
            ['nombre' => 'Eliminar Cuotas', 'slug' => 'cuotas.eliminar', 'modulo' => 'cuotas', 'categoria' => 'escritura'],
            
            // Documentos
            ['nombre' => 'Ver Documentos', 'slug' => 'documentos.ver', 'modulo' => 'documentos', 'categoria' => 'lectura'],
            ['nombre' => 'Crear Documentos', 'slug' => 'documentos.crear', 'modulo' => 'documentos', 'categoria' => 'escritura'],
            ['nombre' => 'Editar Documentos', 'slug' => 'documentos.editar', 'modulo' => 'documentos', 'categoria' => 'escritura'],
            ['nombre' => 'Eliminar Documentos', 'slug' => 'documentos.eliminar', 'modulo' => 'documentos', 'categoria' => 'escritura'],
            ['nombre' => 'Compartir Documentos', 'slug' => 'documentos.compartir', 'modulo' => 'documentos', 'categoria' => 'especial'],
            ['nombre' => 'Aprobar Documentos', 'slug' => 'documentos.aprobar', 'modulo' => 'documentos', 'categoria' => 'especial'],
            
            // Directiva
            ['nombre' => 'Ver Directiva', 'slug' => 'directiva.ver', 'modulo' => 'directiva', 'categoria' => 'lectura'],
            ['nombre' => 'Gestionar Directiva', 'slug' => 'directiva.gestionar', 'modulo' => 'directiva', 'categoria' => 'escritura'],
            
            // Asambleas
            ['nombre' => 'Ver Asambleas', 'slug' => 'asambleas.ver', 'modulo' => 'asambleas', 'categoria' => 'lectura'],
            ['nombre' => 'Crear Asambleas', 'slug' => 'asambleas.crear', 'modulo' => 'asambleas', 'categoria' => 'escritura'],
            
            // Finanzas
            ['nombre' => 'Ver Finanzas', 'slug' => 'finanzas.ver', 'modulo' => 'finanzas', 'categoria' => 'lectura'],
            ['nombre' => 'Gestionar Finanzas', 'slug' => 'finanzas.gestionar', 'modulo' => 'finanzas', 'categoria' => 'escritura'],
            
            // Sistema
            ['nombre' => 'Ver Roles', 'slug' => 'roles.ver', 'modulo' => 'sistema', 'categoria' => 'lectura'],
            ['nombre' => 'Gestionar Roles', 'slug' => 'roles.gestionar', 'modulo' => 'sistema', 'categoria' => 'escritura'],
            ['nombre' => 'Ver Configuración', 'slug' => 'configuracion.ver', 'modulo' => 'sistema', 'categoria' => 'lectura'],
            ['nombre' => 'Gestionar Configuración', 'slug' => 'configuracion.gestionar', 'modulo' => 'sistema', 'categoria' => 'escritura'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::create($permiso);
        }

        // Crear Roles
        $roles = [
            [
                'nombre' => 'Super Administrador',
                'slug' => 'superadmin',
                'descripcion' => 'Acceso total al sistema',
                'color' => '#dc3545',
                'nivel' => 100,
                'permisos' => Permiso::all()->pluck('id')->toArray(), // Todos los permisos
            ],
            [
                'nombre' => 'Administrador',
                'slug' => 'administrador',
                'descripcion' => 'Gestión completa del sistema',
                'color' => '#fd7e14',
                'nivel' => 90,
                'permisos' => Permiso::whereIn('slug', [
                    'miembros.ver', 'miembros.crear', 'miembros.editar', 'miembros.eliminar',
                    'cuotas.ver', 'cuotas.crear', 'cuotas.editar', 'cuotas.eliminar',
                    'documentos.ver', 'documentos.crear', 'documentos.editar', 'documentos.eliminar',
                    'documentos.compartir', 'documentos.aprobar',
                    'directiva.ver', 'directiva.gestionar',
                    'asambleas.ver', 'asambleas.crear',
                    'finanzas.ver', 'finanzas.gestionar',
                    'roles.ver',
                ])->pluck('id')->toArray(),
            ],
            [
                'nombre' => 'Coordinador',
                'slug' => 'coordinador',
                'descripcion' => 'Coordinación y aprobaciones',
                'color' => '#0d6efd',
                'nivel' => 70,
                'permisos' => Permiso::whereIn('slug', [
                    'miembros.ver', 'miembros.crear', 'miembros.editar',
                    'cuotas.ver', 'cuotas.crear', 'cuotas.editar',
                    'documentos.ver', 'documentos.crear', 'documentos.editar',
                    'documentos.compartir', 'documentos.aprobar',
                    'directiva.ver',
                    'asambleas.ver', 'asambleas.crear',
                ])->pluck('id')->toArray(),
            ],
            [
                'nombre' => 'Secretario',
                'slug' => 'secretario',
                'descripcion' => 'Gestión administrativa',
                'color' => '#198754',
                'nivel' => 60,
                'permisos' => Permiso::whereIn('slug', [
                    'miembros.ver', 'miembros.crear', 'miembros.editar',
                    'cuotas.ver', 'cuotas.crear', 'cuotas.editar',
                    'documentos.ver', 'documentos.crear', 'documentos.editar',
                    'documentos.compartir',
                    'directiva.ver',
                    'asambleas.ver',
                ])->pluck('id')->toArray(),
            ],
            [
                'nombre' => 'Tesorero',
                'slug' => 'tesorero',
                'descripcion' => 'Gestión financiera',
                'color' => '#ffc107',
                'nivel' => 50,
                'permisos' => Permiso::whereIn('slug', [
                    'miembros.ver',
                    'cuotas.ver', 'cuotas.crear', 'cuotas.editar',
                    'documentos.ver', 'documentos.crear',
                    'finanzas.ver', 'finanzas.gestionar',
                ])->pluck('id')->toArray(),
            ],
            [
                'nombre' => 'Miembro',
                'slug' => 'miembro',
                'descripcion' => 'Usuario regular',
                'color' => '#6c757d',
                'nivel' => 10,
                'permisos' => Permiso::whereIn('slug', [
                    'miembros.ver',
                    'documentos.ver',
                    'directiva.ver',
                    'asambleas.ver',
                ])->pluck('id')->toArray(),
            ],
        ];

        foreach ($roles as $rolData) {
            $permisos = $rolData['permisos'];
            unset($rolData['permisos']);
            
            $rol = Rol::create($rolData);
            $rol->permisos()->attach($permisos);
        }
    }
}
