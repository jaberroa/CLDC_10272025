<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Eleccion extends Model
{
    use HasFactory;

    protected $table = 'elecciones';

    protected $fillable = [
        'organizacion_id',
        'titulo',
        'descripcion',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'votos_totales',
        'votacion_abierta',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'votacion_abierta' => 'boolean',
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

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }
}