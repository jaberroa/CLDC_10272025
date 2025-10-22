<?php

namespace App\Http\Controllers;

use App\Models\CarnetTemplate;
use App\Models\CarnetPersonalizado;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;

class CarnetController extends Controller
{
    /**
     * Mostrar selector de plantillas de carnet
     */
    public function selector($miembroId): View
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])->findOrFail($miembroId);
        $templates = CarnetTemplate::activos()->ordenados()->get();
        $personalizado = CarnetPersonalizado::where('miembro_id', $miembroId)->first();

        // Si no existe personalización, crear una por defecto
        if (!$personalizado) {
            $personalizado = new CarnetPersonalizado([
                'miembro_id' => $miembroId,
                'template_id' => $templates->first()->id ?? 1,
                'color_primario' => '#008080',
                'color_secundario' => '#20B2AA',
                'color_fondo' => '#ffffff',
                'color_texto' => '#000000',
                'fuente_familia' => 'Arial, sans-serif',
                'tamaño_nombre' => 18,
                'tamaño_profesion' => 14,
                'tamaño_organizacion' => 12,
                'nombre_negrita' => true,
                'nombre_cursiva' => false,
                'profesion_negrita' => false,
                'profesion_cursiva' => false,
                'datos_personalizados' => [
                    'nombre' => $miembro->nombre_completo,
                    'profesion' => $miembro->profesion,
                    'organizacion' => $miembro->organizacion->nombre ?? 'CLDCI Nacional',
                    'numero_carnet' => $miembro->numero_carnet
                ],
                'activo' => true
            ]);
        }

        return view('carnet.selector', compact('miembro', 'templates', 'personalizado'));
    }

    /**
     * Mostrar editor de carnet
     */
    public function editor($miembroId, $templateId): View
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])->findOrFail($miembroId);
        $template = CarnetTemplate::findOrFail($templateId);
        $personalizado = CarnetPersonalizado::where('miembro_id', $miembroId)
                                          ->where('template_id', $templateId)
                                          ->first();

        // Si no existe personalización, crear una por defecto
        if (!$personalizado) {
            $personalizado = new CarnetPersonalizado([
                'miembro_id' => $miembroId,
                'template_id' => $templateId,
                'color_primario' => '#008080',
                'color_secundario' => '#20B2AA',
                'color_fondo' => '#ffffff',
                'color_texto' => '#000000',
                'fuente_familia' => 'Arial, sans-serif',
                'tamaño_nombre' => 18,
                'tamaño_profesion' => 14,
                'tamaño_organizacion' => 12,
                'nombre_negrita' => true,
                'nombre_cursiva' => false,
                'profesion_negrita' => false,
                'profesion_cursiva' => false,
                'datos_personalizados' => [
                    'nombre' => $miembro->nombre_completo,
                    'profesion' => $miembro->profesion,
                    'organizacion' => $miembro->organizacion->nombre ?? 'CLDCI Nacional',
                    'numero_carnet' => $miembro->numero_carnet
                ],
                'activo' => true
            ]);
        }

        return view('carnet.editor', compact('miembro', 'template', 'personalizado'));
    }

    /**
     * Guardar personalización de carnet
     */
    public function guardarPersonalizacion(Request $request, $miembroId): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:carnet_templates,id',
            'color_primario' => 'required|string',
            'color_secundario' => 'required|string',
            'color_fondo' => 'required|string',
            'color_texto' => 'required|string',
            'fuente_familia' => 'required|string',
            'tamaño_nombre' => 'required|integer|min:8|max:48',
            'tamaño_profesion' => 'required|integer|min:6|max:24',
            'tamaño_organizacion' => 'required|integer|min:6|max:20',
            'nombre_negrita' => 'boolean',
            'nombre_cursiva' => 'boolean',
            'profesion_negrita' => 'boolean',
            'profesion_cursiva' => 'boolean',
            'datos_personalizados' => 'nullable|json'
        ]);

        $personalizacion = CarnetPersonalizado::updateOrCreate(
            [
                'miembro_id' => $miembroId,
                'template_id' => $request->template_id
            ],
            [
                'color_primario' => $request->color_primario,
                'color_secundario' => $request->color_secundario,
                'color_fondo' => $request->color_fondo,
                'color_texto' => $request->color_texto,
                'fuente_familia' => $request->fuente_familia,
                'tamaño_nombre' => $request->tamaño_nombre,
                'tamaño_profesion' => $request->tamaño_profesion,
                'tamaño_organizacion' => $request->tamaño_organizacion,
                'nombre_negrita' => $request->boolean('nombre_negrita'),
                'nombre_cursiva' => $request->boolean('nombre_cursiva'),
                'profesion_negrita' => $request->boolean('profesion_negrita'),
                'profesion_cursiva' => $request->boolean('profesion_cursiva'),
                'datos_personalizados' => $request->datos_personalizados ? json_decode($request->datos_personalizados, true) : null,
                'activo' => true
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Personalización guardada correctamente',
            'personalizacion' => $personalizacion
        ]);
    }

    /**
     * Generar carnet final
     */
    public function generar($miembroId, $templateId): View
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])->findOrFail($miembroId);
        $template = CarnetTemplate::findOrFail($templateId);
        $personalizado = CarnetPersonalizado::where('miembro_id', $miembroId)
                                          ->where('template_id', $templateId)
                                          ->first();

        // Si no existe personalización, crear una por defecto
        if (!$personalizado) {
            $personalizado = new CarnetPersonalizado([
                'miembro_id' => $miembroId,
                'template_id' => $templateId,
                'color_primario' => '#008080',
                'color_secundario' => '#20B2AA',
                'color_fondo' => '#ffffff',
                'color_texto' => '#000000',
                'fuente_familia' => 'Arial, sans-serif',
                'tamaño_nombre' => 18,
                'tamaño_profesion' => 14,
                'tamaño_organizacion' => 12,
                'nombre_negrita' => true,
                'nombre_cursiva' => false,
                'profesion_negrita' => false,
                'profesion_cursiva' => false,
                'datos_personalizados' => [
                    'nombre' => $miembro->nombre_completo,
                    'profesion' => $miembro->profesion,
                    'organizacion' => $miembro->organizacion->nombre ?? 'CLDCI Nacional',
                    'numero_carnet' => $miembro->numero_carnet
                ],
                'activo' => true
            ]);
        }

        return view($template->template_path, compact('miembro', 'template', 'personalizado'));
    }

    /**
     * Obtener plantillas disponibles (API)
     */
    public function getTemplates(): JsonResponse
    {
        $templates = CarnetTemplate::activos()->ordenados()->get();
        
        return response()->json($templates);
    }

    /**
     * Obtener datos de plantilla específica (API)
     */
    public function getTemplate($templateId): JsonResponse
    {
        $template = CarnetTemplate::findOrFail($templateId);
        
        return response()->json($template);
    }

    /**
     * Obtener personalización de carnet (API)
     */
    public function getPersonalizacion($miembroId, $templateId): JsonResponse
    {
        $personalizacion = CarnetPersonalizado::where('miembro_id', $miembroId)
                                             ->where('template_id', $templateId)
                                             ->first();
        
        if (!$personalizacion) {
            // Crear personalización por defecto
            $template = CarnetTemplate::findOrFail($templateId);
            $configDefault = $template->configuracion_default ?? [];
            
            $personalizacion = new CarnetPersonalizado([
                'miembro_id' => $miembroId,
                'template_id' => $templateId,
                'color_primario' => $configDefault['color_primario'] ?? '#667eea',
                'color_secundario' => $configDefault['color_secundario'] ?? '#764ba2',
                'color_fondo' => $configDefault['color_fondo'] ?? '#ffffff',
                'color_texto' => $configDefault['color_texto'] ?? '#000000',
                'fuente_familia' => $configDefault['fuente_familia'] ?? 'Arial, sans-serif',
                'tamaño_nombre' => $configDefault['tamaño_nombre'] ?? 18,
                'tamaño_profesion' => $configDefault['tamaño_profesion'] ?? 14,
                'tamaño_organizacion' => $configDefault['tamaño_organizacion'] ?? 12,
                'nombre_negrita' => $configDefault['nombre_negrita'] ?? true,
                'nombre_cursiva' => $configDefault['nombre_cursiva'] ?? false,
                'profesion_negrita' => $configDefault['profesion_negrita'] ?? false,
                'profesion_cursiva' => $configDefault['profesion_cursiva'] ?? false,
                'activo' => true
            ]);
        }
        
        return response()->json($personalizacion);
    }

    /**
     * Subir foto de miembro
     */
    public function subirFoto(Request $request, $miembroId): JsonResponse
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $miembro = Miembro::findOrFail($miembroId);
        
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'miembro_' . $miembroId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/miembros/fotos', $filename);
            
            $miembro->foto_url = 'storage/miembros/fotos/' . $filename;
            $miembro->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto subida correctamente',
                'foto_url' => asset($miembro->foto_url)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error subiendo foto'
        ], 400);
    }

    /**
     * Generar PDF del carnet
     */
    public function generarPDF($miembroId, $templateId): Response
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])->findOrFail($miembroId);
        $template = CarnetTemplate::findOrFail($templateId);
        $personalizado = CarnetPersonalizado::where('miembro_id', $miembroId)
                                          ->where('template_id', $templateId)
                                          ->first();

        // Si no existe personalización, crear una por defecto
        if (!$personalizado) {
            $personalizado = new CarnetPersonalizado([
                'miembro_id' => $miembroId,
                'template_id' => $templateId,
                'color_primario' => '#008080',
                'color_secundario' => '#20B2AA',
                'color_fondo' => '#ffffff',
                'color_texto' => '#000000',
                'fuente_familia' => 'Arial, sans-serif',
                'tamaño_nombre' => 18,
                'tamaño_profesion' => 14,
                'tamaño_organizacion' => 12,
                'nombre_negrita' => true,
                'nombre_cursiva' => false,
                'profesion_negrita' => false,
                'profesion_cursiva' => false,
                'datos_personalizados' => [
                    'nombre' => $miembro->nombre_completo,
                    'profesion' => $miembro->profesion,
                    'organizacion' => $miembro->organizacion->nombre ?? 'CLDCI Nacional',
                    'numero_carnet' => $miembro->numero_carnet
                ],
                'activo' => true
            ]);
        }

        // Generar HTML del carnet
        $html = view($template->template_path, compact('miembro', 'template', 'personalizado'))->render();
        
        // Aquí se integraría con una librería de PDF como DomPDF o similar
        // Por ahora retornamos el HTML para que el JavaScript maneje la generación
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Obtener datos del miembro para carnet (API)
     */
    public function getMiembroData($miembroId): JsonResponse
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])->findOrFail($miembroId);
        
        return response()->json([
            'id' => $miembro->id,
            'nombre_completo' => $miembro->nombre_completo,
            'numero_carnet' => $miembro->numero_carnet,
            'profesion' => $miembro->profesion,
            'organizacion' => $miembro->organizacion->nombre ?? 'CLDCI Nacional',
            'tipo_membresia' => $miembro->estadoMembresia->nombre ?? 'Activa',
            'fecha_ingreso' => $miembro->fecha_ingreso->format('Y'),
            'valido_hasta' => $miembro->fecha_ingreso->addYears(2)->format('Y'),
            'foto_url' => $miembro->foto_url ? asset($miembro->foto_url) : null,
        ]);
    }
}