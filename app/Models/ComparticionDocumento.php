<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ComparticionDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'comparticion_documentos';
    protected $connection = 'pgsql';

    protected $fillable = [
        'documento_id',
        'tipo',
        'usuario_id',
        'email_externo',
        'nombre_externo',
        'token',
        'password_hash',
        'puede_ver',
        'puede_descargar',
        'puede_comentar',
        'puede_editar',
        'fecha_expiracion',
        'max_accesos',
        'accesos_actuales',
        'requiere_autenticacion',
        'notificar_acceso',
        'activa',
        'ultimo_acceso',
        'ultima_ip',
        'compartido_por',
        'mensaje',
    ];

    protected $casts = [
        'puede_ver' => 'boolean',
        'puede_descargar' => 'boolean',
        'puede_comentar' => 'boolean',
        'puede_editar' => 'boolean',
        'fecha_expiracion' => 'datetime',
        'max_accesos' => 'integer',
        'accesos_actuales' => 'integer',
        'requiere_autenticacion' => 'boolean',
        'notificar_acceso' => 'boolean',
        'activa' => 'boolean',
        'ultimo_acceso' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comparticion) {
            if (empty($comparticion->token)) {
                $comparticion->token = Str::random(64);
            }
        });
    }

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function compartidoPor()
    {
        return $this->belongsTo(User::class, 'compartido_por');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)
            ->where(function($q) {
                $q->whereNull('fecha_expiracion')
                  ->orWhere('fecha_expiracion', '>', now());
            })
            ->where(function($q) {
                $q->whereNull('max_accesos')
                  ->orWhereRaw('accesos_actuales < max_accesos');
            });
    }

    public function scopeExternas($query)
    {
        return $query->where('tipo', 'externo');
    }

    public function getEstaVigenteAttribute()
    {
        if (!$this->activa) return false;
        if ($this->fecha_expiracion && $this->fecha_expiracion->isPast()) return false;
        if ($this->max_accesos && $this->accesos_actuales >= $this->max_accesos) return false;
        return true;
    }

    public function getUrlCompartidaAttribute()
    {
        return route('documentos.compartido', $this->token);
    }

    public function registrarAcceso($ip = null)
    {
        $this->increment('accesos_actuales');
        $this->update([
            'ultimo_acceso' => now(),
            'ultima_ip' => $ip ?? request()->ip(),
        ]);

        if ($this->notificar_acceso) {
            // TODO: Enviar notificación
        }
    }
}
