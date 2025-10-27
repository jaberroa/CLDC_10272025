<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CuotaMembresia extends Model
{
    use HasFactory;

    protected $table = 'cuotas_membresia';

    protected $fillable = [
        'miembro_id',
        'tipo_cuota',
        'monto',
        'fecha_vencimiento',
        'fecha_pago',
        'estado',
        'recurrente',
        'frecuencia_recurrencia',
        'proxima_fecha_generacion',
        'observaciones'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'proxima_fecha_generacion' => 'date',
        'monto' => 'decimal:2',
        'recurrente' => 'boolean'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'vencida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_cuota', $tipo);
    }

    public function scopeVencidasAutomaticamente($query)
    {
        return $query->where('estado', 'pendiente')
                    ->where('fecha_vencimiento', '<', now());
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->where('estado', 'pendiente')
                    ->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->where('fecha_vencimiento', '>', now());
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEsVencidaAttribute()
    {
        return $this->estado === 'vencida' || 
               ($this->estado === 'pendiente' && $this->fecha_vencimiento->isPast());
    }

    public function getEsPorVencerAttribute()
    {
        return $this->estado === 'pendiente' && 
               $this->fecha_vencimiento->isFuture() && 
               $this->fecha_vencimiento->diffInDays(now()) <= 30;
    }

    public function getDiasVencidaAttribute()
    {
        if ($this->es_vencida) {
            return $this->fecha_vencimiento->diffInDays(now());
        }
        return 0;
    }

    public function getDiasPorVencerAttribute()
    {
        if ($this->es_por_vencer) {
            return $this->fecha_vencimiento->diffInDays(now());
        }
        return null;
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pagada' => 'success',
            'pendiente' => 'warning',
            'vencida' => 'danger',
            'cancelada' => 'secondary',
            default => 'secondary'
        };
    }

    public function getEstadoIconAttribute()
    {
        return match($this->estado) {
            'pagada' => 'check-circle',
            'pendiente' => 'clock',
            'vencida' => 'exclamation-triangle',
            'cancelada' => 'times-circle',
            default => 'question-circle'
        };
    }

    // ========================================
    // MÉTODOS DE NEGOCIO
    // ========================================

    public function marcarComoPagada($fechaPago = null)
    {
        $this->update([
            'estado' => 'pagada',
            'fecha_pago' => $fechaPago ?? now()
        ]);
    }

    public function marcarComoVencida()
    {
        $this->update(['estado' => 'vencida']);
    }

    public function estaVencida()
    {
        return $this->estado === 'vencida' || ($this->estado === 'pendiente' && $this->fecha_vencimiento < now());
    }

    public function cancelar($observaciones = null)
    {
        $this->update([
            'estado' => 'cancelada',
            'observaciones' => $observaciones ?? $this->observaciones
        ]);
    }

    public function esPagada()
    {
        return $this->estado === 'pagada';
    }

    public function esPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function esVencida()
    {
        return $this->estado === 'vencida';
    }

    public function esCancelada()
    {
        return $this->estado === 'cancelada';
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function estadisticas()
    {
        return [
            'total' => self::count(),
            'pendientes' => self::pendientes()->count(),
            'pagadas' => self::pagadas()->count(),
            'vencidas' => self::vencidas()->count(),
            'canceladas' => self::canceladas()->count(),
            'por_vencer' => self::porVencer()->count(),
            'monto_total_pendiente' => self::pendientes()->sum('monto'),
            'monto_total_pagado' => self::pagadas()->sum('monto'),
            'monto_total_vencido' => self::vencidas()->sum('monto')
        ];
    }

    public static function actualizarVencidas()
    {
        $vencidas = self::vencidasAutomaticamente()->get();
        
        foreach ($vencidas as $cuota) {
            $cuota->marcarComoVencida();
        }
        
        return $vencidas->count();
    }

    public static function generarCuotas($miembroId, $tipoCuota, $monto, $cantidadMeses = 12)
    {
        $cuotas = [];
        $fechaInicio = now();
        
        for ($i = 0; $i < $cantidadMeses; $i++) {
            $fechaVencimiento = $fechaInicio->copy()->addMonths($i + 1);
            
            $cuotas[] = self::create([
                'miembro_id' => $miembroId,
                'tipo_cuota' => $tipoCuota,
                'monto' => $monto,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'pendiente'
            ]);
        }
        
        return $cuotas;
    }
}