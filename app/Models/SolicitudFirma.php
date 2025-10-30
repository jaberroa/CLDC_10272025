<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudFirma extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes_firma';
    protected $connection = 'pgsql';

    protected $fillable = [
        'documento_id', 'titulo', 'mensaje', 'tipo', 'estado', 'fecha_limite',
        'requiere_orden', 'permite_rechazar', 'total_firmantes', 'firmantes_completados',
        'documento_firmado_ruta', 'completado_en', 'creado_por'
    ];

    protected $casts = [
        'fecha_limite' => 'date',
        'requiere_orden' => 'boolean',
        'permite_rechazar' => 'boolean',
        'completado_en' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function firmantes()
    {
        return $this->hasMany(FirmanteDocumento::class, 'solicitud_id')->orderBy('orden');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function verificarCompletado()
    {
        $firmados = $this->firmantes()->where('estado', 'firmado')->count();
        
        if ($firmados === $this->total_firmantes) {
            $this->update([
                'estado' => 'completado',
                'firmantes_completados' => $firmados,
                'completado_en' => now(),
            ]);
            return true;
        }
        
        return false;
    }
}
