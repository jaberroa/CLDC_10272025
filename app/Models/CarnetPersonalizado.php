<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarnetPersonalizado extends Model
{
    use HasFactory;

    protected $table = 'carnet_personalizados';
    protected $connection = 'pgsql';

    protected $fillable = [
        'miembro_id',
        'template_id',
        'personalizacion_json'
    ];

    protected $casts = [
        'personalizacion_json' => 'array'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CarnetTemplate::class, 'template_id');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getPersonalizacionAttribute()
    {
        return $this->personalizacion_json ?? [];
    }

    public function setPersonalizacionAttribute($value)
    {
        $this->personalizacion_json = is_array($value) ? $value : json_decode($value, true);
    }
}