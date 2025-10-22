<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarnetTemplate;

class CarnetTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'nombre' => 'QR Test - Template de Prueba',
                'descripcion' => 'Template simple para probar generación de QR',
                'template_path' => 'carnet.templates.qr-test',
                'preview_image' => 'assets/images/carnet-preview-qr-test.png',
                'configuracion_default' => [
                    'color_primario' => '#008080',
                    'color_secundario' => '#20B2AA',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'fuente_familia' => 'Arial, sans-serif',
                    'tamaño_nombre' => 18,
                    'tamaño_profesion' => 14,
                    'tamaño_organizacion' => 12
                ],
                'activo' => true,
                'orden' => 1
            ],
            [
                'nombre' => 'Modelo 8 - Enchazbado Destacado',
                'descripcion' => 'Diseño con dos secciones: teal superior y blanca inferior',
                'template_path' => 'carnet.templates.modelo8',
                'preview_image' => 'assets/images/carnet-preview-modelo8.png',
                'configuracion_default' => [
                    'color_primario' => '#008080',
                    'color_secundario' => '#ffffff',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'fuente_familia' => 'Arial, sans-serif',
                    'tamaño_nombre' => 20,
                    'tamaño_profesion' => 14,
                    'tamaño_organizacion' => 12
                ],
                'activo' => true,
                'orden' => 1
            ],
            [
                'nombre' => 'Modelo 11 - Profesional con Degradado',
                'descripcion' => 'Diseño con degradado suave de azul a púrpura',
                'template_path' => 'carnet.templates.modelo11',
                'preview_image' => 'assets/images/carnet-preview-modelo11.png',
                'configuracion_default' => [
                    'color_primario' => '#4facfe',
                    'color_secundario' => '#00f2fe',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'fuente_familia' => 'Arial, sans-serif',
                    'tamaño_nombre' => 18,
                    'tamaño_profesion' => 14,
                    'tamaño_organizacion' => 12
                ],
                'activo' => true,
                'orden' => 2
            ],
            [
                'nombre' => 'Modelo 13 - Barra Superior de Acento',
                'descripcion' => 'Diseño con barra superior teal y logo corporativo',
                'template_path' => 'carnet.templates.modelo13',
                'preview_image' => 'assets/images/carnet-preview-modelo13.png',
                'configuracion_default' => [
                    'color_primario' => '#008080',
                    'color_secundario' => '#ffffff',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'fuente_familia' => 'Arial, sans-serif',
                    'tamaño_nombre' => 18,
                    'tamaño_profesion' => 14,
                    'tamaño_organizacion' => 12
                ],
                'activo' => true,
                'orden' => 3
            ],
            [
                'nombre' => 'Modelo 15 - Patrón Geométrico',
                'descripcion' => 'Diseño con patrón geométrico sutil y sombra elegante',
                'template_path' => 'carnet.templates.modelo15',
                'preview_image' => 'assets/images/carnet-preview-modelo15.png',
                'configuracion_default' => [
                    'color_primario' => '#667eea',
                    'color_secundario' => '#764ba2',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'fuente_familia' => 'Arial, sans-serif',
                    'tamaño_nombre' => 18,
                    'tamaño_profesion' => 14,
                    'tamaño_organizacion' => 12
                ],
                'activo' => true,
                'orden' => 4
            ],
            [
                'nombre' => 'Modelo Clásico - Tradicional',
                'descripcion' => 'Diseño clásico tradicional con logo grande',
                'template_path' => 'carnet.templates.clasico',
                'preview_image' => 'assets/images/carnet-preview-clasico.png',
                'configuracion_default' => [
                    'color_primario' => '#667eea',
                    'color_secundario' => '#764ba2',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'fuente_familia' => 'Arial, sans-serif',
                    'tamaño_nombre' => 16,
                    'tamaño_profesion' => 14,
                    'tamaño_organizacion' => 12
                ],
                'activo' => true,
                'orden' => 5
            ]
        ];

        foreach ($templates as $template) {
            CarnetTemplate::create($template);
        }
    }
}