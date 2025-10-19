<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DocumentoLegal extends Model
{
    use HasFactory;

    protected $table = 'documentos_legales';

    protected $fillable = [
        'organizacion_id',
        'tipo',
        'titulo',
        'descripcion',
        'numero_documento',
        'fecha_emision',
        'fecha_vigencia',
        'archivo_url',
        'activo',
        'created_by',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vigencia' => 'date',
        'activo' => 'boolean',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }
}