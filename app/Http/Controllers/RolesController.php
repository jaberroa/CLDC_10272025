<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Rol::withCount(['usuarios', 'permisos'])
            ->ordenadosPorNivel()
            ->paginate(20);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permiso::orderBy('modulo')->orderBy('nombre')->get()->groupBy('modulo');
        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre',
            'slug' => 'required|string|max:100|unique:roles,slug',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'nivel' => 'required|integer|min:0|max:100',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $rol = Rol::create([
            'nombre' => $validated['nombre'],
            'slug' => $validated['slug'],
            'descripcion' => $validated['descripcion'] ?? null,
            'color' => $validated['color'] ?? '#0d6efd',
            'nivel' => $validated['nivel'],
            'activo' => true,
        ]);

        if (!empty($validated['permisos'])) {
            $rol->permisos()->attach($validated['permisos']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente');
    }

    public function show(Rol $rol)
    {
        $rol->load(['permisos', 'usuarios']);
        return view('roles.show', compact('rol'));
    }

    public function edit(Rol $rol)
    {
        $rol->load('permisos');
        $permisos = Permiso::orderBy('modulo')->orderBy('nombre')->get()->groupBy('modulo');
        $permisosAsignados = $rol->permisos->pluck('id')->toArray();

        return view('roles.edit', compact('rol', 'permisos', 'permisosAsignados'));
    }

    public function update(Request $request, Rol $rol)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,' . $rol->id,
            'slug' => 'required|string|max:100|unique:roles,slug,' . $rol->id,
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'nivel' => 'required|integer|min:0|max:100',
            'activo' => 'boolean',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $rol->update([
            'nombre' => $validated['nombre'],
            'slug' => $validated['slug'],
            'descripcion' => $validated['descripcion'] ?? null,
            'color' => $validated['color'] ?? $rol->color,
            'nivel' => $validated['nivel'],
            'activo' => $validated['activo'] ?? true,
        ]);

        $rol->permisos()->sync($validated['permisos'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy(Rol $rol)
    {
        if ($rol->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un rol con usuarios asignados');
        }

        $rol->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente');
    }
}
