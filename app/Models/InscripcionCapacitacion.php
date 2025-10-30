<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InscripcionCapacitacion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones_capacitacion';
    protected $connection = 'pgsql';

    protected $fillable = [
        'capacitacion_id',
        'miembro_id',
        'fecha_inscripcion',
        'asistio',
        'calificacion',
        'certificado_url',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'asistio' => 'boolean',
        'calificacion' => 'decimal:2',
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

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class);
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }
}