<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuotaMembresia;
use App\Models\Miembro;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CuotasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $miembros = Miembro::take(10)->get();
        
        if ($miembros->isEmpty()) {
            $this->command->info('No hay miembros disponibles para crear cuotas.');
            return;
        }

        $tiposCuota = ['mensual', 'trimestral', 'anual'];
        $montos = [
            'mensual' => 500.00,
            'trimestral' => 1500.00,
            'anual' => 5000.00
        ];

        foreach ($miembros as $miembro) {
            // Crear cuotas para cada tipo
            foreach ($tiposCuota as $tipo) {
                $fechaVencimiento = match($tipo) {
                    'mensual' => Carbon::now()->addMonth(),
                    'trimestral' => Carbon::now()->addMonths(3),
                    'anual' => Carbon::now()->addYear(),
                };

                // Crear cuota pendiente
                CuotaMembresia::create([
                    'id' => Str::uuid(),
                    'miembro_id' => $miembro->id,
                    'tipo_cuota' => $tipo,
                    'monto' => $montos[$tipo],
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado' => 'pendiente',
                    'created_by' => '1', // Usuario admin
                ]);

                // Crear cuota pagada (historial)
                CuotaMembresia::create([
                    'id' => Str::uuid(),
                    'miembro_id' => $miembro->id,
                    'tipo_cuota' => $tipo,
                    'monto' => $montos[$tipo],
                    'fecha_vencimiento' => Carbon::now()->subMonth(),
                    'estado' => 'pagada',
                    'fecha_pago' => Carbon::now()->subMonth()->addDays(5),
                    'metodo_pago' => 'transferencia',
                    'comprobante_url' => 'https://example.com/comprobante.pdf',
                    'created_by' => '1',
                ]);
            }
        }

        $this->command->info('Cuotas creadas exitosamente para ' . $miembros->count() . ' miembros.');
    }
}