<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaVoto extends Model
{
    use HasFactory;

    protected $table = 'auditoria_votos';
    protected $connection = 'pgsql';

    public $timestamps = false;

    protected $fillable = [
        'voto_id',
        'user_id',
        'eleccion_id',
        'accion',
        'detalles',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($auditoria) {
            if (empty($auditoria->created_at)) {
                $auditoria->created_at = now();
            }
        });
    }

    public function voto(): BelongsTo
    {
        return $this->belongsTo(Voto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class);
    }

    /**
     * Registrar acción en auditoría
     */
    public static function registrar($accion, $userId, $eleccionId, $detalles = null, $votoId = null)
    {
        return static::create([
            'voto_id' => $votoId,
            'user_id' => $userId,
            'eleccion_id' => $eleccionId,
            'accion' => $accion,
            'detalles' => is_array($detalles) ? json_encode($detalles) : $detalles,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}


