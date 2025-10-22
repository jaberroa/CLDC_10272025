<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MiembroDirectivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'miembro_id',
        'organo_id',
        'cargo_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'es_presidente',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_presidente' => 'boolean'
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

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function organo(): BelongsTo
    {
        return $this->belongsTo(Organo::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }
}


