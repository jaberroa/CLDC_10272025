<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeccionDocumental;
use App\Models\CarpetaDocumental;

class SeccionesDocumentalesSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Secciones
        $contratos = SeccionDocumental::create([
            'nombre' => 'Contratos',
            'slug' => 'contratos',
            'descripcion' => 'Contratos de servicios y convenios',
            'icono' => 'ri-file-text-line',
            'color' => '#0d6efd',
            'orden' => 1,
            'activa' => true,
            'requiere_aprobacion' => true,
            'permite_versionado' => true,
            'creado_por' => 1,
        ]);

        $actas = SeccionDocumental::create([
            'nombre' => 'Actas',
            'slug' => 'actas',
            'descripcion' => 'Actas de asambleas y reuniones',
            'icono' => 'ri-file-list-line',
            'color' => '#198754',
            'orden' => 2,
            'activa' => true,
            'creado_por' => 1,
        ]);

        $administrativos = SeccionDocumental::create([
            'nombre' => 'Documentos Administrativos',
            'slug' => 'administrativos',
            'descripcion' => 'Documentos administrativos internos',
            'icono' => 'ri-briefcase-line',
            'color' => '#ffc107',
            'orden' => 3,
            'activa' => true,
            'creado_por' => 1,
        ]);

        $legales = SeccionDocumental::create([
            'nombre' => 'Documentos Legales',
            'slug' => 'legales',
            'descripcion' => 'Documentos con valor legal',
            'icono' => 'ri-scales-line',
            'color' => '#dc3545',
            'orden' => 4,
            'activa' => true,
            'requiere_aprobacion' => true,
            'creado_por' => 1,
        ]);

        // Crear algunas carpetas de ejemplo
        CarpetaDocumental::create([
            'seccion_id' => $contratos->id,
            'nombre' => 'Contratos de Clientes',
            'slug' => 'contratos-clientes',
            'descripcion' => 'Contratos con clientes externos',
            'nivel' => 1,
            'activa' => true,
            'publica' => false,
            'creado_por' => 1,
        ]);

        CarpetaDocumental::create([
            'seccion_id' => $actas->id,
            'nombre' => 'Actas de Asamblea 2025',
            'slug' => 'actas-asamblea-2025',
            'descripcion' => 'Actas del año 2025',
            'nivel' => 1,
            'activa' => true,
            'publica' => true,
            'creado_por' => 1,
        ]);

        CarpetaDocumental::create([
            'seccion_id' => $administrativos->id,
            'nombre' => 'Correspondencia',
            'slug' => 'correspondencia',
            'descripcion' => 'Correspondencia interna y externa',
            'nivel' => 1,
            'activa' => true,
            'publica' => false,
            'creado_por' => 1,
        ]);
    }
}
