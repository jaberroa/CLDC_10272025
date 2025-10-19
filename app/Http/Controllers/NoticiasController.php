<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asamblea;
use App\Models\Capacitacion;
use App\Models\Eleccion;
use App\Models\DocumentoLegal;
use App\Models\TransaccionFinanciera;
use Illuminate\Support\Facades\DB;

class NoticiasController extends Controller
{
    /**
     * Display a listing of all news.
     */
    public function index(Request $request)
    {
        $noticias = collect();
        
        // 1. Asambleas (todas las futuras)
        $asambleasNoticias = Asamblea::where('fecha_asamblea', '>=', now())
            ->whereIn('estado', ['convocada', 'programada'])
            ->orderBy('fecha_asamblea', 'asc')
            ->get()
            ->map(function ($asamblea) {
                return [
                    'id' => $asamblea->id,
                    'tipo' => 'asamblea',
                    'titulo' => $asamblea->titulo,
                    'descripcion' => 'Convocatoria oficial de asamblea',
                    'fecha' => $asamblea->fecha_asamblea,
                    'lugar' => $asamblea->lugar ?? 'Por definir',
                    'modalidad' => $asamblea->modalidad,
                    'icono' => 'ri-calendar-line',
                    'color' => 'info',
                    'prioridad' => 1,
                    'categoria' => 'Asambleas'
                ];
            });

        // 2. Comunicados de directiva
        $comunicadosNoticias = DocumentoLegal::where('tipo', 'comunicado')
            ->where('activo', true)
            ->orderBy('fecha_emision', 'desc')
            ->get()
            ->map(function ($comunicado) {
                return [
                    'id' => $comunicado->id,
                    'tipo' => 'comunicado',
                    'titulo' => $comunicado->titulo,
                    'descripcion' => 'Comunicado oficial de la directiva',
                    'fecha' => $comunicado->fecha_emision,
                    'lugar' => 'Oficina Nacional',
                    'modalidad' => 'oficial',
                    'icono' => 'ri-megaphone-line',
                    'color' => 'warning',
                    'prioridad' => 2,
                    'categoria' => 'Comunicados'
                ];
            });

        // 3. Capacitaciones próximas
        $capacitacionesNoticias = Capacitacion::where('fecha_inicio', '>=', now())
            ->whereIn('estado', ['programada', 'activa'])
            ->orderBy('fecha_inicio', 'asc')
            ->get()
            ->map(function ($capacitacion) {
                return [
                    'id' => $capacitacion->id,
                    'tipo' => 'capacitacion',
                    'titulo' => $capacitacion->titulo,
                    'descripcion' => 'Nueva capacitación disponible',
                    'fecha' => $capacitacion->fecha_inicio,
                    'lugar' => $capacitacion->lugar ?? 'Por definir',
                    'modalidad' => $capacitacion->modalidad,
                    'icono' => 'ri-graduation-cap-line',
                    'color' => 'success',
                    'prioridad' => 3,
                    'categoria' => 'Capacitaciones'
                ];
            });

        // 4. Elecciones próximas
        $eleccionesNoticias = Eleccion::where('fecha_inicio', '>=', now())
            ->whereIn('estado', ['preparacion', 'activa'])
            ->orderBy('fecha_inicio', 'asc')
            ->get()
            ->map(function ($eleccion) {
                return [
                    'id' => $eleccion->id,
                    'tipo' => 'eleccion',
                    'titulo' => $eleccion->titulo,
                    'descripcion' => 'Proceso electoral en curso',
                    'fecha' => $eleccion->fecha_inicio,
                    'lugar' => 'Virtual',
                    'modalidad' => 'virtual',
                    'icono' => 'ri-government-line',
                    'color' => 'primary',
                    'prioridad' => 4,
                    'categoria' => 'Elecciones'
                ];
            });

        // 5. Noticias institucionales (transacciones importantes)
        $institucionalesNoticias = TransaccionFinanciera::where('monto', '>', 50000)
            ->where('tipo', 'ingreso')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaccion) {
                return [
                    'id' => $transaccion->id,
                    'tipo' => 'institucional',
                    'titulo' => 'Ingreso importante registrado',
                    'descripcion' => 'Nuevo ingreso de $' . number_format($transaccion->monto, 2),
                    'fecha' => $transaccion->fecha,
                    'lugar' => $transaccion->organizacion->nombre ?? 'CLDCI',
                    'modalidad' => 'financiero',
                    'icono' => 'ri-money-dollar-circle-line',
                    'color' => 'success',
                    'prioridad' => 5,
                    'categoria' => 'Institucional'
                ];
            });

        // Combinar todas las noticias
        $noticias = $asambleasNoticias
            ->concat($comunicadosNoticias)
            ->concat($capacitacionesNoticias)
            ->concat($eleccionesNoticias)
            ->concat($institucionalesNoticias)
            ->sortBy(['prioridad', 'fecha']);

        // Estadísticas para filtros
        $estadisticas = [
            'total_noticias' => $noticias->count(),
            'por_categoria' => $noticias->groupBy('categoria')->map->count(),
            'por_tipo' => $noticias->groupBy('tipo')->map->count(),
        ];

        // Filtros
        $categoriaFiltro = $request->get('categoria');
        $tipoFiltro = $request->get('tipo');

        if ($categoriaFiltro) {
            $noticias = $noticias->where('categoria', $categoriaFiltro);
        }

        if ($tipoFiltro) {
            $noticias = $noticias->where('tipo', $tipoFiltro);
        }

        return view('noticias.index', compact(
            'noticias',
            'estadisticas',
            'categoriaFiltro',
            'tipoFiltro'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('noticias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementar creación de noticias si es necesario
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Implementar vista detalle de noticia si es necesario
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Implementar edición de noticia si es necesario
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Implementar actualización de noticia si es necesario
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Implementar eliminación de noticia si es necesario
    }
}
