<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InscripcionCurso extends Model
{
    use HasFactory;

    protected $fillable = [
        'miembro_id',
        'curso_id',
        'fecha_inscripcion',
        'estado',
        'nota_final',
        'certificado_url'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
        'nota_final' => 'decimal:2'
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

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }
}