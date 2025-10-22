<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cargo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
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

    public function miembrosDirectivos(): HasMany
    {
        return $this->hasMany(MiembroDirectivo::class);
    }
}