<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeComentario extends Model
{
    use HasFactory;

    protected $table = 'likes_comentarios';

    protected $fillable = ['comentario_id', 'usuario_id'];

    public function comentario()
    {
        return $this->belongsTo(ComentarioDocumento::class, 'comentario_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
