<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaDocumento extends Model
{
    use HasFactory;

    protected $table = 'auditoria_documentos';
    protected $connection = 'pgsql';
    public $timestamps = false;

    protected $fillable = [
        'documento_id', 'carpeta_id', 'seccion_id', 'usuario_id', 'email_usuario',
        'nombre_usuario', 'accion', 'entidad_tipo', 'entidad_id', 'descripcion',
        'datos_anteriores', 'datos_nuevos', 'metadatos', 'ip', 'user_agent',
        'ubicacion_geo', 'dispositivo', 'navegador', 'resultado', 'mensaje_error',
        'nivel', 'sospechosa', 'fecha_accion'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'metadatos' => 'array',
        'sospechosa' => 'boolean',
        'fecha_accion' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar($accion, $entidad, $descripcion = null, $datosAdicionales = [])
    {
        return self::create([
            'accion' => $accion,
            'entidad_tipo' => get_class($entidad),
            'entidad_id' => $entidad->id,
            'usuario_id' => auth()->id(),
            'email_usuario' => auth()->user()->email ?? null,
            'nombre_usuario' => auth()->user()->name ?? null,
            'descripcion' => $descripcion,
            'metadatos' => $datosAdicionales,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'resultado' => 'exito',
            'nivel' => 'info',
            'fecha_accion' => now(),
        ]);
    }
}
