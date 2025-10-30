<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialRecordatorio extends Model
{
    use HasFactory;

    protected $table = 'historial_recordatorios';
    protected $connection = 'pgsql';

    protected $fillable = [
        'recordatorio_id', 'destinatario_email', 'destinatario_nombre', 'estado',
        'mensaje_error', 'fecha_envio', 'fecha_apertura', 'fecha_click', 'ip_apertura'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_apertura' => 'datetime',
        'fecha_click' => 'datetime',
    ];

    public function recordatorio()
    {
        return $this->belongsTo(RecordatorioDocumento::class, 'recordatorio_id');
    }
}
