<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AsistenciaAsamblea extends Model
{
    use HasFactory;

    protected $table = 'asistencia_asambleas';
    protected $connection = 'pgsql';

    protected $fillable = [
        'asamblea_id',
        'miembro_id',
        'estado',
        'confirmada',
        'fecha_confirmacion',
        'fecha_asistencia',
        'hora_llegada',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'confirmada' => 'boolean',
        'fecha_confirmacion' => 'datetime',
        'fecha_asistencia' => 'date',
        'hora_llegada' => 'string'
    ];

    /**
     * Relación con la asamblea
     */
    public function asamblea(): BelongsTo
    {
        return $this->belongsTo(Asamblea::class);
    }

    /**
     * Relación con el miembro
     */
    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    /**
     * Relación con el usuario que creó el registro
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el registro
     */
    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeConfirmadas($query)
    {
        return $query->where('confirmada', true);
    }

    public function scopeNoConfirmadas($query)
    {
        return $query->where('confirmada', false);
    }

    /**
     * Accessors
     */
    public function getEstadoColorAttribute(): string
    {
        return $this->confirmada ? 'success' : 'secondary';
    }

    public function getEstadoTextoAttribute(): string
    {
        return $this->confirmada ? 'Confirmada' : 'Pendiente';
    }

    public function getTiempoTranscurridoAttribute(): string
    {
        if (!$this->fecha_confirmacion) {
            return 'N/A';
        }

        return $this->fecha_confirmacion->diffForHumans();
    }
}