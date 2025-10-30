<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class Voto extends Model
{
    use HasFactory;

    protected $table = 'votos';
    protected $connection = 'pgsql';

    public $timestamps = false; // Solo usamos created_at

    protected $fillable = [
        'eleccion_id',
        'miembro_id',
        'candidato_id',
        'fecha_voto',
    ];

    protected $casts = [
        'fecha_voto' => 'datetime',
    ];

    /**
     * Boot del modelo - Comentado para evitar campos inexistentes
     */
    /*
    protected static function boot()
    {
        parent::boot();

        // Generar hash automáticamente al crear
        static::creating(function ($voto) {
            if (empty($voto->created_at)) {
                $voto->created_at = now();
            }
            
            if (empty($voto->hash)) {
                $voto->hash = static::generarHash(
                    $voto->user_id,
                    $voto->eleccion_id,
                    $voto->candidato_id,
                    $voto->created_at
                );
            }
        });
    }
    */

    /**
     * Generar hash SHA-256 para el voto
     */
    public static function generarHash($userId, $eleccionId, $candidatoId, $timestamp)
    {
        $data = sprintf(
            '%d|%d|%d|%s|%s',
            $userId,
            $eleccionId,
            $candidatoId,
            $timestamp,
            config('app.key')
        );
        
        return hash('sha256', $data);
    }

    /**
     * Verificar integridad del voto
     */
    public function verificarIntegridad(): bool
    {
        $hashCalculado = static::generarHash(
            $this->user_id,
            $this->eleccion_id,
            $this->candidato_id,
            $this->created_at
        );
        
        return $this->hash === $hashCalculado;
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con Elección
     */
    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class, 'eleccion_id');
    }

    /**
     * Relación con Candidato
     */
    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    /**
     * Scope por elección
     */
    public function scopePorEleccion($query, $eleccionId)
    {
        return $query->where('eleccion_id', $eleccionId);
    }

    /**
     * Scope por usuario
     */
    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
