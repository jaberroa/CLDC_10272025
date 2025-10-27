<?php

namespace App\Http\Controllers;

use App\Models\TipoEleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TipoEleccionController extends Controller
{
    public function index()
    {
        try {
            $tipos = TipoEleccion::ordenados()->paginate(15);
        } catch (\Exception $e) {
            // Si la tabla no existe, mostrar valores por defecto
            $tipos = collect([
                (object)[
                    'id' => 1,
                    'nombre' => 'Junta Directiva',
                    'slug' => 'directiva',
                    'descripcion' => 'Elección de miembros de la Junta Directiva',
                    'icono' => 'ri-team-line',
                    'color' => 'primary',
                    'activo' => true,
                    'orden' => 1,
                ],
                (object)[
                    'id' => 2,
                    'nombre' => 'Comisión',
                    'slug' => 'comision',
                    'descripcion' => 'Elección de miembros de comisiones especializadas',
                    'icono' => 'ri-group-line',
                    'color' => 'success',
                    'activo' => true,
                    'orden' => 2,
                ],
                (object)[
                    'id' => 3,
                    'nombre' => 'Especial',
                    'slug' => 'especial',
                    'descripcion' => 'Elecciones especiales o extraordinarias',
                    'icono' => 'ri-star-line',
                    'color' => 'warning',
                    'activo' => true,
                    'orden' => 3,
                ],
            ]);
            
            // Crear un paginador manual
            $page = request()->get('page', 1);
            $perPage = 15;
            $tipos = new \Illuminate\Pagination\LengthAwarePaginator(
                $tipos->forPage($page, $perPage),
                $tipos->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
        
        return view('tipos-elecciones.index', compact('tipos'));
    }

    public function create()
    {
        return view('tipos-elecciones.create');
    }

    public function show(TipoEleccion $tipoEleccion)
    {
        $tipoEleccion->load('elecciones.organizacion');
        return view('tipos-elecciones.show', compact('tipoEleccion'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100|unique:tipos_elecciones,nombre',
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'activo' => 'nullable|boolean',
                'orden' => 'nullable|integer|min:0',
            ]);

            $validated['slug'] = Str::slug($validated['nombre']);
            $validated['activo'] = $request->has('activo') ? true : false;
            $validated['icono'] = $validated['icono'] ?? 'ri-checkbox-circle-line';
            $validated['color'] = $validated['color'] ?? 'primary';
            $validated['orden'] = $validated['orden'] ?? 0;

            $tipo = TipoEleccion::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de elección creado exitosamente',
                    'tipo' => $tipo
                ]);
            }

            return redirect()
                ->route('tipos-elecciones.index')
                ->with('success', 'Tipo de elección creado exitosamente');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear tipo de elección: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear tipo de elección: ' . $e->getMessage());
        }
    }

    public function edit(TipoEleccion $tipoEleccion)
    {
        return view('tipos-elecciones.edit', compact('tipoEleccion'));
    }

    public function update(Request $request, TipoEleccion $tipoEleccion)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100|unique:tipos_elecciones,nombre,' . $tipoEleccion->id,
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'activo' => 'nullable|boolean',
                'orden' => 'nullable|integer|min:0',
            ]);

            $validated['slug'] = Str::slug($validated['nombre']);
            $validated['activo'] = $request->has('activo') ? true : false;

            $tipoEleccion->update($validated);

            return redirect()
                ->route('tipos-elecciones.index')
                ->with('success', 'Tipo de elección actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar tipo de elección: ' . $e->getMessage());
        }
    }

    public function destroy(TipoEleccion $tipoEleccion)
    {
        try {
            // Verificar si hay elecciones asociadas
            if ($tipoEleccion->elecciones()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este tipo porque tiene elecciones asociadas'
                ], 400);
            }

            $tipoEleccion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de elección eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar tipo de elección: ' . $e->getMessage()
            ], 500);
        }
    }
}
