<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Voto extends Model
{
    use HasFactory;

    protected $table = 'votos';

    protected $fillable = [
        'eleccion_id',
        'elector_id',
        'candidato_id',
        'fecha_voto',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'fecha_voto' => 'datetime',
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

    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function elector(): BelongsTo
    {
        return $this->belongsTo(Elector::class);
    }
}