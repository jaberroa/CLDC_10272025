<?php

namespace App\Http\Controllers;

use App\Models\Eleccion;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EleccionAdminController extends Controller
{
    public function create()
    {
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $cargos = \App\Models\Cargo::activos()->orderBy('nombre')->get();
        $miembros = \App\Models\Miembro::activos()->orderBy('nombre_completo')->get();
        
        // Intentar cargar tipos desde BD, si falla usar valores por defecto
        try {
            $tiposElecciones = \App\Models\TipoEleccion::activos()->ordenados()->get();
        } catch (\Exception $e) {
            // Usar valores estáticos si la tabla no existe
            $tiposElecciones = collect([
                (object)['slug' => 'directiva', 'nombre' => 'Junta Directiva', 'icono' => 'ri-team-line', 'color' => 'primary'],
                (object)['slug' => 'comision', 'nombre' => 'Comisión', 'icono' => 'ri-group-line', 'color' => 'success'],
                (object)['slug' => 'especial', 'nombre' => 'Especial', 'icono' => 'ri-star-line', 'color' => 'warning'],
            ]);
        }
        
        return view('elecciones.create', compact('organizaciones', 'cargos', 'miembros', 'tiposElecciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:directiva,comision,especial',
            'organizacion_id' => 'required|exists:organizaciones,id',
            'fecha_inicio_votacion' => 'required|date',
            'hora_inicio' => 'required',
            'duracion_minutos' => 'required|integer|min:15',
            'estado_activo' => 'nullable|boolean',
            'candidatos' => 'required|array|min:2',
            'candidatos.*.miembro_id' => 'required|exists:miembros,id',
            'candidatos.*.cargo_id' => 'required|exists:cargos,id',
            'candidatos.*.nombre' => 'required|string',
            'candidatos.*.propuestas' => 'nullable|string',
            'candidatos.*.orden' => 'required|integer',
        ]);

        // Combinar fecha y hora de inicio
        $fechaHoraInicio = $validated['fecha_inicio_votacion'] . ' ' . $validated['hora_inicio'];
        
        // Calcular fecha y hora de fin
        $inicio = \Carbon\Carbon::parse($fechaHoraInicio);
        $duracionMinutos = (int) $validated['duracion_minutos'];
        $fin = $inicio->copy()->addMinutes($duracionMinutos);

        // Solo usar campos que existen en la tabla actual
        $data = [
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'tipo' => $validated['tipo'],
            'organizacion_id' => $validated['organizacion_id'],
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'created_by' => auth()->id(),
            'quorum_requerido' => 50,
        ];

        $eleccion = Eleccion::create($data);

        // Crear candidatos
        foreach ($validated['candidatos'] as $candidatoData) {
            $miembro = \App\Models\Miembro::find($candidatoData['miembro_id']);
            $cargo = \App\Models\Cargo::find($candidatoData['cargo_id']);
            
            // Preparar datos del candidato
            $candidatoInfo = [
                'eleccion_id' => $eleccion->id,
                'miembro_id' => $candidatoData['miembro_id'],
                'cargo_id' => $candidatoData['cargo_id'],
            ];
            
            // Agregar campos opcionales si existen en la tabla
            if (\Schema::hasColumn('candidatos', 'nombre')) {
                $candidatoInfo['nombre'] = $candidatoData['nombre'];
            }
            if (\Schema::hasColumn('candidatos', 'cargo')) {
                $candidatoInfo['cargo'] = $cargo->nombre;
            }
            if (\Schema::hasColumn('candidatos', 'propuestas')) {
                $candidatoInfo['propuestas'] = $candidatoData['propuestas'] ?? null;
            }
            if (\Schema::hasColumn('candidatos', 'orden')) {
                $candidatoInfo['orden'] = $candidatoData['orden'];
            }
            if (\Schema::hasColumn('candidatos', 'activo')) {
                $candidatoInfo['activo'] = true;
            }
            
            \App\Models\Candidato::create($candidatoInfo);
        }

        return redirect()
            ->route('elecciones.index')
            ->with('success', 'Elección creada exitosamente con ' . count($validated['candidatos']) . ' candidatos.');
    }

    public function edit(Eleccion $eleccion)
    {
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $cargos = \App\Models\Cargo::activos()->orderBy('nombre')->get();
        $miembros = \App\Models\Miembro::activos()->orderBy('nombre_completo')->get();
        
        // Intentar cargar tipos desde BD, si falla usar valores por defecto
        try {
            $tiposElecciones = \App\Models\TipoEleccion::activos()->ordenados()->get();
        } catch (\Exception $e) {
            // Usar valores estáticos si la tabla no existe
            $tiposElecciones = collect([
                (object)['slug' => 'directiva', 'nombre' => 'Junta Directiva', 'icono' => 'ri-team-line', 'color' => 'primary'],
                (object)['slug' => 'comision', 'nombre' => 'Comisión', 'icono' => 'ri-group-line', 'color' => 'success'],
                (object)['slug' => 'especial', 'nombre' => 'Especial', 'icono' => 'ri-star-line', 'color' => 'warning'],
            ]);
        }
        
        $eleccion->load(['candidatos', 'organizacion']);
        
        return view('elecciones.edit', compact('eleccion', 'organizaciones', 'cargos', 'miembros', 'tiposElecciones'));
    }

    public function update(Request $request, Eleccion $eleccion)
    {
        try {
            \Log::info('Iniciando actualización de elección', [
                'eleccion_id' => $eleccion->id,
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:directiva,comision,especial',
            'organizacion_id' => 'required|exists:organizaciones,id',
            'fecha_inicio_votacion' => 'required|date',
            'hora_inicio' => 'required',
            'duracion_minutos' => 'required|integer|min:15',
            'estado_activo' => 'nullable|boolean',
            'candidatos' => 'required|array|min:2',
            'candidatos.*.miembro_id' => 'required|exists:miembros,id',
            'candidatos.*.cargo_id' => 'required|exists:cargos,id',
            'candidatos.*.nombre' => 'required|string',
            'candidatos.*.propuestas' => 'nullable|string',
            'candidatos.*.orden' => 'required|integer',
        ]);

        // Combinar fecha y hora de inicio
        $fechaHoraInicio = $validated['fecha_inicio_votacion'] . ' ' . $validated['hora_inicio'];
        
        // Calcular fecha y hora de fin
        $inicio = \Carbon\Carbon::parse($fechaHoraInicio);
        $duracionMinutos = (int) $validated['duracion_minutos'];
        $fin = $inicio->copy()->addMinutes($duracionMinutos);

        // Actualizar datos de la elección
        $data = [
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'tipo' => $validated['tipo'],
            'organizacion_id' => $validated['organizacion_id'],
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
        ];

        $eleccion->update($data);

        // Eliminar candidatos existentes y crear los nuevos
        $eleccion->candidatos()->delete();

        // Crear nuevos candidatos
        foreach ($validated['candidatos'] as $candidatoData) {
            $miembro = \App\Models\Miembro::find($candidatoData['miembro_id']);
            $cargo = \App\Models\Cargo::find($candidatoData['cargo_id']);
            
            $candidatoInfo = [
                'eleccion_id' => $eleccion->id,
                'miembro_id' => $candidatoData['miembro_id'],
                'cargo_id' => $candidatoData['cargo_id'],
            ];
            
            if (\Schema::hasColumn('candidatos', 'nombre')) {
                $candidatoInfo['nombre'] = $candidatoData['nombre'];
            }
            if (\Schema::hasColumn('candidatos', 'cargo')) {
                $candidatoInfo['cargo'] = $cargo->nombre;
            }
            if (\Schema::hasColumn('candidatos', 'propuestas')) {
                $candidatoInfo['propuestas'] = $candidatoData['propuestas'] ?? null;
            }
            if (\Schema::hasColumn('candidatos', 'orden')) {
                $candidatoInfo['orden'] = $candidatoData['orden'];
            }
            if (\Schema::hasColumn('candidatos', 'activo')) {
                $candidatoInfo['activo'] = true;
            }
            
            \App\Models\Candidato::create($candidatoInfo);
        }

            \Log::info('Elección actualizada exitosamente', [
                'eleccion_id' => $eleccion->id,
                'candidatos_count' => count($validated['candidatos'])
            ]);

            return redirect()
                ->route('elecciones.index')
                ->with('success', 'Elección actualizada exitosamente con ' . count($validated['candidatos']) . ' candidatos.');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación al actualizar elección', [
                'eleccion_id' => $eleccion->id,
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error al actualizar elección', [
                'eleccion_id' => $eleccion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la elección: ' . $e->getMessage());
        }
    }

    public function destroy(Eleccion $eleccion)
    {
        if ($eleccion->estado === 'activa') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una elección activa'
                ], 400);
            }
            return back()->with('error', 'No se puede eliminar una elección activa');
        }

        $eleccion->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Elección eliminada exitosamente'
            ]);
        }

        return redirect()
            ->route('elecciones.index')
            ->with('success', 'Elección eliminada exitosamente');
    }
}
