<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Elector extends Model
{
    use HasFactory;

    protected $table = 'electores';

    protected $fillable = [
        'padron_id',
        'miembro_id',
        'elegible',
        'observaciones',
    ];

    protected $casts = [
        'elegible' => 'boolean',
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

    public function padron(): BelongsTo
    {
        return $this->belongsTo(PadronElectoral::class, 'padron_id');
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }
}