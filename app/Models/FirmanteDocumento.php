<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FirmanteDocumento extends Model
{
    use HasFactory;

    protected $table = 'firmantes_documento';
    protected $connection = 'pgsql';

    protected $fillable = [
        'solicitud_id', 'usuario_id', 'email', 'nombre', 'orden', 'estado',
        'razon_rechazo', 'token', 'firma_imagen', 'firma_tipo', 'certificado_digital',
        'fecha_envio', 'fecha_visto', 'fecha_firma', 'ip_firma', 'user_agent',
        'ubicacion_geo', 'metodo_autenticacion', 'recordatorios_enviados', 'ultimo_recordatorio'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_visto' => 'datetime',
        'fecha_firma' => 'datetime',
        'ultimo_recordatorio' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($firmante) {
            if (empty($firmante->token)) {
                $firmante->token = Str::random(64);
            }
        });
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudFirma::class, 'solicitud_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getUrlFirmaAttribute()
    {
        return route('firmas.firmar', $this->token);
    }

    public function firmar($firmaData)
    {
        $this->update([
            'estado' => 'firmado',
            'firma_imagen' => $firmaData['imagen'] ?? null,
            'firma_tipo' => $firmaData['tipo'] ?? 'dibujada',
            'fecha_firma' => now(),
            'ip_firma' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->solicitud->verificarCompletado();
    }
}
