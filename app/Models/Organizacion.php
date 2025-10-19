<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
        'pais',
        'provincia',
        'ciudad',
        'direccion',
        'telefono',
        'email',
        'estado_adecuacion',
        'miembros_minimos',
        'fecha_fundacion',
        'organizacion_padre_id',
    ];

    protected $casts = [
        'fecha_fundacion' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Relación con organizaciones hijas
     */
    public function organizacionesHijas(): HasMany
    {
        return $this->hasMany(Organizacion::class, 'organizacion_padre_id');
    }

    /**
     * Relación con organización padre
     */
    public function organizacionPadre(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_padre_id');
    }

    /**
     * Relación con miembros
     */
    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class);
    }

    /**
     * Relación con asambleas
     */
    public function asambleas(): HasMany
    {
        return $this->hasMany(Asamblea::class);
    }

    /**
     * Relación con órganos directivos
     */
    public function organosDirectivos(): HasMany
    {
        return $this->hasMany(OrganoCldc::class);
    }

    /**
     * Relación con cursos
     */
    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    /**
     * Relación con elecciones
     */
    public function elecciones(): HasMany
    {
        return $this->hasMany(Eleccion::class);
    }

    /**
     * Relación con documentos legales
     */
    public function documentosLegales(): HasMany
    {
        return $this->hasMany(DocumentoLegal::class);
    }

    /**
     * Relación con presupuestos
     */
    public function presupuestos(): HasMany
    {
        return $this->hasMany(Presupuesto::class);
    }

    /**
     * Relación con padrones electorales
     */
    public function padronesElectorales(): HasMany
    {
        return $this->hasMany(PadronElectoral::class);
    }

    /**
     * Relación con transacciones financieras
     */
    public function transaccionesFinancieras(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class);
    }

    /**
     * Relación con capacitaciones
     */
    public function capacitaciones(): HasMany
    {
        return $this->hasMany(Capacitacion::class);
    }

    /**
     * Relación con períodos de directiva
     */
    public function periodosDirectiva(): HasMany
    {
        return $this->hasMany(PeriodoDirectiva::class);
    }

    /**
     * Scope para organizaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->whereIn('estado_adecuacion', ['aprobada', 'en_revision']);
    }

    /**
     * Scope para seccionales
     */
    public function scopeSeccionales($query)
    {
        return $query->where('tipo', 'seccional');
    }

    /**
     * Scope para seccionales internacionales
     */
    public function scopeSeccionalesInternacionales($query)
    {
        return $query->where('tipo', 'seccional_internacional');
    }

    /**
     * Obtener estadísticas de la organización
     */
    public function getEstadisticasAttribute()
    {
        return [
            'miembros_activos' => $this->miembros()->where('estado_membresia', 'activa')->count(),
            'asambleas_programadas' => $this->asambleas()->where('estado', 'convocada')->count(),
            'cursos_activos' => $this->cursos()->where('estado', 'programada')->count(),
            'elecciones_activas' => $this->elecciones()->where('estado', 'activa')->count(),
        ];
    }
}
