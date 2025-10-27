<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Directiva extends Model
{
    use HasFactory;

    protected $table = 'directivas';

    protected $fillable = [
        'miembro_id',
        'organo_id',
        'cargo_id',
        'periodo_directiva',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function organo(): BelongsTo
    {
        return $this->belongsTo(Organo::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }

    public function scopePorOrgano($query, $organoId)
    {
        return $query->where('organo_id', $organoId);
    }

    public function scopePorCargo($query, $cargoId)
    {
        return $query->where('cargo_id', $cargoId);
    }

    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_directiva_id', $periodoId);
    }

    public function scopeVigentes($query)
    {
        return $query->where('estado', 'activo')
                    ->where(function ($q) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', now());
                    });
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'activo')
                    ->where('fecha_fin', '<', now());
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'activo' => 'success',
            'inactivo' => 'danger',
            'suspendido' => 'warning',
            default => 'secondary'
        };
    }

    public function getEstadoNombreAttribute()
    {
        return match($this->estado) {
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'suspendido' => 'Suspendido',
            default => 'Desconocido'
        };
    }

    public function getEsVigenteAttribute()
    {
        return $this->estado === 'activo' && 
               ($this->fecha_fin === null || $this->fecha_fin->isFuture());
    }

    public function getEsVencidoAttribute()
    {
        return $this->estado === 'activo' && 
               $this->fecha_fin && $this->fecha_fin->isPast();
    }

    public function getDuracionAttribute()
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return null;
        }
        
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    public function getDuracionFormateadaAttribute()
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 'Sin fecha de fin';
        }
        
        $dias = $this->fecha_inicio->diffInDays($this->fecha_fin);
        
        if ($dias < 30) {
            return "{$dias} días";
        } elseif ($dias < 365) {
            $meses = floor($dias / 30);
            return "{$meses} meses";
        } else {
            $años = floor($dias / 365);
            $meses = floor(($dias % 365) / 30);
            return "{$años} años" . ($meses > 0 ? " {$meses} meses" : "");
        }
    }

    // ========================================
    // MÉTODOS DE NEGOCIO
    // ========================================

    public function activar()
    {
        $this->update(['estado' => 'activo']);
    }

    public function desactivar()
    {
        $this->update(['estado' => 'inactivo']);
    }

    public function suspender()
    {
        $this->update(['estado' => 'suspendido']);
    }

    public function finalizar($fechaFin = null)
    {
        $this->update([
            'estado' => 'inactivo',
            'fecha_fin' => $fechaFin ?? now()
        ]);
    }

    public function renovar($nuevaFechaFin = null)
    {
        $this->update([
            'fecha_fin' => $nuevaFechaFin,
            'estado' => 'activo'
        ]);
    }

    public function puedeSerActivo()
    {
        // Verificar si hay conflicto con otros directivos en el mismo cargo
        $conflicto = self::where('cargo_id', $this->cargo_id)
                         ->where('organo_id', $this->organo_id)
                         ->where('estado', 'activo')
                         ->where('id', '!=', $this->id)
                         ->where(function ($q) {
                             $q->whereNull('fecha_fin')
                               ->orWhere('fecha_fin', '>=', $this->fecha_inicio);
                         })
                         ->exists();
        
        return !$conflicto;
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function estadisticas()
    {
        return [
            'total' => self::count(),
            'activos' => self::activos()->count(),
            'inactivos' => self::inactivos()->count(),
            'vigentes' => self::vigentes()->count(),
            'vencidos' => self::vencidos()->count(),
            'por_organo' => self::selectRaw('organo_id, COUNT(*) as total')
                              ->groupBy('organo_id')
                              ->with('organo')
                              ->get(),
            'por_cargo' => self::selectRaw('cargo_id, COUNT(*) as total')
                              ->groupBy('cargo_id')
                              ->with('cargo')
                              ->get()
        ];
    }

    public static function porOrgano($organoId)
    {
        return self::porOrgano($organoId)->get();
    }

    public static function porCargo($cargoId)
    {
        return self::porCargo($cargoId)->get();
    }

    public static function porPeriodo($periodoId)
    {
        return self::porPeriodo($periodoId)->get();
    }

    public static function directivosActivos()
    {
        return self::vigentes()->with(['miembro', 'organo', 'cargo'])->get();
    }

    public static function proximosVencimientos($dias = 30)
    {
        return self::where('estado', 'activo')
                   ->where('fecha_fin', '<=', now()->addDays($dias))
                   ->where('fecha_fin', '>', now())
                   ->with(['miembro', 'organo', 'cargo'])
                   ->get();
    }
}
