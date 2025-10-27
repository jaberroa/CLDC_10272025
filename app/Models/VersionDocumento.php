<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class VersionDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'versiones_documentos';

    public $timestamps = false;

    protected $fillable = [
        'documento_id',
        'numero_version',
        'nombre_archivo',
        'ruta',
        'tamano_bytes',
        'hash_archivo',
        'comentario_version',
        'tipo_cambio',
        'cambios',
        'activa',
        'descargable',
        'creado_por',
        'creado_en',
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
        'cambios' => 'array',
        'activa' => 'boolean',
        'descargable' => 'boolean',
        'creado_en' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->ruta);
    }

    public function getTamanoMbAttribute()
    {
        return round($this->tamano_bytes / 1024 / 1024, 2);
    }

    public function activar()
    {
        // Desactivar todas las versiones del documento
        self::where('documento_id', $this->documento_id)->update(['activa' => false]);
        
        // Activar esta versión
        $this->update(['activa' => true]);
        
        // Actualizar documento principal
        $this->documento->update([
            'version' => $this->numero_version,
            'nombre_archivo' => $this->nombre_archivo,
            'ruta' => $this->ruta,
            'tamano_bytes' => $this->tamano_bytes,
        ]);
    }
}
