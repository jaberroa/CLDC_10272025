<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComentarioDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'comentarios_documentos';

    protected $fillable = [
        'documento_id', 'comentario_padre_id', 'usuario_id', 'email_externo',
        'nombre_externo', 'contenido', 'menciones', 'archivos_adjuntos', 'pagina',
        'coordenadas', 'resuelto', 'resuelto_por', 'resuelto_en', 'total_respuestas',
        'total_likes'
    ];

    protected $casts = [
        'menciones' => 'array',
        'archivos_adjuntos' => 'array',
        'coordenadas' => 'array',
        'resuelto' => 'boolean',
        'resuelto_en' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function comentarioPadre()
    {
        return $this->belongsTo(ComentarioDocumento::class, 'comentario_padre_id');
    }

    public function respuestas()
    {
        return $this->hasMany(ComentarioDocumento::class, 'comentario_padre_id');
    }

    public function likes()
    {
        return $this->hasMany(LikeComentario::class, 'comentario_id');
    }

    public function resueltoPor()
    {
        return $this->belongsTo(User::class, 'resuelto_por');
    }

    public function scopeNoResueltos($query)
    {
        return $query->where('resuelto', false);
    }
}
