<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionFinanciera extends Model
{
    use HasFactory;

    protected $table = 'transacciones_financieras';
    protected $connection = 'pgsql';

    protected $fillable = [
        'organizacion_id',
        'tipo',
        'concepto',
        'monto',
        'fecha',
        'estado',
        'referencia',
        'observaciones',
        'created_by'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeEgresos($query)
    {
        return $query->where('tipo', 'egreso');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePorPeriodo($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEsIngresoAttribute()
    {
        return $this->tipo === 'ingreso';
    }

    public function getEsEgresoAttribute()
    {
        return $this->tipo === 'egreso';
    }

    public function getEsConfirmadaAttribute()
    {
        return $this->estado === 'confirmada';
    }

    public function getEsPendienteAttribute()
    {
        return $this->estado === 'pendiente';
    }

    public function getEsCanceladaAttribute()
    {
        return $this->estado === 'cancelada';
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function estadisticas($organizacionId = null)
    {
        $query = self::query();
        
        if ($organizacionId) {
            $query->where('organizacion_id', $organizacionId);
        }

        return [
            'total_transacciones' => $query->count(),
            'total_ingresos' => $query->ingresos()->sum('monto'),
            'total_egresos' => $query->egresos()->sum('monto'),
            'saldo_actual' => $query->ingresos()->sum('monto') - $query->egresos()->sum('monto'),
            'transacciones_confirmadas' => $query->confirmadas()->count(),
            'transacciones_pendientes' => $query->pendientes()->count()
        ];
    }
}