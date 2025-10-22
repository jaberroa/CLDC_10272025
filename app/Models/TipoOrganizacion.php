<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoOrganizacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_organizacion';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function organizaciones()
    {
        return $this->hasMany(Organizacion::class, 'tipo_organizacion_id');
    }
}


