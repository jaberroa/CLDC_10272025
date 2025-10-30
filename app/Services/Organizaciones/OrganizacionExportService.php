<?php

namespace App\Services\Organizaciones;

use App\Models\Organizacion;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrganizacionExportService
{
    /**
     * Stream CSV export of organizations
     */
    public function streamCsv(array $filters = []): Response
    {
        $organizaciones = $this->getOrganizacionesForExport($filters);

        $filename = 'organizaciones_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($organizaciones) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            fputcsv($handle, [
                'ID',
                'Nombre',
                'Código',
                'Tipo',
                'Estado',
                'Descripción',
                'Dirección',
                'Teléfono',
                'Email',
                'Total Miembros',
                'Miembros Activos',
                'Miembros Vencidos',
                'Total Asambleas',
                'Asambleas Activas',
                'Total Elecciones',
                'Elecciones Activas',
                'Total Cursos',
                'Cursos Activos',
                'Fecha Creación',
                'Última Actualización'
            ]);

            // Data
            foreach ($organizaciones as $organizacion) {
                fputcsv($handle, [
                    $organizacion->id,
                    $organizacion->nombre,
                    $organizacion->codigo,
                    $this->getTipoLabel($organizacion->tipo),
                    $this->getEstadoLabel($organizacion->estado),
                    $organizacion->descripcion ?? '',
                    $organizacion->direccion ?? '',
                    $organizacion->telefono ?? '',
                    $organizacion->email ?? '',
                    $organizacion->miembros_count ?? 0,
                    $organizacion->miembros_activos_count ?? 0,
                    $organizacion->miembros_vencidos_count ?? 0,
                    $organizacion->asambleas_count ?? 0,
                    $organizacion->asambleas_activas_count ?? 0,
                    $organizacion->elecciones_count ?? 0,
                    $organizacion->elecciones_activas_count ?? 0,
                    $organizacion->cursos_count ?? 0,
                    $organizacion->cursos_activos_count ?? 0,
                    $organizacion->created_at->format('d/m/Y H:i:s'),
                    $organizacion->updated_at->format('d/m/Y H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get organizations data for export
     */
    private function getOrganizacionesForExport(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Organizacion::query();

        // Aplicar filtros
        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        return $query->withCount([
            'miembros',
            'miembros as miembros_activos_count' => function ($query) {
                $query->whereHas('estadoMembresia', function ($q) {
                    $q->where('nombre', 'activa');
                });
            },
            'miembros as miembros_vencidos_count' => function ($query) {
                $query->where('fecha_vencimiento', '<', now());
            },
            'asambleas',
            'asambleas as asambleas_activas_count' => function ($query) {
                $query->where('estado', 'convocada');
            },
            'elecciones',
            'elecciones as elecciones_activas_count' => function ($query) {
                $query->where('estado', 'en_proceso');
            },
            'cursos',
            'cursos as cursos_activos_count' => function ($query) {
                $query->where('estado', 'activo');
            },
        ])
        ->orderBy('nombre')
        ->get();
    }

    /**
     * Get tipo label in Spanish
     */
    private function getTipoLabel(string $tipo): string
    {
        $labels = [
            'nacional' => 'Nacional',
            'seccional' => 'Seccional Provincial',
            'seccional_internacional' => 'Seccional Internacional',
            'diaspora' => 'Diáspora',
        ];

        return $labels[$tipo] ?? ucfirst($tipo);
    }

    /**
     * Get estado label in Spanish
     */
    private function getEstadoLabel(string $estado): string
    {
        $labels = [
            'activa' => 'Activa',
            'inactiva' => 'Inactiva',
            'suspendida' => 'Suspendida',
        ];

        return $labels[$estado] ?? ucfirst($estado);
    }

    /**
     * Export organizations to Excel (future implementation)
     */
    public function streamExcel(array $filters = []): Response
    {
        // TODO: Implement Excel export using Laravel Excel package
        throw new \Exception('Excel export not implemented yet');
    }

    /**
     * Export organizations statistics
     */
    public function exportEstadisticas(): Response
    {
        $estadisticas = [
            'total_organizaciones' => Organizacion::count(),
            'por_tipo' => Organizacion::select('tipo', DB::raw('count(*) as total'))
                ->groupBy('tipo')
                ->get(),
            'por_estado' => Organizacion::select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->get(),
            'con_miembros' => Organizacion::has('miembros')->count(),
            'sin_miembros' => Organizacion::doesntHave('miembros')->count(),
            'fecha_exportacion' => now()->format('d/m/Y H:i:s'),
        ];

        $filename = 'estadisticas_organizaciones_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($estadisticas, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

