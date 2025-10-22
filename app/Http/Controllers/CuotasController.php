<?php

namespace App\Http\Controllers;

use App\Models\CuotaMembresia;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CuotasController extends Controller
{
    /**
     * Mostrar lista de cuotas
     */
    public function index(Request $request): View
    {
        $query = CuotaMembresia::with(['miembro', 'creadoPor']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_cuota')) {
            $query->where('tipo_cuota', $request->tipo_cuota);
        }

        if ($request->filled('miembro_id')) {
            $query->where('miembro_id', $request->miembro_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_vencimiento', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_vencimiento', '<=', $request->fecha_hasta);
        }

        // Sorting
        $sortField = $request->get('sort', 'fecha_vencimiento');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validar campos de ordenamiento
        $allowedSortFields = ['miembro_id', 'tipo_cuota', 'monto', 'fecha_vencimiento', 'estado', 'fecha_pago'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'fecha_vencimiento';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 25);
        if ($perPage === 'all') {
            $cuotas = $query->get();
        } else {
            $perPage = (int) $perPage;
            if (!in_array($perPage, [25, 50, 100])) {
                $perPage = 25;
            }
            $cuotas = $query->paginate($perPage);
        }

        // Estadísticas
        $estadisticas = [
            'total_cuotas' => CuotaMembresia::count(),
            'pendientes' => CuotaMembresia::pendientes()->count(),
            'pagadas' => CuotaMembresia::pagadas()->count(),
            'vencidas' => CuotaMembresia::vencidas()->count(),
            'monto_pendiente' => CuotaMembresia::pendientes()->sum('monto'),
            'monto_pagado' => CuotaMembresia::pagadas()->sum('monto'),
        ];

        $miembros = Miembro::select('id', 'nombre_completo', 'numero_carnet')->get();

        return view('cuotas.index', compact('cuotas', 'estadisticas', 'miembros'));
    }

    /**
     * Mostrar formulario para crear cuota
     */
    public function create(): View
    {
        $miembros = Miembro::select('id', 'nombre_completo', 'numero_carnet')->get();
        $tiposCuota = ['mensual', 'trimestral', 'anual'];
        
        return view('cuotas.create', compact('miembros', 'tiposCuota'));
    }

    /**
     * Crear nueva cuota
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'miembro_id' => 'required|exists:miembros,id',
            'tipo_cuota' => 'required|in:mensual,trimestral,anual',
            'monto' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after:today',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $cuota = CuotaMembresia::create([
            'miembro_id' => $request->miembro_id,
            'tipo_cuota' => $request->tipo_cuota,
            'monto' => $request->monto,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'observaciones' => $request->observaciones,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('cuotas.index')
            ->with('success', 'Cuota creada exitosamente.');
    }

    /**
     * Mostrar detalles de cuota
     */
    public function show(CuotaMembresia $cuota): View
    {
        $cuota->load(['miembro', 'creadoPor']);
        return view('cuotas.show', compact('cuota'));
    }

    /**
     * Marcar cuota como pagada
     */
    public function marcarPagada(Request $request, CuotaMembresia $cuota): RedirectResponse
    {
        $request->validate([
            'metodo_pago' => 'required|string|max:50',
            'comprobante_url' => 'nullable|url|max:255',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $cuota->marcarComoPagada(
            $request->metodo_pago,
            $request->comprobante_url
        );

        if ($request->observaciones) {
            $cuota->update(['observaciones' => $request->observaciones]);
        }

        return redirect()->back()
            ->with('success', 'Cuota marcada como pagada.');
    }

    /**
     * Generar cuotas automáticamente
     */
    public function generarCuotas(Request $request): RedirectResponse
    {
        $request->validate([
            'tipo_cuota' => 'required|in:mensual,trimestral,anual',
            'monto' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'cantidad_periodos' => 'required|integer|min:1|max:12'
        ]);

        $miembros = Miembro::where('estado_membresia_id', 1)->get(); // Solo miembros activos
        $cuotasCreadas = 0;

        DB::transaction(function () use ($miembros, $request, &$cuotasCreadas) {
            foreach ($miembros as $miembro) {
                $fechaInicio = $request->fecha_inicio;
                
                for ($i = 0; $i < $request->cantidad_periodos; $i++) {
                    $fechaVencimiento = match($request->tipo_cuota) {
                        'mensual' => \Carbon\Carbon::parse($fechaInicio)->addMonths($i),
                        'trimestral' => \Carbon\Carbon::parse($fechaInicio)->addMonths($i * 3),
                        'anual' => \Carbon\Carbon::parse($fechaInicio)->addYears($i),
                    };

                    CuotaMembresia::create([
                        'miembro_id' => $miembro->id,
                        'tipo_cuota' => $request->tipo_cuota,
                        'monto' => $request->monto,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'created_by' => auth()->id(),
                    ]);
                    
                    $cuotasCreadas++;
                }
            }
        });

        return redirect()->route('cuotas.index')
            ->with('success', "Se generaron {$cuotasCreadas} cuotas exitosamente.");
    }

    /**
     * Actualizar cuotas vencidas
     */
    public function actualizarVencidas(): RedirectResponse
    {
        $cuotasVencidas = CuotaMembresia::vencidasPorFecha()->get();
        $actualizadas = 0;

        foreach ($cuotasVencidas as $cuota) {
            $cuota->marcarComoVencida();
            $actualizadas++;
        }

        return redirect()->back()
            ->with('success', "Se actualizaron {$actualizadas} cuotas como vencidas.");
    }

    /**
     * Exportar cuotas
     */
    public function exportar(Request $request)
    {
        $query = CuotaMembresia::with(['miembro']);

        // Aplicar mismos filtros que en index
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_cuota')) {
            $query->where('tipo_cuota', $request->tipo_cuota);
        }

        $cuotas = $query->orderBy('fecha_vencimiento', 'desc')->get();

        $filename = 'cuotas_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($cuotas) {
            $file = fopen('php://output', 'w');
            
            // Headers CSV
            fputcsv($file, [
                'Miembro',
                'Tipo Cuota',
                'Monto',
                'Fecha Vencimiento',
                'Estado',
                'Fecha Pago',
                'Método Pago'
            ]);

            foreach ($cuotas as $cuota) {
                fputcsv($file, [
                    $cuota->miembro->nombre_completo,
                    $cuota->tipo_cuota_label,
                    $cuota->monto,
                    $cuota->fecha_vencimiento->format('d/m/Y'),
                    $cuota->estado,
                    $cuota->fecha_pago ? $cuota->fecha_pago->format('d/m/Y') : '',
                    $cuota->metodo_pago ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Eliminar múltiples cuotas seleccionadas
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:cuotas_membresia,id'
        ]);

        $selectedIds = $request->input('selected_ids');
        $deletedCount = CuotaMembresia::whereIn('id', $selectedIds)->delete();

        return redirect()->route('cuotas.index')
            ->with('success', "Se eliminaron {$deletedCount} cuotas correctamente.");
    }
}