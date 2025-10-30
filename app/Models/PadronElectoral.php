<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PadronElectoral extends Model
{
    use HasFactory;

    protected $table = 'padrones_electorales';
    protected $connection = 'pgsql';

    protected $fillable = [
        'organizacion_id',
        'eleccion_id',
        'nombre',
        'descripcion',
        'fecha_creacion',
        'fecha_cierre',
        'activo',
        'total_electores',
        'created_by',
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_cierre' => 'date',
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

    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function electores(): HasMany
    {
        return $this->hasMany(Elector::class, 'padron_id');
    }
}