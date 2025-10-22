<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Miembro extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizacion_id',
        'user_id',
        'nombre_completo',
        'email',
        'cedula',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'profesion',
        'estado_membresia_id',
        'fecha_ingreso',
        'fecha_vencimiento',
        'numero_carnet',
        'foto_url',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_vencimiento' => 'date',
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

    /**
     * Relación con organización
     */
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    /**
     * Relación con estado de membresía
     */
    public function estadoMembresia(): BelongsTo
    {
        return $this->belongsTo(EstadoMembresia::class, 'estado_membresia_id');
    }

    /**
     * Relación con usuario Laravel
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con asistencia a asambleas
     */
    public function asistenciaAsambleas(): HasMany
    {
        return $this->hasMany(AsistenciaAsamblea::class);
    }

    /**
     * Relación con miembros directivos
     */
    public function miembrosDirectivos(): HasMany
    {
        return $this->hasMany(MiembroDirectivo::class);
    }

    /**
     * Relación con candidatos
     */
    public function candidatos(): HasMany
    {
        return $this->hasMany(Candidato::class);
    }

    /**
     * Relación con votos emitidos
     */
    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'votante_id');
    }

    /**
     * Relación con inscripciones a cursos
     */
    public function inscripcionesCursos(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class);
    }

    /**
     * Relación con inscripciones a capacitaciones
     */
    public function inscripcionesCapacitacion(): HasMany
    {
        return $this->hasMany(InscripcionCapacitacion::class);
    }

    /**
     * Relación con electores
     */
    public function electores(): HasMany
    {
        return $this->hasMany(Elector::class);
    }

    /**
     * Relación con seccionales (como coordinador)
     */
    public function seccionalesCoordinadas(): HasMany
    {
        return $this->hasMany(Seccional::class, 'coordinador_id');
    }

    /**
     * Scope para miembros activos
     */
    public function scopeActivos($query)
    {
        return $query->whereHas('estadoMembresia', function($q) {
            $q->where('nombre', 'activa');
        });
    }

    /**
     * Scope para miembros por organización
     */
    public function scopePorOrganizacion($query, $organizacionId)
    {
        return $query->where('organizacion_id', $organizacionId);
    }

    /**
     * Scope para búsqueda por nombre o cédula
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre_completo', 'like', "%{$termino}%")
              ->orWhere('cedula', 'like', "%{$termino}%")
              ->orWhere('numero_carnet', 'like', "%{$termino}%");
        });
    }

    /**
     * Obtener cargos actuales del miembro
     */
    public function getCargosActualesAttribute()
    {
        return $this->miembrosDirectivos()
            ->where('estado', 'activo')
            ->with(['cargo', 'organo'])
            ->get();
    }

    /**
     * Obtiene el nombre separado del campo nombre_completo.
     */
    public function getNombreAttribute(): ?string
    {
        if (empty($this->nombre_completo)) {
            return null;
        }

        $partes = preg_split('/\s+/', trim($this->nombre_completo), -1, PREG_SPLIT_NO_EMPTY);

        return $partes[0] ?? null;
    }

    /**
     * Obtiene los apellidos separados del campo nombre_completo.
     */
    public function getApellidoAttribute(): ?string
    {
        if (empty($this->nombre_completo)) {
            return null;
        }

        $partes = preg_split('/\s+/', trim($this->nombre_completo), -1, PREG_SPLIT_NO_EMPTY);

        if (count($partes) <= 1) {
            return null;
        }

        return implode(' ', array_slice($partes, 1));
    }

    /**
     * Verificar si el miembro es presidente
     */
    public function esPresidente()
    {
        return $this->miembrosDirectivos()
            ->where('estado', 'activo')
            ->where('es_presidente', true)
            ->exists();
    }

    /**
     * Obtener estadísticas del miembro
     */
    public function getEstadisticasAttribute()
    {
        return [
            'asambleas_asistidas' => $this->asistenciaAsambleas()->where('presente', true)->count(),
            'cursos_completados' => $this->inscripcionesCursos()->where('estado', 'completo')->count(),
            'votos_emitidos' => $this->votos()->count(),
            'cargos_actuales' => $this->miembrosDirectivos()->where('estado', 'activo')->count(),
        ];
    }

    /**
     * Generar número de carnet automáticamente
     */
    public static function generarNumeroCarnet($organizacionId, $fechaReferencia = null)
    {
        $organizacion = Organizacion::find($organizacionId);
        $prefijo = $organizacion->codigo ?? 'CLDCI';
        $fecha = $fechaReferencia ? Carbon::parse($fechaReferencia) : now();
        $año = $fecha->year;

        $ultimoNumero = self::where('organizacion_id', $organizacionId)
            ->whereYear('fecha_ingreso', $año)
            ->max('numero_carnet');

        if ($ultimoNumero) {
            $secuencia = (int) substr($ultimoNumero, strrpos($ultimoNumero, '-') + 1);
            $secuencia++;
        } else {
            $secuencia = 1;
        }

        return "{$prefijo}-{$año}-" . str_pad($secuencia, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relaciones con cuotas
     */
    public function cuotas(): HasMany
    {
        return $this->hasMany(CuotaMembresia::class);
    }

    public function cuotasPendientes(): HasMany
    {
        return $this->hasMany(CuotaMembresia::class)->where('estado', 'pendiente');
    }

    public function cuotasPagadas(): HasMany
    {
        return $this->hasMany(CuotaMembresia::class)->where('estado', 'pagada');
    }

    public function cuotasVencidas(): HasMany
    {
        return $this->hasMany(CuotaMembresia::class)->where('estado', 'vencida');
    }

    /**
     * Verificar si tiene cuotas pendientes
     */
    public function tieneCuotasPendientes(): bool
    {
        return $this->cuotasPendientes()->exists();
    }

    /**
     * Verificar si tiene cuotas vencidas
     */
    public function tieneCuotasVencidas(): bool
    {
        return $this->cuotasVencidas()->exists();
    }
}
