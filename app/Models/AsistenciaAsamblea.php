<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AsistenciaAsamblea extends Model
{
    use HasFactory;

    protected $table = 'asistencia_asambleas';

    protected $fillable = [
        'asamblea_id',
        'miembro_id',
        'presente',
        'fecha_asistencia',
        'observaciones',
    ];

    protected $casts = [
        'fecha_asistencia' => 'datetime',
        'presente' => 'boolean',
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

    public function asamblea(): BelongsTo
    {
        return $this->belongsTo(Asamblea::class);
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }
}