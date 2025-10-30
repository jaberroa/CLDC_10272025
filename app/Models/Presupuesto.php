<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Presupuesto extends Model
{
    use HasFactory;

    protected $table = 'presupuestos';
    protected $connection = 'pgsql';

    protected $fillable = [
        'organizacion_id',
        'nombre',
        'descripcion',
        'año',
        'monto_presupuestado',
        'monto_ejecutado',
        'activo',
        'fecha_inicio',
        'fecha_fin',
        'created_by',
    ];

    protected $casts = [
        'monto_presupuestado' => 'decimal:2',
        'monto_ejecutado' => 'decimal:2',
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
}