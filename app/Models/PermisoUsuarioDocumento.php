<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoUsuarioDocumento extends Model
{
    use HasFactory;

    protected $table = 'permisos_usuarios_documentos';

    protected $fillable = [
        'usuario_id', 'rol_id', 'ambito', 'seccion_id', 'carpeta_id', 'documento_id',
        'permisos_personalizados', 'fecha_inicio', 'fecha_fin', 'activo', 'asignado_por'
    ];

    protected $casts = [
        'permisos_personalizados' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function rol()
    {
        return $this->belongsTo(RolDocumental::class, 'rol_id');
    }

    public function seccion()
    {
        return $this->belongsTo(SeccionDocumental::class, 'seccion_id');
    }

    public function carpeta()
    {
        return $this->belongsTo(CarpetaDocumental::class, 'carpeta_id');
    }

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function scopeVigentes($query)
    {
        return $query->where('activo', true)
            ->where(function($q) {
                $q->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', now());
            });
    }
}
