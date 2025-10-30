<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SeccionalSubmission extends Model
{
    use HasFactory;

    protected $table = 'seccional_submissions';
    protected $connection = 'pgsql';

    protected $fillable = [
        'organizacion_id',
        'nombre',
        'codigo',
        'tipo',
        'pais',
        'provincia',
        'ciudad',
        'direccion',
        'telefono',
        'email',
        'estado_adecuacion',
        'miembros_minimos',
        'fecha_fundacion',
        'organizacion_padre_id',
        'estatutos_url',
        'actas_fundacion_url',
        'created_by',
    ];

    protected $casts = [
        'fecha_fundacion' => 'date',
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