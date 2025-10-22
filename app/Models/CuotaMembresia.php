<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuotaMembresia extends Model
{
    use HasFactory;

    protected $table = 'cuotas_membresia';

    protected $fillable = [
        'id',
        'miembro_id',
        'tipo_cuota',
        'monto',
        'fecha_vencimiento',
        'estado',
        'fecha_pago',
        'metodo_pago',
        'comprobante_url',
        'observaciones',
        'created_by'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];

    // Relaciones
    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
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

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_cuota', $tipo);
    }

    public function scopeVencidasPorFecha($query)
    {
        return $query->where('estado', 'pendiente')
                    ->where('fecha_vencimiento', '<', now());
    }

    // Accessors
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pagada' => 'success',
            'pendiente' => 'warning',
            'vencida' => 'danger',
            default => 'secondary'
        };
    }

    public function getTipoCuotaLabelAttribute(): string
    {
        return match($this->tipo_cuota) {
            'mensual' => 'Mensual',
            'trimestral' => 'Trimestral',
            'anual' => 'Anual',
            default => ucfirst($this->tipo_cuota)
        };
    }

    // Métodos
    public function marcarComoPagada($metodoPago = null, $comprobanteUrl = null): void
    {
        $this->update([
            'estado' => 'pagada',
            'fecha_pago' => now(),
            'metodo_pago' => $metodoPago,
            'comprobante_url' => $comprobanteUrl
        ]);
    }

    public function marcarComoVencida(): void
    {
        $this->update(['estado' => 'vencida']);
    }

    public function estaVencida(): bool
    {
        return $this->estado === 'pendiente' && $this->fecha_vencimiento < now();
    }
}