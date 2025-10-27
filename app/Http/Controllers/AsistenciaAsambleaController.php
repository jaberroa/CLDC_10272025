<?php

namespace App\Http\Controllers;

use App\Models\Asamblea;
use App\Models\AsistenciaAsamblea;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaAsambleaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $asambleaId = $request->get('asamblea_id');
        $asamblea = null;
        
        if ($asambleaId) {
            $asamblea = Asamblea::with(['organizacion', 'creadoPor'])->findOrFail($asambleaId);
        }

        $query = AsistenciaAsamblea::with(['asamblea.organizacion', 'miembro']);

        if ($asambleaId) {
            $query->where('asamblea_id', $asambleaId);
        }

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->whereHas('miembro', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_asistencia', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_asistencia', '<=', $request->get('fecha_hasta'));
        }

        $asistencias = $query->orderBy('fecha_asistencia', 'desc')
            ->paginate(request('per_page', 25));

        $estadisticas = [
            'total' => AsistenciaAsamblea::when($asambleaId, function($q) use ($asambleaId) {
                return $q->where('asamblea_id', $asambleaId);
            })->count(),
            'confirmadas' => AsistenciaAsamblea::when($asambleaId, function($q) use ($asambleaId) {
                return $q->where('asamblea_id', $asambleaId);
            })->where('estado', 'confirmada')->count(),
            'presentes' => AsistenciaAsamblea::when($asambleaId, function($q) use ($asambleaId) {
                return $q->where('asamblea_id', $asambleaId);
            })->where('estado', 'presente')->count(),
            'ausentes' => AsistenciaAsamblea::when($asambleaId, function($q) use ($asambleaId) {
                return $q->where('asamblea_id', $asambleaId);
            })->where('estado', 'ausente')->count(),
            'tardanzas' => AsistenciaAsamblea::when($asambleaId, function($q) use ($asambleaId) {
                return $q->where('asamblea_id', $asambleaId);
            })->where('estado', 'tardanza')->count(),
            'hoy' => AsistenciaAsamblea::when($asambleaId, function($q) use ($asambleaId) {
                return $q->where('asamblea_id', $asambleaId);
            })->whereDate('fecha_asistencia', Carbon::today())->count()
        ];

        $asambleas = Asamblea::orderBy('fecha_asamblea', 'desc')->get();

        return view('asambleas.asistencias.index', compact('asistencias', 'estadisticas', 'asamblea', 'asambleas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $asambleaId = $request->get('asamblea_id');
        $asamblea = null;
        
        if ($asambleaId) {
            $asamblea = Asamblea::with(['organizacion'])->findOrFail($asambleaId);
        }

        $asambleas = Asamblea::orderBy('fecha_asamblea', 'desc')->get();
        $miembros = Miembro::where('estado_membresia_id', 1)->orderBy('nombre')->get();

        return view('asambleas.asistencias.create', compact('asamblea', 'asambleas', 'miembros'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asamblea_id' => 'required|exists:asambleas,id',
            'miembro_id' => 'required|exists:miembros,id',
            'estado' => 'required|in:confirmada,presente,ausente,tardanza',
            'fecha_asistencia' => 'required|date',
            'hora_llegada' => 'nullable|date_format:H:i',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar si ya existe una asistencia para este miembro en esta asamblea
        $existingAsistencia = AsistenciaAsamblea::where('asamblea_id', $request->asamblea_id)
            ->where('miembro_id', $request->miembro_id)
            ->first();

        if ($existingAsistencia) {
            return redirect()->back()
                ->withErrors(['miembro_id' => 'Este miembro ya tiene una asistencia registrada para esta asamblea.'])
                ->withInput();
        }

        $asistencia = AsistenciaAsamblea::create([
            'asamblea_id' => $request->asamblea_id,
            'miembro_id' => $request->miembro_id,
            'estado' => $request->estado,
            'fecha_asistencia' => $request->fecha_asistencia,
            'hora_llegada' => $request->hora_llegada,
            'observaciones' => $request->observaciones,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('asambleas.asistencias.index', ['asamblea_id' => $request->asamblea_id])
            ->with('success', 'Asistencia registrada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AsistenciaAsamblea $asistenciaAsamblea)
    {
        $asistenciaAsamblea->load(['asamblea.organizacion', 'miembro', 'creadoPor']);
        
        return view('asambleas.asistencias.show', compact('asistenciaAsamblea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AsistenciaAsamblea $asistenciaAsamblea)
    {
        $asambleas = Asamblea::orderBy('fecha_asamblea', 'desc')->get();
        $miembros = Miembro::where('estado_membresia_id', 1)->orderBy('nombre')->get();

        return view('asambleas.asistencias.edit', compact('asistenciaAsamblea', 'asambleas', 'miembros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AsistenciaAsamblea $asistenciaAsamblea)
    {
        $request->validate([
            'asamblea_id' => 'required|exists:asambleas,id',
            'miembro_id' => 'required|exists:miembros,id',
            'estado' => 'required|in:confirmada,presente,ausente,tardanza',
            'fecha_asistencia' => 'required|date',
            'hora_llegada' => 'nullable|date_format:H:i',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar si ya existe otra asistencia para este miembro en esta asamblea (excluyendo la actual)
        $existingAsistencia = AsistenciaAsamblea::where('asamblea_id', $request->asamblea_id)
            ->where('miembro_id', $request->miembro_id)
            ->where('id', '!=', $asistenciaAsamblea->id)
            ->first();

        if ($existingAsistencia) {
            return redirect()->back()
                ->withErrors(['miembro_id' => 'Este miembro ya tiene una asistencia registrada para esta asamblea.'])
                ->withInput();
        }

        $asistenciaAsamblea->update([
            'asamblea_id' => $request->asamblea_id,
            'miembro_id' => $request->miembro_id,
            'estado' => $request->estado,
            'fecha_asistencia' => $request->fecha_asistencia,
            'hora_llegada' => $request->hora_llegada,
            'observaciones' => $request->observaciones,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('asambleas.asistencias.index', ['asamblea_id' => $request->asamblea_id])
            ->with('success', 'Asistencia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AsistenciaAsamblea $asistenciaAsamblea)
    {
        $asambleaId = $asistenciaAsamblea->asamblea_id;
        $asistenciaAsamblea->delete();

        return redirect()->route('asambleas.asistencias.index', ['asamblea_id' => $asambleaId])
            ->with('success', 'Asistencia eliminada exitosamente.');
    }

    /**
     * Confirmar asistencia de un miembro
     */
    public function confirmarAsistencia(Request $request)
    {
        $request->validate([
            'asamblea_id' => 'required|exists:asambleas,id',
            'miembro_id' => 'required|exists:miembros,id'
        ]);

        $asamblea = Asamblea::findOrFail($request->asamblea_id);
        $miembro = Miembro::findOrFail($request->miembro_id);

        // Verificar si ya existe una asistencia
        $asistencia = AsistenciaAsamblea::where('asamblea_id', $request->asamblea_id)
            ->where('miembro_id', $request->miembro_id)
            ->first();

        if ($asistencia) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una asistencia registrada para este miembro.'
            ]);
        }

        // Crear nueva asistencia
        AsistenciaAsamblea::create([
            'asamblea_id' => $request->asamblea_id,
            'miembro_id' => $request->miembro_id,
            'estado' => 'confirmada',
            'fecha_asistencia' => Carbon::now(),
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia confirmada exitosamente.'
        ]);
    }

    /**
     * Marcar como presente
     */
    public function marcarPresente(AsistenciaAsamblea $asistenciaAsamblea)
    {
        $asistenciaAsamblea->update([
            'estado' => 'presente',
            'hora_llegada' => Carbon::now()->format('H:i'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', 'Miembro marcado como presente.');
    }

    /**
     * Marcar como ausente
     */
    public function marcarAusente(AsistenciaAsamblea $asistenciaAsamblea)
    {
        $asistenciaAsamblea->update([
            'estado' => 'ausente',
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', 'Miembro marcado como ausente.');
    }

    /**
     * Marcar como tardanza
     */
    public function marcarTardanza(AsistenciaAsamblea $asistenciaAsamblea)
    {
        $asistenciaAsamblea->update([
            'estado' => 'tardanza',
            'hora_llegada' => Carbon::now()->format('H:i'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', 'Miembro marcado con tardanza.');
    }
}

