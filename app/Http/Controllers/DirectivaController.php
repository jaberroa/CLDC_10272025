<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\PeriodoDirectiva;
use App\Models\Miembro;
use Illuminate\Support\Facades\DB;

class DirectivaController extends Controller
{
    /**
     * Display the directiva structure.
     */
    public function index()
    {
        // Obtener la organización principal
        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();
        
        if (!$organizacionPrincipal) {
            return redirect()->route('dashboard')->with('error', 'No se encontró la organización principal.');
        }

        // Obtener períodos de directiva activos
        $periodosActivos = PeriodoDirectiva::where('organizacion_id', $organizacionPrincipal->id)
            ->where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Obtener estructura jerárquica de la directiva actual
        $directivaActual = $periodosActivos->first();
        
        // Obtener miembros de la directiva actual
        $miembrosDirectiva = collect();
        if ($directivaActual && $directivaActual->directiva) {
            $directivaData = json_decode($directivaActual->directiva, true);
            $miembrosDirectiva = $this->obtenerMiembrosDirectiva($directivaData);
        }

        // Obtener estadísticas de la directiva
        $estadisticas = [
            'total_periodos' => PeriodoDirectiva::where('organizacion_id', $organizacionPrincipal->id)->count(),
            'periodos_activos' => $periodosActivos->count(),
            'miembros_directiva_actual' => $miembrosDirectiva->count(),
            'organizaciones_activas' => Organizacion::activas()->count(),
        ];

        return view('directiva.index', compact(
            'organizacionPrincipal',
            'periodosActivos',
            'directivaActual',
            'miembrosDirectiva',
            'estadisticas'
        ));
    }

    /**
     * Show the form for creating a new directiva period.
     */
    public function create()
    {
        $organizaciones = Organizacion::activas()->get();
        $miembros = Miembro::with(['organizacion', 'estadoMembresia'])
            ->whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'activa');
            })
            ->get();

        return view('directiva.create', compact('organizaciones', 'miembros'));
    }

    /**
     * Store a newly created directiva period.
     */
    public function store(Request $request)
    {
        $request->validate([
            'organizacion_id' => 'required|exists:organizaciones,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'directiva' => 'required|array',
            'directiva.*.cargo' => 'required|string',
            'directiva.*.miembro_id' => 'required|exists:miembros,id',
        ]);

        // Desactivar períodos anteriores
        PeriodoDirectiva::where('organizacion_id', $request->organizacion_id)
            ->update(['activo' => false]);

        // Crear nuevo período
        $periodo = PeriodoDirectiva::create([
            'organizacion_id' => $request->organizacion_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'directiva' => json_encode($request->directiva),
            'activo' => true,
        ]);

        return redirect()->route('directiva.index')
            ->with('success', 'Período de directiva creado exitosamente.');
    }

    /**
     * Display the specified directiva period.
     */
    public function show($id)
    {
        $periodo = PeriodoDirectiva::with('organizacion')->findOrFail($id);
        $directivaData = json_decode($periodo->directiva, true);
        $miembrosDirectiva = $this->obtenerMiembrosDirectiva($directivaData);

        return view('directiva.show', compact('periodo', 'miembrosDirectiva'));
    }

    /**
     * Show the form for editing the specified directiva period.
     */
    public function edit($id)
    {
        $periodo = PeriodoDirectiva::with('organizacion')->findOrFail($id);
        $organizaciones = Organizacion::activas()->get();
        $miembros = Miembro::with(['organizacion', 'estadoMembresia'])
            ->whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'activa');
            })
            ->get();

        return view('directiva.edit', compact('periodo', 'organizaciones', 'miembros'));
    }

    /**
     * Update the specified directiva period.
     */
    public function update(Request $request, $id)
    {
        $periodo = PeriodoDirectiva::findOrFail($id);

        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'directiva' => 'required|array',
            'directiva.*.cargo' => 'required|string',
            'directiva.*.miembro_id' => 'required|exists:miembros,id',
        ]);

        $periodo->update([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'directiva' => json_encode($request->directiva),
        ]);

        return redirect()->route('directiva.index')
            ->with('success', 'Período de directiva actualizado exitosamente.');
    }

    /**
     * Remove the specified directiva period.
     */
    public function destroy($id)
    {
        $periodo = PeriodoDirectiva::findOrFail($id);
        $periodo->delete();

        return redirect()->route('directiva.index')
            ->with('success', 'Período de directiva eliminado exitosamente.');
    }

    /**
     * Obtener miembros de la directiva con sus datos completos
     */
    private function obtenerMiembrosDirectiva($directivaData)
    {
        $miembrosIds = collect($directivaData)->pluck('miembro_id');
        
        return Miembro::with(['organizacion', 'estadoMembresia'])
            ->whereIn('id', $miembrosIds)
            ->get()
            ->map(function($miembro) use ($directivaData) {
                $cargo = collect($directivaData)->firstWhere('miembro_id', $miembro->id);
                $miembro->cargo_directiva = $cargo['cargo'] ?? 'Sin cargo';
                return $miembro;
            });
    }
}