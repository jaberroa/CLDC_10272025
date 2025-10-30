<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Miembro extends Model
{
    use HasFactory;

    protected $table = 'miembros';
    protected $connection = 'pgsql';

    protected $fillable = [
        'user_id',
        'organizacion_id',
        'numero_carnet',
        'nombre_completo',
        'cedula',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'profesion',
        'estado_membresia_id',
        'tipo_membresia',
        'fecha_ingreso',
        'fecha_vencimiento',
        'foto_url',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function estadoMembresia(): BelongsTo
    {
        return $this->belongsTo(EstadoMembresia::class, 'estado_membresia_id');
    }

    public function cuotas(): HasMany
    {
        return $this->hasMany(CuotaMembresia::class);
    }

    public function directivos(): HasMany
    {
        return $this->hasMany(MiembroDirectivo::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(AsistenciaAsamblea::class);
    }

    public function inscripcionesCursos(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class);
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidato::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }

    public function carnetPersonalizado(): HasOne
    {
        return $this->hasOne(CarnetPersonalizado::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->whereHas('estadoMembresia', function ($q) {
            $q->where('nombre', 'activa');
        });
    }

    public function scopePorOrganizacion($query, $organizacionId)
    {
        return $query->where('organizacion_id', $organizacionId);
    }

    public function scopePorEstado($query, $estadoId)
    {
        return $query->where('estado_membresia_id', $estadoId);
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', now());
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->where('fecha_vencimiento', '>', now());
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    public function getAñosMembresiaAttribute()
    {
        return $this->fecha_ingreso ? $this->fecha_ingreso->diffInYears(now()) : 0;
    }

    public function getEstadoMembresiaNombreAttribute()
    {
        return $this->estadoMembresia?->nombre ?? 'Sin estado';
    }

    public function getEstadoMembresiaColorAttribute()
    {
        return $this->estadoMembresia?->color ?? '#6c757d';
    }

    public function getEsVencidoAttribute()
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    public function getEsPorVencerAttribute()
    {
        return $this->fecha_vencimiento && 
               $this->fecha_vencimiento->isFuture() && 
               $this->fecha_vencimiento->diffInDays(now()) <= 30;
    }

    public function getInicialesAttribute()
    {
        $nombres = explode(' ', $this->nombre_completo);
        $iniciales = '';
        foreach ($nombres as $nombre) {
            if (!empty($nombre)) {
                $iniciales .= strtoupper(substr($nombre, 0, 1));
            }
        }
        return substr($iniciales, 0, 2);
    }

    // ========================================
    // MÉTODOS DE NEGOCIO
    // ========================================

    public function tieneCuotasPendientes()
    {
        return $this->cuotas()->where('estado', 'pendiente')->exists();
    }

    public function cuotasPendientes()
    {
        return $this->cuotas()->where('estado', 'pendiente')->get();
    }

    public function cuotasVencidas()
    {
        return $this->cuotas()->where('estado', 'vencida')->get();
    }

    public function esDirectivo()
    {
        return $this->directivos()->where('estado', 'activo')->exists();
    }

    public function cargosDirectivos()
    {
        return $this->directivos()
                    ->with(['organo', 'cargo'])
                    ->where('estado', 'activo')
                    ->get();
    }

    public function asistenciaAsambleas()
    {
        return $this->asistencias()->where('presente', true)->count();
    }

    public function cursosCompletados()
    {
        return $this->inscripcionesCursos()->where('estado', 'completado')->count();
    }

    public function votosEmitidos()
    {
        return $this->votos()->count();
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function estadisticas()
    {
        return [
            'total' => self::count(),
            'activos' => self::activos()->count(),
            'vencidos' => self::vencidos()->count(),
            'por_vencer' => self::porVencer()->count(),
            'con_cuotas_pendientes' => self::whereHas('cuotas', function ($q) {
                $q->where('estado', 'pendiente');
            })->count(),
            'directivos' => self::whereHas('directivos', function ($q) {
                $q->where('estado', 'activo');
            })->count()
        ];
    }

    public static function porOrganizacion($organizacionId)
    {
        return self::porOrganizacion($organizacionId)->get();
    }

    public static function porEstado($estadoId)
    {
        return self::where('estado_membresia_id', $estadoId)->get();
    }

    /**
     * Genera un número de carnet único para el miembro
     */
    public static function generarNumeroCarnet($organizacionId, $fechaIngreso = null)
    {
        $organizacion = Organizacion::find($organizacionId);
        $prefijo = $organizacion ? strtoupper(substr($organizacion->codigo ?? 'CLDCI', 0, 5)) : 'CLDCI';
        
        $año = $fechaIngreso ? date('Y', strtotime($fechaIngreso)) : date('Y');
        
        // Buscar el último número de carnet para este año
        $ultimoCarnet = self::where('numero_carnet', 'LIKE', $prefijo . '-' . $año . '-%')
            ->orderBy('numero_carnet', 'desc')
            ->first();
        
        if ($ultimoCarnet) {
            // Extraer el número secuencial del último carnet
            $partes = explode('-', $ultimoCarnet->numero_carnet);
            $ultimoNumero = (int) end($partes);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }
        
        return $prefijo . '-' . $año . '-' . str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);
    }
}