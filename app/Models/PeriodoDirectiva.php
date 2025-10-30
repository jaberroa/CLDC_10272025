<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PeriodoDirectiva extends Model
{
    use HasFactory;

    protected $table = 'periodos_directiva';
    protected $connection = 'pgsql';

    protected $fillable = [
        'organizacion_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
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

    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class);
    }
}