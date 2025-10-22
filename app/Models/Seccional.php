<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Seccional extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'telefono',
        'email',
        'coordinador_id',
        'activa'
    ];

    protected $casts = [
        'activa' => 'boolean'
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

    public function coordinador(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'coordinador_id');
    }

    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class);
    }
}


