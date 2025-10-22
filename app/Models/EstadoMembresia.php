<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoMembresia extends Model
{
    use HasFactory;

    protected $table = 'estados_membresia';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function miembros()
    {
        return $this->hasMany(Miembro::class, 'estado_membresia_id');
    }
}


