<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CarpetaDocumental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'carpetas_documentales';

    protected $fillable = [
        'seccion_id',
        'carpeta_padre_id',
        'nombre',
        'slug',
        'descripcion',
        'ruta_completa',
        'nivel',
        'icono',
        'color',
        'orden',
        'activa',
        'publica',
        'solo_lectura',
        'hereda_permisos',
        'permisos_personalizados',
        'total_documentos',
        'tamano_total_bytes',
        'entidad_tipo',
        'entidad_id',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'publica' => 'boolean',
        'solo_lectura' => 'boolean',
        'hereda_permisos' => 'boolean',
        'permisos_personalizados' => 'array',
        'total_documentos' => 'integer',
        'tamano_total_bytes' => 'integer',
    ];

    // Relaciones
    public function seccion()
    {
        return $this->belongsTo(SeccionDocumental::class, 'seccion_id');
    }

    public function carpetaPadre()
    {
        return $this->belongsTo(CarpetaDocumental::class, 'carpeta_padre_id');
    }

    public function subcarpetas()
    {
        return $this->hasMany(CarpetaDocumental::class, 'carpeta_padre_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoGestion::class, 'carpeta_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Relación polimórfica con entidades
    public function entidad()
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopePublicas($query)
    {
        return $query->where('publica', true);
    }

    public function scopeRaiz($query)
    {
        return $query->whereNull('carpeta_padre_id');
    }

    public function scopePorSeccion($query, $seccionId)
    {
        return $query->where('seccion_id', $seccionId);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
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
    public function getTamanoTotalMbAttribute()
    {
        return round($this->tamano_total_bytes / 1024 / 1024, 2);
    }

    public function getRutaCompletaArrayAttribute()
    {
        return explode('/', $this->ruta_completa);
    }

    // Métodos útiles
    public function actualizarRutaCompleta()
    {
        $ruta = [$this->slug];
        
        $carpeta = $this;
        while ($carpeta->carpeta_padre_id) {
            $carpeta = $carpeta->carpetaPadre;
            array_unshift($ruta, $carpeta->slug);
        }
        
        $this->ruta_completa = implode('/', $ruta);
        $this->save();
    }

    public function actualizarEstadisticas()
    {
        $this->total_documentos = $this->documentos()->count();
        $this->tamano_total_bytes = $this->documentos()->sum('tamano_bytes');
        $this->save();
    }

    public function obtenerArbolCompleto()
    {
        return $this->subcarpetas()->with('subcarpetas')->get();
    }

    public function moverA($nuevaCarpetaPadreId)
    {
        $this->carpeta_padre_id = $nuevaCarpetaPadreId;
        $this->nivel = $nuevaCarpetaPadreId ? CarpetaDocumental::find($nuevaCarpetaPadreId)->nivel + 1 : 1;
        $this->save();
        $this->actualizarRutaCompleta();
        
        // Actualizar rutas de subcarpetas
        foreach ($this->subcarpetas as $subcarpeta) {
            $subcarpeta->actualizarRutaCompleta();
        }
    }
}
