<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model

    protected $connection = 'pgsql';
{
    protected $fillable = [
        'miembro_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo_mime',
        'tamaño',
        'extension'
    ];

    public function miembro()
    {
        return $this->belongsTo(Miembro::class);
    }
}
