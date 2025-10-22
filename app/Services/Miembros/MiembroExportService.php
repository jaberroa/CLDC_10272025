<?php

namespace App\Services\Miembros;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MiembroExportService
{
    public function __construct(
        protected MiembroQueryService $queryService
    ) {
    }

    /**
     * Genera una respuesta CSV en streaming con los miembros filtrados.
     */
    public function streamCsv(array $filters = []): StreamedResponse
    {
        $miembros = $this->queryService->getForExport($filters);
        $filename = 'miembros_cldci_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($miembros) {
            $file = fopen('php://output', 'w');

            // Encabezados
            fputcsv($file, [
                'Número Carnet',
                'Nombre Completo',
                'Email',
                'Teléfono',
                'Profesión',
                'Tipo Membresía',
                'Estado',
                'Fecha Ingreso',
                'Organización',
                'Cédula',
            ]);

            // Datos
            $miembros->each(function ($miembro) use ($file) {
                fputcsv($file, [
                    $miembro->numero_carnet,
                    $miembro->nombre_completo,
                    $miembro->email,
                    $miembro->telefono,
                    $miembro->profesion,
                    $miembro->tipo_membresia,
                    $miembro->estado_membresia,
                    optional($miembro->fecha_ingreso)->format('d/m/Y'),
                    optional($miembro->organizacion)->nombre,
                    $miembro->cedula,
                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }
}
