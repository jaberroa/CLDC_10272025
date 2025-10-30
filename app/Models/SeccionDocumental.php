<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SeccionDocumental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'secciones_documentales';
    protected $connection = 'pgsql';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'color',
        'orden',
        'activa',
        'visible_menu',
        'permisos_defecto',
        'requiere_aprobacion',
        'permite_versionado',
        'permite_compartir_externo',
        'max_tamano_archivo_mb',
        'formatos_permitidos',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'visible_menu' => 'boolean',
        'permisos_defecto' => 'array',
        'requiere_aprobacion' => 'boolean',
        'permite_versionado' => 'boolean',
        'permite_compartir_externo' => 'boolean',
        'formatos_permitidos' => 'array',
    ];

    // Relaciones
    public function carpetas()
    {
        return $this->hasMany(CarpetaDocumental::class, 'seccion_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoGestion::class, 'seccion_id');
    }

    public function camposMetadatos()
    {
        return $this->hasMany(CampoMetadato::class, 'seccion_id');
    }

    public function flujosAprobacion()
    {
        return $this->hasMany(FlujoAprobacion::class, 'seccion_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeVisiblesMenu($query)
    {
        return $query->where('visible_menu', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }

    // Mutators
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Accessors
    public function getTotalCarpetasAttribute()
    {
        return $this->carpetas()->count();
    }

    public function getTotalDocumentosAttribute()
    {
        return $this->documentos()->count();
    }

    // Métodos útiles
    public function puedeSubirFormato($extension)
    {
        if (empty($this->formatos_permitidos)) {
            return true; // Si no hay restricción, permite todo
        }
        
        return in_array(strtolower($extension), array_map('strtolower', $this->formatos_permitidos));
    }

    public function puedeSubirTamano($tamanoMB)
    {
        return $tamanoMB <= $this->max_tamano_archivo_mb;
    }
}
