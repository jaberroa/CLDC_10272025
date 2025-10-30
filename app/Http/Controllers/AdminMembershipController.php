<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EstadoMembresia;
use App\Models\Miembro;
use App\Models\CuotaMembresia;
use App\Models\TipoMembresia;

class AdminMembershipController extends Controller
{
    public function deleteEstado(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'confirm_name' => 'required|string',
            'force' => 'nullable|boolean',
        ]);

        $estado = EstadoMembresia::find($id);
        if (!$estado) {
            return response()->json(['success' => false, 'message' => 'Estado no encontrado'], 404);
        }

        if (strcasecmp($data['confirm_name'], $estado->nombre) !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'El nombre ingresado no coincide con el estado seleccionado.',
            ], 422);
        }

        // ¿Hay cuotas asociadas mediante miembros con este estado?
        $miembrosIds = Miembro::where('estado_membresia_id', $id)->pluck('id');
        $cuotasAsociadas = CuotaMembresia::whereIn('miembro_id', $miembrosIds)->exists();

        if ($cuotasAsociadas && !$request->boolean('force')) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: hay cuotas asociadas a miembros con este estado. Ponga en null primero o use force.'
            ], 422);
        }

        // Si force: poner en null el estado de los miembros afectados
        if ($request->boolean('force')) {
            Miembro::where('estado_membresia_id', $id)->update(['estado_membresia_id' => null]);
        } else {
            // Sin force, solo permitir si no hay miembros con ese estado
            if (Miembro::where('estado_membresia_id', $id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar: aún hay miembros con ese estado.'
                ], 422);
            }
        }

        $estado->delete();
        return response()->json(['success' => true, 'message' => 'Estado eliminado']);
    }

    public function createEstado(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255'
        ]);
        $estado = EstadoMembresia::firstOrCreate(['nombre' => strtolower($data['nombre'])], [
            'descripcion' => $data['descripcion'] ?? null,
        ]);
        return response()->json(['success' => true, 'estado' => $estado]);
    }

    public function createTipo(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
        ]);
        $slug = str($data['nombre'])->lower()->slug('_');
        $tipo = TipoMembresia::firstOrCreate(['slug' => $slug], [
            'nombre' => $data['nombre'],
        ]);
        return response()->json(['success' => true, 'tipo' => $tipo]);
    }

    public function deleteTipo(Request $request, $idOrSlug): JsonResponse
    {
        $data = $request->validate([
            'confirm_name' => 'required|string',
            'force' => 'nullable|boolean',
        ]);

        // Buscar por id o por slug
        $tipo = is_numeric($idOrSlug)
            ? TipoMembresia::find($idOrSlug)
            : TipoMembresia::where('slug', $idOrSlug)->first();
        if (!$tipo) {
            return response()->json(['success' => false, 'message' => 'Tipo no encontrado'], 404);
        }

        if (strcasecmp($data['confirm_name'], $tipo->nombre) !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'El nombre ingresado no coincide con el tipo seleccionado.',
            ], 422);
        }

        // Verificar miembros asociados a este tipo (slug)
        $miembrosAsociados = Miembro::where('tipo_membresia', $tipo->slug)->pluck('id');
        $cuotasAsociadas = false; // Si quieres controlar lógica de dependencia en cuotas, impleméntalo aquí

        if ($miembrosAsociados->count() > 0 && !$request->boolean('force')) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: hay miembros asociados con este tipo. Utilice FORZAR para desvincularlos.',
                'needs_force' => true,
                'dependientes' => $miembrosAsociados->count(),
            ], 409);
        }

        // Si force: poner en null el tipo de los miembros afectados
        if ($request->boolean('force')) {
            Miembro::where('tipo_membresia', $tipo->slug)->update(['tipo_membresia' => null]);
        } else {
            if ($miembrosAsociados->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar: aún hay miembros con ese tipo.'
                ], 422);
            }
        }

        $tipo->delete();
        return response()->json(['success' => true, 'message' => 'Tipo de Membresía eliminado correctamente.']);
    }
}
