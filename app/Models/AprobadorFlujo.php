<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprobadorFlujo extends Model
{
    use HasFactory;

    protected $table = 'aprobadores_flujo';
    protected $connection = 'pgsql';

    protected $fillable = ['flujo_id', 'usuario_id', 'orden', 'obligatorio'];

    protected $casts = ['obligatorio' => 'boolean'];

    public function flujo()
    {
        return $this->belongsTo(FlujoAprobacion::class, 'flujo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
