<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CronogramaDirectiva extends Model
{
    use HasFactory;

    protected $table = 'cronogramas_directiva';
    protected $connection = 'pgsql';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'lugar',
        'tipo_evento',
        'estado',
        'organo_id',
        'responsable_id',
        'observaciones',
        'participantes',
        'agenda',
        'requiere_confirmacion',
        'cupo_maximo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'participantes' => 'array',
        'agenda' => 'array',
        'requiere_confirmacion' => 'boolean',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function organo(): BelongsTo
    {
        return $this->belongsTo(Organo::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'responsable_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeProgramados($query)
    {
        return $query->where('estado', 'programado');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    public function scopePorTipoEvento($query, $tipo)
    {
        return $query->where('tipo_evento', $tipo);
    }

    public function scopePorOrgano($query, $organoId)
    {
        return $query->where('organo_id', $organoId);
    }

    public function scopeProximos($query, $dias = 30)
    {
        return $query->where('fecha_inicio', '>=', Carbon::today())
                    ->where('fecha_inicio', '<=', Carbon::today()->addDays($dias))
                    ->where('estado', '!=', 'cancelado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_inicio', '<', Carbon::today())
                    ->where('estado', 'programado');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'programado' => 'primary',
            'en_curso' => 'info',
            'completado' => 'success',
            'cancelado' => 'danger',
            default => 'secondary',
        };
    }

    public function getTipoEventoColorAttribute()
    {
        return match ($this->tipo_evento) {
            'reunion' => 'primary',
            'asamblea' => 'success',
            'capacitacion' => 'info',
            'eleccion' => 'warning',
            'conferencia' => 'purple',
            default => 'secondary',
        };
    }

    public function getTipoEventoIconAttribute()
    {
        return match ($this->tipo_evento) {
            'reunion' => 'ri-team-line',
            'asamblea' => 'ri-group-line',
            'capacitacion' => 'ri-book-open-line',
            'eleccion' => 'ri-vote-line',
            'conferencia' => 'ri-presentation-line',
            default => 'ri-calendar-line',
        };
    }

    // ========================================
    // MÉTODOS DE NEGOCIO
    // ========================================

    public function iniciar()
    {
        $this->update(['estado' => 'en_curso']);
    }

    public function completar()
    {
        $this->update(['estado' => 'completado']);
    }

    public function cancelar()
    {
        $this->update(['estado' => 'cancelado']);
    }

    public function reprogramar(Carbon $nuevaFecha, ?Carbon $nuevaHora = null)
    {
        $this->update([
            'fecha_inicio' => $nuevaFecha,
            'hora_inicio' => $nuevaHora ? $nuevaHora->format('H:i:s') : $this->hora_inicio,
            'estado' => 'programado'
        ]);
    }

    public function estaVencido()
    {
        return $this->fecha_inicio < Carbon::today() && $this->estado === 'programado';
    }

    public function esProximo()
    {
        return $this->fecha_inicio >= Carbon::today() && 
               $this->fecha_inicio <= Carbon::today()->addDays(7) && 
               $this->estado === 'programado';
    }
}
