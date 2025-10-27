<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Asamblea extends Model
{
    use HasFactory;

    protected $table = 'asambleas';

    protected $fillable = [
        'organizacion_id',
        'titulo',
        'descripcion',
        'fecha_convocatoria',
        'fecha_asamblea',
        'lugar',
        'tipo',
        'modalidad',
        'enlace_virtual',
        'quorum_minimo',
        'convocatoria_url',
        'acta_url',
        'estado',
        'asistentes_count',
        'quorum_alcanzado',
        'created_by'
    ];

    protected $casts = [
        'fecha_convocatoria' => 'datetime',
        'fecha_asamblea' => 'datetime',
        'quorum_alcanzado' => 'boolean'
    ];

    /**
     * Relación con la organización
     */
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    /**
     * Relación con el usuario que creó la asamblea
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó la asamblea
     */
    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'convocada');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'finalizada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha_asamblea', '>=', Carbon::now())
                    ->where('fecha_asamblea', '<=', Carbon::now()->addDays(7));
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Accessors
     */
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'convocada' => 'info',
            'en_proceso' => 'warning',
            'finalizada' => 'success',
            'cancelada' => 'danger',
            default => 'secondary'
        };
    }

    public function getTipoAsambleaColorAttribute(): string
    {
        return match($this->tipo) {
            'ordinaria' => 'primary',
            'extraordinaria' => 'warning',
            'especial' => 'info',
            default => 'secondary'
        };
    }

    public function getTipoAsambleaIconAttribute(): string
    {
        return match($this->tipo) {
            'ordinaria' => 'ri-calendar-line',
            'extraordinaria' => 'ri-calendar-event-line',
            'especial' => 'ri-calendar-check-line',
            default => 'ri-calendar-line'
        };
    }

    public function getDiasRestantesAttribute(): int
    {
        return Carbon::now()->diffInDays($this->fecha_asamblea, false);
    }

    public function getEsProximaAttribute(): bool
    {
        return $this->fecha_asamblea >= Carbon::now() && $this->fecha_asamblea <= Carbon::now()->addDays(7);
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_asamblea < Carbon::now() && $this->estado === 'convocada';
    }

    public function getPorcentajeAsistenciaAttribute(): float
    {
        if ($this->quorum_minimo <= 0) {
            return 0;
        }

        return round(($this->asistentes_count / $this->quorum_minimo) * 100, 1);
    }

    /**
     * Métodos de negocio
     */
    public function iniciar(): bool
    {
        if ($this->estado !== 'convocada') {
            return false;
        }

        $this->update(['estado' => 'en_proceso']);
        return true;
    }

    public function completar(): bool
    {
        if ($this->estado !== 'en_proceso') {
            return false;
        }

        $this->update(['estado' => 'finalizada']);
        return true;
    }

    public function cancelar(): bool
    {
        if (in_array($this->estado, ['finalizada', 'cancelada'])) {
            return false;
        }

        $this->update(['estado' => 'cancelada']);
        return true;
    }

    public function reprogramar(Carbon $nuevaFecha): bool
    {
        if ($this->estado === 'finalizada') {
            return false;
        }

        $this->update(['fecha_asamblea' => $nuevaFecha]);
        return true;
    }

    public function obtenerAsistenciaConfirmada(): int
    {
        return $this->asistentes_count;
    }

    public function cumpleQuorum(): bool
    {
        return $this->quorum_alcanzado;
    }
}