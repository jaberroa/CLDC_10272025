<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoGestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documentos_gestion';

    protected $fillable = [
        'seccion_id',
        'carpeta_id',
        'titulo',
        'slug',
        'descripcion',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'extension',
        'tipo_mime',
        'tamano_bytes',
        'hash_archivo',
        'version',
        'documento_original_id',
        'es_version_actual',
        'estado',
        'requiere_firma',
        'firmado',
        'confidencial',
        'nivel_acceso',
        'ruta_preview',
        'contenido_indexado',
        'procesado',
        'entidad_tipo',
        'entidad_id',
        'fecha_documento',
        'fecha_vencimiento',
        'fecha_revision',
        'fecha_ultimo_acceso',
        'total_descargas',
        'total_visualizaciones',
        'total_compartidos',
        'subido_por',
        'aprobado_por',
        'aprobado_en',
        'actualizado_por',
        'eliminado_por',
    ];

    protected $casts = [
        'version' => 'integer',
        'es_version_actual' => 'boolean',
        'requiere_firma' => 'boolean',
        'firmado' => 'boolean',
        'confidencial' => 'boolean',
        'procesado' => 'boolean',
        'tamano_bytes' => 'integer',
        'total_descargas' => 'integer',
        'total_visualizaciones' => 'integer',
        'total_compartidos' => 'integer',
        'fecha_documento' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_revision' => 'date',
        'fecha_ultimo_acceso' => 'date',
        'aprobado_en' => 'datetime',
    ];

    // Relaciones
    public function seccion()
    {
        return $this->belongsTo(SeccionDocumental::class, 'seccion_id');
    }

    public function carpeta()
    {
        return $this->belongsTo(CarpetaDocumental::class, 'carpeta_id');
    }

    public function documentoOriginal()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_original_id');
    }

    public function versiones()
    {
        return $this->hasMany(VersionDocumento::class, 'documento_id')->orderBy('numero_version', 'desc');
    }

    public function metadatos()
    {
        return $this->hasMany(ValorMetadato::class, 'documento_id');
    }

    public function comparticiones()
    {
        return $this->hasMany(ComparticionDocumento::class, 'documento_id');
    }

    public function aprobaciones()
    {
        return $this->hasMany(AprobacionDocumento::class, 'documento_id');
    }

    public function solicitudesFirma()
    {
        return $this->hasMany(SolicitudFirma::class, 'documento_id');
    }

    public function recordatorios()
    {
        return $this->hasMany(RecordatorioDocumento::class, 'documento_id');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioDocumento::class, 'documento_id')->whereNull('comentario_padre_id');
    }

    public function auditoria()
    {
        return $this->hasMany(AuditoriaDocumento::class, 'documento_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function eliminadoPor()
    {
        return $this->belongsTo(User::class, 'eliminado_por');
    }

    // Relación polimórfica
    public function entidad()
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['borrador', 'revision', 'aprobado']);
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeVersionActual($query)
    {
        return $query->where('es_version_actual', true);
    }

    public function scopeConfidenciales($query)
    {
        return $query->where('confidencial', true);
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<=', now()->addDays($dias))
            ->whereDate('fecha_vencimiento', '>=', now());
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('titulo', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%")
              ->orWhere('contenido_indexado', 'like', "%{$termino}%");
        });
    }

    // Mutators
    public function setTituloAttribute($value)
    {
        $this->attributes['titulo'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Accessors
    public function getTamanoMbAttribute()
    {
        return round($this->tamano_bytes / 1024 / 1024, 2);
    }

    public function getTamanoHumanoAttribute()
    {
        $bytes = $this->tamano_bytes;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->ruta);
    }

    public function getUrlPreviewAttribute()
    {
        return $this->ruta_preview ? Storage::disk('public')->url($this->ruta_preview) : null;
    }

    public function getEsImagenAttribute()
    {
        return in_array(strtolower($this->extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    public function getEsPdfAttribute()
    {
        return strtolower($this->extension) === 'pdf';
    }

    public function getEsDocumentoOficinaAttribute()
    {
        return in_array(strtolower($this->extension), ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
    }

    public function getEstaVencidoAttribute()
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    public function getDiasParaVencerAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }
        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    // Métodos útiles
    public function incrementarDescargas()
    {
        $this->increment('total_descargas');
        $this->update(['fecha_ultimo_acceso' => now()]);
    }

    public function incrementarVisualizaciones()
    {
        $this->increment('total_visualizaciones');
        $this->update(['fecha_ultimo_acceso' => now()]);
    }

    public function crearVersion($archivo, $comentario = null)
    {
        // Guardar archivo
        $nombreArchivo = time() . '_v' . ($this->version + 1) . '_' . $archivo->getClientOriginalName();
        $ruta = $archivo->storeAs("documents/{$this->seccion_id}/{$this->carpeta_id}", $nombreArchivo, 'public');
        
        // Crear registro de versión
        $version = VersionDocumento::create([
            'documento_id' => $this->id,
            'numero_version' => $this->version + 1,
            'nombre_archivo' => $nombreArchivo,
            'ruta' => $ruta,
            'tamano_bytes' => $archivo->getSize(),
            'hash_archivo' => hash_file('sha256', $archivo->getRealPath()),
            'comentario_version' => $comentario,
            'creado_por' => auth()->id(),
        ]);
        
        // Actualizar documento actual
        $this->update([
            'version' => $this->version + 1,
            'nombre_archivo' => $nombreArchivo,
            'ruta' => $ruta,
            'tamano_bytes' => $archivo->getSize(),
        ]);
        
        return $version;
    }

    public function aprobar($aprobadorId = null)
    {
        $this->update([
            'estado' => 'aprobado',
            'aprobado_por' => $aprobadorId ?? auth()->id(),
            'aprobado_en' => now(),
        ]);
    }

    public function duplicar($nuevaCarpetaId = null)
    {
        $nuevo = $this->replicate();
        $nuevo->carpeta_id = $nuevaCarpetaId ?? $this->carpeta_id;
        $nuevo->titulo = $this->titulo . ' (Copia)';
        $nuevo->slug = Str::slug($nuevo->titulo);
        $nuevo->version = 1;
        $nuevo->documento_original_id = null;
        $nuevo->es_version_actual = true;
        $nuevo->subido_por = auth()->id();
        $nuevo->save();
        
        return $nuevo;
    }
}
