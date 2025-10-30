<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprobacionDocumento extends Model
{
    use HasFactory;

    protected $table = 'aprobaciones_documentos';
    protected $connection = 'pgsql';

    protected $fillable = [
        'documento_id', 'flujo_id', 'aprobador_id', 'orden_aprobacion',
        'estado', 'comentarios', 'razon_rechazo', 'delegado_a', 'razon_delegacion',
        'fecha_solicitud', 'fecha_limite', 'fecha_respuesta', 'fecha_escalacion',
        'recordatorios_enviados', 'ultimo_recordatorio', 'ip_aprobacion', 'user_agent'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'fecha_escalacion' => 'datetime',
        'ultimo_recordatorio' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function flujo()
    {
        return $this->belongsTo(FlujoAprobacion::class, 'flujo_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobador_id');
    }

    public function delegadoA()
    {
        return $this->belongsTo(User::class, 'delegado_a');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function aprobar($comentarios = null)
    {
        $this->update([
            'estado' => 'aprobado',
            'comentarios' => $comentarios,
            'fecha_respuesta' => now(),
            'ip_aprobacion' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function rechazar($razon)
    {
        $this->update([
            'estado' => 'rechazado',
            'razon_rechazo' => $razon,
            'fecha_respuesta' => now(),
            'ip_aprobacion' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
