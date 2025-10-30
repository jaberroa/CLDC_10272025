<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuotaHistorial extends Model
{
    use HasFactory;

    protected $table = 'cuotas_historial';
    protected $connection = 'pgsql';

    protected $fillable = [
        'cuota_id',
        'estado_anterior',
        'estado_nuevo',
        'user_id',
        'motivo',
    ];
}


