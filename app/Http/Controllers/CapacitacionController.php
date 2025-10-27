<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use App\Models\Organizacion;
use App\Models\Miembro;
use App\Models\InscripcionCapacitacion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class CapacitacionController extends Controller
{
    /**
     * Mostrar lista de capacitaciones
     */
    public function index(): View
    {
        $capacitaciones = Capacitacion::orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        $estadisticas = [
            'total_capacitaciones' => Capacitacion::count(),
            'activas' => Capacitacion::where('activo', true)->count(),
            'inactivas' => Capacitacion::where('activo', false)->count(),
            'proximas' => Capacitacion::where('fecha_inicio', '>=', now())
                ->where('activo', true)
                ->count()
        ];

        return view('capacitaciones.index', compact('capacitaciones', 'estadisticas'));
    }

    /**
     * Mostrar próximo curso
     */
    public function proximo(): View
    {
        $proximoCurso = Capacitacion::where('fecha_inicio', '>=', Carbon::now())
            ->where('activo', true)
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        if (!$proximoCurso) {
            return view('capacitaciones.proximo', compact('proximoCurso'))
                ->with('message', 'No hay cursos programados próximamente.');
        }

        $estadisticas = [
            'total_capacitaciones' => Capacitacion::count(),
            'inscripciones_confirmadas' => 0, // Se calculará cuando se implemente la tabla de inscripciones
            'cupo_disponible' => $proximoCurso->cupo_maximo ?? 0,
            'dias_restantes' => Carbon::now()->diffInDays($proximoCurso->fecha_inicio, false),
            'porcentaje_ocupacion' => 0 // Se calculará cuando se implemente la tabla de inscripciones
        ];

        return view('capacitaciones.proximo', compact('proximoCurso', 'estadisticas'));
    }

    /**
     * Mostrar formulario para crear capacitación
     */
    public function create(): View
    {
        return view('capacitaciones.create');
    }

    /**
     * Guardar nueva capacitación
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date|after:today',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'lugar' => 'nullable|string|max:255',
            'modalidad' => 'required|in:presencial,virtual,mixta',
            'enlace_virtual' => 'nullable|url',
            'costo' => 'required|numeric|min:0',
            'cupo_maximo' => 'nullable|integer|min:1',
            'instructor' => 'nullable|string|max:255',
            'contenido' => 'nullable|string'
        ]);

        $capacitacion = Capacitacion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'lugar' => $request->lugar,
            'modalidad' => $request->modalidad,
            'enlace_virtual' => $request->enlace_virtual,
            'costo' => $request->costo,
            'cupo_maximo' => $request->cupo_maximo,
            'instructor' => $request->instructor,
            'contenido' => $request->contenido,
            'activo' => true
        ]);

        return redirect()->route('capacitaciones.proximo')
            ->with('success', 'Capacitación creada exitosamente.');
    }

    /**
     * Mostrar detalles de capacitación
     */
    public function show(Capacitacion $capacitacion): View
    {
        $capacitacion->load(['inscripciones.miembro']);
        return view('capacitaciones.show', compact('capacitacion'));
    }

    /**
     * Mostrar formulario para editar capacitación
     */
    public function edit(Capacitacion $capacitacion): View
    {
        return view('capacitaciones.edit', compact('capacitacion'));
    }

    /**
     * Actualizar capacitación
     */
    public function update(Request $request, Capacitacion $capacitacion): RedirectResponse
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'lugar' => 'nullable|string|max:255',
            'modalidad' => 'required|in:presencial,virtual,mixta',
            'enlace_virtual' => 'nullable|url',
            'costo' => 'required|numeric|min:0',
            'cupo_maximo' => 'nullable|integer|min:1',
            'instructor' => 'nullable|string|max:255',
            'contenido' => 'nullable|string',
            'activo' => 'required|boolean'
        ]);

        $capacitacion->update($request->all());

        return redirect()->route('capacitaciones.show', $capacitacion)
            ->with('success', 'Capacitación actualizada exitosamente.');
    }

    /**
     * Eliminar capacitación
     */
    public function destroy(Capacitacion $capacitacion): RedirectResponse
    {
        $capacitacion->delete();
        return redirect()->route('capacitaciones.index')
            ->with('success', 'Capacitación eliminada exitosamente.');
    }


    /**
     * Mostrar inscripciones de capacitaciones
     */
    public function inscripciones(): View
    {
        // Como la tabla inscripciones_capacitacion no existe, simularemos datos
        $inscripciones = collect([
            (object)[
                'id' => 1,
                'miembro' => (object)[
                    'nombre_completo' => 'Juan Pérez',
                    'numero_carnet' => 'CLDCI-001',
                    'email' => 'juan.perez@email.com'
                ],
                'capacitacion' => (object)[
                    'titulo' => 'Curso de Locución Profesional',
                    'fecha_inicio' => now()->subDays(5),
                    'modalidad' => 'presencial',
                    'costo' => 2500.00
                ],
                'fecha_inscripcion' => now()->subDays(10),
                'estado' => 'confirmada',
                'asistio' => true
            ],
            (object)[
                'id' => 2,
                'miembro' => (object)[
                    'nombre_completo' => 'María González',
                    'numero_carnet' => 'CLDCI-002',
                    'email' => 'maria.gonzalez@email.com'
                ],
                'capacitacion' => (object)[
                    'titulo' => 'Taller de Producción Radiofónica',
                    'fecha_inicio' => now()->subDays(3),
                    'modalidad' => 'virtual',
                    'costo' => 1800.00
                ],
                'fecha_inscripcion' => now()->subDays(8),
                'estado' => 'pendiente',
                'asistio' => false
            ],
            (object)[
                'id' => 3,
                'miembro' => (object)[
                    'nombre_completo' => 'Carlos Rodríguez',
                    'numero_carnet' => 'CLDCI-003',
                    'email' => 'carlos.rodriguez@email.com'
                ],
                'capacitacion' => (object)[
                    'titulo' => 'Seminario de Comunicación Digital',
                    'fecha_inicio' => now()->subDays(1),
                    'modalidad' => 'mixta',
                    'costo' => 1200.00
                ],
                'fecha_inscripcion' => now()->subDays(5),
                'estado' => 'confirmada',
                'asistio' => false
            ]
        ]);

        $estadisticas = [
            'total_inscripciones' => $inscripciones->count(),
            'confirmadas' => $inscripciones->where('estado', 'confirmada')->count(),
            'pendientes' => $inscripciones->where('estado', 'pendiente')->count(),
            'asistieron' => $inscripciones->where('asistio', true)->count(),
            'no_asistieron' => $inscripciones->where('asistio', false)->count(),
            'ingresos_totales' => $inscripciones->sum('capacitacion.costo')
        ];

        return view('capacitaciones.inscripciones', compact('inscripciones', 'estadisticas'));
    }

    /**
     * Inscribir miembro en capacitación
     */
    public function inscribir(Request $request): RedirectResponse
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'miembro_id' => 'required|exists:miembros,id'
        ]);

        InscripcionCapacitacion::create([
            'curso_id' => $request->curso_id,
            'miembro_id' => $request->miembro_id,
            'fecha_inscripcion' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Inscripción realizada exitosamente.');
    }
}
