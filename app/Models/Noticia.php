<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Noticia extends Model
{
    use HasFactory;

    protected $table = 'noticias';

    protected $fillable = [
        'titulo',
        'contenido',
        'tipo',
        'estado',
        'fecha_publicacion',
        'autor_id'
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime'
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopePublicadas($query)
    {
        return $query->where('estado', 'publicada');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_publicacion', '>=', now()->subDays($dias));
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getEsPublicadaAttribute()
    {
        return $this->estado === 'publicada';
    }

    public function getEsBorradorAttribute()
    {
        return $this->estado === 'borrador';
    }

    public function getEsArchivadaAttribute()
    {
        return $this->estado === 'archivada';
    }
}
