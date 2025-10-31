<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use App\Models\EstadoMembresia;
use App\Models\TipoMembresia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SoporteMembresiasController extends Controller
{
    /**
     * Display a listing of estados and tipos de membresía.
     */
    public function index()
    {
        $estados = EstadoMembresia::orderBy('nombre')->get();
        $tipos = TipoMembresia::orderBy('nombre')->get();
        
        return view('soporte.membresias.index', compact('estados', 'tipos'));
    }

    // ========================================
    // ESTADOS DE MEMBRESÍA
    // ========================================

    public function storeEstado(Request $request)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.estados_membresia', 'nombre')
            ],
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        EstadoMembresia::create($validated);

        return redirect()->route('soporte.membresias.index')
            ->with('success', 'Estado de membresía creado exitosamente.');
    }

    public function updateEstado(Request $request, $id)
    {
        $estado = EstadoMembresia::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.estados_membresia', 'nombre')->ignore($id)
            ],
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $estado->update($validated);

        return redirect()->route('soporte.membresias.index')
            ->with('success', 'Estado de membresía actualizado exitosamente.');
    }

    public function destroyEstado($id)
    {
        $estado = EstadoMembresia::findOrFail($id);
        
        // Verificar si hay miembros usando este estado
        $miembrosCount = $estado->miembros()->count();
        
        if ($miembrosCount > 0) {
            return redirect()->route('soporte.membresias.index')
                ->with('error', 'No se puede eliminar el estado porque tiene ' . $miembrosCount . ' miembro(s) asociado(s).');
        }

        $estado->delete();

        return redirect()->route('soporte.membresias.index')
            ->with('success', 'Estado de membresía eliminado exitosamente.');
    }

    // ========================================
    // TIPOS DE MEMBRESÍA
    // ========================================

    public function storeTipo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.tipos_membresia', 'nombre')
            ],
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'activo' => 'boolean'
        ]);

        // Filtrar solo los campos que existen en la base de datos
        $columns = DB::connection('pgsql')->getSchemaBuilder()->getColumnListing('tipos_membresia');
        $dataToCreate = [];
        
        // Solo incluir campos que existen en la BD y están en validados
        foreach ($validated as $key => $value) {
            if (in_array($key, $columns)) {
                $dataToCreate[$key] = $value;
            }
        }
        
        TipoMembresia::create($dataToCreate);

        return redirect()->route('soporte.membresias.index')
            ->with('success', 'Tipo de membresía creado exitosamente.');
    }

    public function updateTipo(Request $request, $id)
    {
        $tipo = TipoMembresia::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.tipos_membresia', 'nombre')->ignore($id)
            ],
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'activo' => 'boolean'
        ]);

        // Filtrar solo los campos que existen en la base de datos
        $columns = DB::connection('pgsql')->getSchemaBuilder()->getColumnListing('tipos_membresia');
        $dataToUpdate = array_intersect_key($validated, array_flip($columns));
        
        $tipo->update($dataToUpdate);

        return redirect()->route('soporte.membresias.index')
            ->with('success', 'Tipo de membresía actualizado exitosamente.');
    }

    public function destroyTipo($id)
    {
        $tipo = TipoMembresia::findOrFail($id);
        
        // Verificar si hay miembros usando este tipo (por ID)
        $miembrosCount = DB::table('miembros')
            ->where('tipo_membresia', $tipo->id)
            ->count();
        
        if ($miembrosCount > 0) {
            return redirect()->route('soporte.membresias.index')
                ->with('error', 'No se puede eliminar el tipo porque tiene ' . $miembrosCount . ' miembro(s) asociado(s).');
        }

        $tipo->delete();

        return redirect()->route('soporte.membresias.index')
            ->with('success', 'Tipo de membresía eliminado exitosamente.');
    }
}
