<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
        'estado',
        'descripcion',
        'direccion',
        'telefono',
        'email',
        'logo_url'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class);
    }

    public function asambleas(): HasMany
    {
        return $this->hasMany(Asamblea::class);
    }

    public function elecciones(): HasMany
    {
        return $this->hasMany(Eleccion::class);
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    public function transacciones(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class);
    }

    public function presupuestos(): HasMany
    {
        return $this->hasMany(Presupuesto::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoLegal::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getTotalMiembrosAttribute()
    {
        return $this->miembros()->count();
    }

    public function getMiembrosActivosAttribute()
    {
        return $this->miembros()->activos()->count();
    }

    public function getMiembrosVencidosAttribute()
    {
        return $this->miembros()->vencidos()->count();
    }

    public function getMiembrosPorVencerAttribute()
    {
        return $this->miembros()->porVencer()->count();
    }

    // ========================================
    // MÉTODOS DE NEGOCIO
    // ========================================

    public function estadisticas()
    {
        return [
            'total_miembros' => $this->total_miembros,
            'miembros_activos' => $this->miembros_activos,
            'miembros_vencidos' => $this->miembros_vencidos,
            'miembros_por_vencer' => $this->miembros_por_vencer,
            'total_asambleas' => $this->asambleas()->count(),
            'asambleas_activas' => $this->asambleas()->where('estado', 'convocada')->count(),
            'total_elecciones' => $this->elecciones()->count(),
            'elecciones_activas' => $this->elecciones()->where('estado', 'en_proceso')->count()
        ];
    }

    public function esSeccional()
    {
        return $this->tipo === 'seccional';
    }

    public function esNacional()
    {
        return $this->tipo === 'nacional';
    }

    public function esRegional()
    {
        return $this->tipo === 'regional';
    }
}