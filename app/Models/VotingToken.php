<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VotingToken extends Model
{
    protected $table = 'voting_tokens';
    protected $connection = 'pgsql';
    protected $primaryKey = 'jti';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'jti',
        'voter_hash',
        'eleccion_id',
        'token_signature',
        'used',
        'issued_at',
        'expires_at',
        'used_at',
        'used_from_ip',
        'user_agent',
        'voto_id',
    ];

    protected $casts = [
        'used' => 'boolean',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($token) {
            if (empty($token->jti)) {
                $token->jti = (string) Str::uuid();
            }
            if (empty($token->issued_at)) {
                $token->issued_at = now();
            }
        });
    }

    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function voto(): BelongsTo
    {
        return $this->belongsTo(Voto::class);
    }

    /**
     * Marcar token como usado de forma atómica
     */
    public static function marcarComoUsado($jti, $votoId, $ip, $userAgent)
    {
        return self::where('jti', $jti)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->update([
                'used' => true,
                'used_at' => now(),
                'voto_id' => $votoId,
                'used_from_ip' => $ip,
                'user_agent' => $userAgent,
            ]) > 0;
    }

    /**
     * Verificar si el token es válido
     */
    public function esValido(): bool
    {
        return !$this->used && 
               $this->expires_at->isFuture() &&
               $this->issued_at->isPast();
    }

    /**
     * Scope para tokens no usados
     */
    public function scopeNoUsados($query)
    {
        return $query->where('used', false);
    }

    /**
     * Scope para tokens expirados
     */
    public function scopeExpirados($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope para tokens vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('issued_at', '<=', now());
    }
}


