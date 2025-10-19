<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TransaccionFinanciera extends Model
{
    use HasFactory;

    protected $table = 'transacciones_financieras';

    protected $fillable = [
        'organizacion_id',
        'tipo',
        'categoria',
        'concepto',
        'monto',
        'fecha',
        'comprobante_url',
        'metodo_pago',
        'referencia',
        'aprobado_por',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
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