<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Asamblea extends Model
{
    use HasFactory;

    protected $table = 'asambleas';

    protected $fillable = [
        'organizacion_id',
        'tipo',
        'titulo',
        'descripcion',
        'fecha_convocatoria',
        'fecha_asamblea',
        'quorum_minimo',
        'lugar',
        'modalidad',
        'enlace_virtual',
        'estado',
        'asistentes_count',
        'quorum_alcanzado',
        'convocatoria_url',
        'acta_url',
        'created_by',
    ];

    protected $casts = [
        'fecha_convocatoria' => 'date',
        'fecha_asamblea' => 'date',
        'quorum_alcanzado' => 'boolean',
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

    public function asistencias(): HasMany
    {
        return $this->hasMany(AsistenciaAsamblea::class);
    }
}