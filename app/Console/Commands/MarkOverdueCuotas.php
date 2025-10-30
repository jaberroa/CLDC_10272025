<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CuotaMembresia;
use App\Models\CuotaHistorial;

class MarkOverdueCuotas extends Command
{
    protected $signature = 'cuotas:mark-overdue';
    protected $description = 'Marca como vencidas las cuotas pendientes cuya fecha de vencimiento es menor a hoy';

    public function handle(): int
    {
        $hoy = now()->startOfDay();
        $cuotas = CuotaMembresia::where('estado', 'pendiente')
            ->whereDate('fecha_vencimiento', '<', $hoy)
            ->get();

        foreach ($cuotas as $cuota) {
            $anterior = $cuota->estado;
            $cuota->estado = 'vencida';
            $cuota->save();

            CuotaHistorial::create([
                'cuota_id' => $cuota->id,
                'estado_anterior' => $anterior,
                'estado_nuevo' => 'vencida',
                'user_id' => null,
                'motivo' => 'Marcado automático por fecha de vencimiento',
            ]);
        }

        $this->info('Cuotas marcadas como vencidas: '.$cuotas->count());
        return self::SUCCESS;
    }
}


