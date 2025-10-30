<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAdecuacion extends Model
{
    use HasFactory;

    protected $table = 'estados_adecuacion';
    protected $connection = 'pgsql';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function organizaciones()
    {
        return $this->hasMany(Organizacion::class, 'estado_adecuacion_id');
    }
}


