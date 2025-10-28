<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asamblea;
use App\Models\Miembro;
use App\Models\Organo;
use App\Models\Organizacion;
use Carbon\Carbon;

class AsambleaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asambleas = Asamblea::with(['organizacion', 'creadoPor'])
            ->orderBy('fecha_asamblea', 'desc')
            ->paginate(request('per_page', 25));

        $estadisticas = [
            'total' => Asamblea::count(),
            'programadas' => Asamblea::where('estado', 'convocada')->count(),
            'en_curso' => Asamblea::where('estado', 'en_proceso')->count(),
            'completadas' => Asamblea::where('estado', 'finalizada')->count(),
            'canceladas' => Asamblea::where('estado', 'cancelada')->count(),
            'proximas' => Asamblea::where('fecha_asamblea', '>=', Carbon::now())
                ->where('fecha_asamblea', '<=', Carbon::now()->addDays(7))
                ->count()
        ];

        $organos = Organo::all();

        return view('asambleas.index', compact('asambleas', 'estadisticas', 'organos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organos = Organo::all();
        $miembros = Miembro::where('estado_membresia_id', 1)->get(); // Solo miembros activos

        return view('asambleas.create', compact('organos', 'miembros'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_convocatoria' => 'required|date',
            'fecha_asamblea' => 'required|date|after:fecha_convocatoria',
            'lugar' => 'required|string|max:255',
            'tipo' => 'required|in:ordinaria,extraordinaria,especial',
            'modalidad' => 'required|in:presencial,virtual,hibrida',
            'enlace_virtual' => 'nullable|url',
            'quorum_minimo' => 'required|integer|min:1',
            'organizacion_id' => 'required|exists:organizaciones,id'
        ]);

        $asamblea = Asamblea::create([
            'organizacion_id' => $request->organizacion_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_convocatoria' => $request->fecha_convocatoria,
            'fecha_asamblea' => $request->fecha_asamblea,
            'lugar' => $request->lugar,
            'tipo' => $request->tipo,
            'modalidad' => $request->modalidad,
            'enlace_virtual' => $request->enlace_virtual,
            'quorum_minimo' => $request->quorum_minimo,
            'estado' => 'convocada',
            'asistentes_count' => 0,
            'quorum_alcanzado' => false,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('asambleas.proxima')
            ->with('success', 'Asamblea creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asamblea $asamblea)
    {
        $asamblea->load(['organizacion', 'creadoPor']);
        
        return view('asambleas.show', compact('asamblea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asamblea $asamblea)
    {
        $organizaciones = Organizacion::all();
        $organos = Organo::all();
        $miembros = Miembro::where('estado_membresia_id', 1)->get();

        return view('asambleas.edit', compact('asamblea', 'organizaciones', 'organos', 'miembros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asamblea $asamblea)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'lugar' => 'required|string|max:255',
            'tipo_asamblea' => 'required|in:ordinaria,extraordinaria,especial',
            'organo_id' => 'required|exists:organos,id',
            'presidente_id' => 'required|exists:miembros,id',
            'secretario_id' => 'required|exists:miembros,id',
            'quorum_minimo' => 'required|integer|min:1',
            'agenda' => 'nullable|array',
            'documentos' => 'nullable|array'
        ]);

        $asamblea->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'lugar' => $request->lugar,
            'tipo_asamblea' => $request->tipo_asamblea,
            'organo_id' => $request->organo_id,
            'presidente_id' => $request->presidente_id,
            'secretario_id' => $request->secretario_id,
            'quorum_minimo' => $request->quorum_minimo,
            'agenda' => $request->agenda ?? [],
            'documentos' => $request->documentos ?? [],
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('asambleas.index')
            ->with('success', 'Asamblea actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asamblea $asamblea)
    {
        $asamblea->delete();

        return redirect()->route('asambleas.index')
            ->with('success', 'Asamblea eliminada exitosamente.');
    }

    /**
     * Show the next upcoming assembly
     */
    public function proxima()
    {
        $proximaAsamblea = Asamblea::with(['organizacion', 'creadoPor'])
            ->where('fecha_asamblea', '>=', Carbon::now())
            ->where('estado', 'convocada')
            ->orderBy('fecha_asamblea', 'asc')
            ->first();

        $organos = Organo::all();

        // Estadísticas básicas que siempre se necesitan
        $estadisticas = [
            'total_asambleas' => Asamblea::count(),
            'asistencia_confirmada' => 0,
            'quorum_requerido' => 0,
            'dias_restantes' => 0,
            'porcentaje_asistencia' => 0
        ];

        if (!$proximaAsamblea) {
            return view('asambleas.proxima', compact('proximaAsamblea', 'organos', 'estadisticas'))
                ->with('message', 'No hay asambleas programadas próximamente.');
        }

        // Actualizar estadísticas específicas de la próxima asamblea
        $estadisticas = [
            'total_asambleas' => Asamblea::count(),
            'asistencia_confirmada' => $proximaAsamblea->asistentes_count ?? 0,
            'quorum_requerido' => $proximaAsamblea->quorum_minimo ?? 0,
            'dias_restantes' => Carbon::now()->diffInDays($proximaAsamblea->fecha_asamblea, false),
            'porcentaje_asistencia' => ($proximaAsamblea->quorum_minimo ?? 0) > 0 
                ? round((($proximaAsamblea->asistentes_count ?? 0) / $proximaAsamblea->quorum_minimo) * 100, 1)
                : 0
        ];

        return view('asambleas.proxima', compact('proximaAsamblea', 'estadisticas', 'organos'));
    }

    /**
     * Confirm attendance for the upcoming assembly
     */
    public function confirmarAsistencia(Request $request)
    {
        $request->validate([
            'asamblea_id' => 'required|exists:asambleas,id',
            'miembro_id' => 'required|exists:miembros,id',
            'confirmada' => 'required|boolean'
        ]);

        $asamblea = Asamblea::findOrFail($request->asamblea_id);
        
        $asamblea->asistencia()->updateOrCreate(
            ['miembro_id' => $request->miembro_id],
            ['confirmada' => $request->confirmada, 'fecha_confirmacion' => Carbon::now()]
        );

        return response()->json([
            'success' => true,
            'message' => $request->confirmada ? 'Asistencia confirmada' : 'Asistencia cancelada'
        ]);
    }
}
