<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecordatorioDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recordatorios_documentos';
    protected $connection = 'pgsql';

    protected $fillable = [
        'documento_id', 'tipo', 'titulo', 'mensaje', 'usuarios_ids', 'emails_externos',
        'fecha_recordatorio', 'frecuencia', 'dias_anticipacion', 'max_repeticiones',
        'repeticiones_enviadas', 'escalar_sin_respuesta', 'dias_escalacion',
        'usuarios_escalacion', 'escalado', 'fecha_escalacion', 'activo', 'estado',
        'ultimo_envio', 'proximo_envio', 'completado_en', 'prioridad', 'creado_por'
    ];

    protected $casts = [
        'usuarios_ids' => 'array',
        'emails_externos' => 'array',
        'usuarios_escalacion' => 'array',
        'fecha_recordatorio' => 'date',
        'escalar_sin_respuesta' => 'boolean',
        'escalado' => 'boolean',
        'fecha_escalacion' => 'datetime',
        'activo' => 'boolean',
        'ultimo_envio' => 'datetime',
        'proximo_envio' => 'datetime',
        'completado_en' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialRecordatorio::class, 'recordatorio_id');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente')
            ->where('activo', true)
            ->whereDate('proximo_envio', '<=', now());
    }
}
