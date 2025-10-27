<?php

namespace App\Http\Controllers;

use App\Models\CronogramaDirectiva;
use App\Models\Organo;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronogramaDirectivaController extends Controller
{
    /**
     * Display a listing of cronogramas.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'buscar',
            'tipo_evento',
            'estado',
            'organo_id',
            'fecha_desde',
            'fecha_hasta',
        ]);

        $query = CronogramaDirectiva::with(['organo', 'responsable']);

        // Aplicar filtros
        if (!empty($filters['buscar'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('titulo', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhere('descripcion', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhere('lugar', 'like', '%' . $filters['buscar'] . '%');
            });
        }

        if (!empty($filters['tipo_evento'])) {
            $query->where('tipo_evento', $filters['tipo_evento']);
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['organo_id'])) {
            $query->where('organo_id', $filters['organo_id']);
        }

        if (!empty($filters['fecha_desde'])) {
            $query->where('fecha_inicio', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->where('fecha_inicio', '<=', $filters['fecha_hasta']);
        }

        // Ordenar por fecha de inicio ascendente
        $query->orderBy('fecha_inicio', 'asc');

        $cronogramas = $query->paginate(25);

        // Estadísticas para las tarjetas
        $estadisticas = [
            'total' => CronogramaDirectiva::count(),
            'programados' => CronogramaDirectiva::programados()->count(),
            'en_curso' => CronogramaDirectiva::enCurso()->count(),
            'completados' => CronogramaDirectiva::completados()->count(),
            'cancelados' => CronogramaDirectiva::cancelados()->count(),
            'proximos' => CronogramaDirectiva::proximos(7)->count(),
        ];

        // Datos para filtros
        $organos = Organo::activos()->get();
        $miembros = Miembro::activos()->get();

        return view('cronograma-directiva.index', compact(
            'cronogramas',
            'estadisticas',
            'organos',
            'miembros',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new cronograma.
     */
    public function create()
    {
        $organos = Organo::activos()->get();
        $miembros = Miembro::activos()->get();

        return view('cronograma-directiva.create', compact(
            'organos',
            'miembros'
        ));
    }

    /**
     * Store a newly created cronograma.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'lugar' => 'nullable|string|max:255',
            'tipo_evento' => 'required|in:reunion,asamblea,capacitacion,eleccion,conferencia',
            'estado' => 'required|in:programado,en_curso,completado,cancelado',
            'organo_id' => 'nullable|exists:organos,id',
            'responsable_id' => 'nullable|exists:miembros,id',
            'observaciones' => 'nullable|string|max:1000',
            'requiere_confirmacion' => 'boolean',
            'cupo_maximo' => 'nullable|integer|min:1',
        ]);

        $cronograma = CronogramaDirectiva::create($request->validated() + [
            'created_by' => auth()->id()
        ]);

        return redirect()->route('cronograma-directiva.index')->with('success', 'Evento agregado al cronograma exitosamente.');
    }

    /**
     * Display the specified cronograma.
     */
    public function show(CronogramaDirectiva $cronogramaDirectiva)
    {
        $cronogramaDirectiva->load(['organo', 'responsable', 'creadoPor', 'actualizadoPor']);

        return view('cronograma-directiva.show', compact('cronogramaDirectiva'));
    }

    /**
     * Show the form for editing the specified cronograma.
     */
    public function edit(CronogramaDirectiva $cronogramaDirectiva)
    {
        $organos = Organo::activos()->get();
        $miembros = Miembro::activos()->get();

        return view('cronograma-directiva.edit', compact(
            'cronogramaDirectiva',
            'organos',
            'miembros'
        ));
    }

    /**
     * Update the specified cronograma.
     */
    public function update(Request $request, CronogramaDirectiva $cronogramaDirectiva)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'lugar' => 'nullable|string|max:255',
            'tipo_evento' => 'required|in:reunion,asamblea,capacitacion,eleccion,conferencia',
            'estado' => 'required|in:programado,en_curso,completado,cancelado',
            'organo_id' => 'nullable|exists:organos,id',
            'responsable_id' => 'nullable|exists:miembros,id',
            'observaciones' => 'nullable|string|max:1000',
            'requiere_confirmacion' => 'boolean',
            'cupo_maximo' => 'nullable|integer|min:1',
        ]);

        $cronogramaDirectiva->update($request->validated() + [
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('cronograma-directiva.index')->with('success', 'Evento actualizado exitosamente.');
    }

    /**
     * Remove the specified cronograma from storage.
     */
    public function destroy(CronogramaDirectiva $cronogramaDirectiva)
    {
        $cronogramaDirectiva->delete();
        return response()->json(['message' => 'Evento eliminado del cronograma exitosamente.']);
    }

    /**
     * Remove multiple cronogramas from storage.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:cronogramas_directiva,id',
        ]);

        CronogramaDirectiva::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Eventos seleccionados eliminados exitosamente.']);
    }

    /**
     * Iniciar evento.
     */
    public function iniciar(CronogramaDirectiva $cronogramaDirectiva)
    {
        $cronogramaDirectiva->iniciar();
        return back()->with('success', 'Evento iniciado exitosamente.');
    }

    /**
     * Completar evento.
     */
    public function completar(CronogramaDirectiva $cronogramaDirectiva)
    {
        $cronogramaDirectiva->completar();
        return back()->with('success', 'Evento completado exitosamente.');
    }

    /**
     * Cancelar evento.
     */
    public function cancelar(CronogramaDirectiva $cronogramaDirectiva)
    {
        $cronogramaDirectiva->cancelar();
        return back()->with('success', 'Evento cancelado exitosamente.');
    }

    /**
     * Export cronogramas.
     */
    public function export(Request $request)
    {
        // Lógica de exportación
        return response()->json(['message' => 'Funcionalidad de exportación no implementada aún.']);
    }
}
