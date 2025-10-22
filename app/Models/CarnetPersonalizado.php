<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarnetPersonalizado extends Model
{
    use HasFactory;

    protected $fillable = [
        'miembro_id',
        'template_id',
        'color_primario',
        'color_secundario',
        'color_fondo',
        'color_texto',
        'fuente_familia',
        'tamaño_nombre',
        'tamaño_profesion',
        'tamaño_organizacion',
        'nombre_negrita',
        'nombre_cursiva',
        'profesion_negrita',
        'profesion_cursiva',
        'datos_personalizados',
        'activo'
    ];

    protected $casts = [
        'datos_personalizados' => 'array',
        'nombre_negrita' => 'boolean',
        'nombre_cursiva' => 'boolean',
        'profesion_negrita' => 'boolean',
        'profesion_cursiva' => 'boolean',
        'activo' => 'boolean'
    ];

    public function miembro()
    {
        return $this->belongsTo(Miembro::class);
    }

    public function template()
    {
        return $this->belongsTo(CarnetTemplate::class, 'template_id');
    }

    public function getEstiloNombreAttribute()
    {
        $estilo = "font-size: {$this->tamaño_nombre}px;";
        $estilo .= "font-family: {$this->fuente_familia};";
        
        if ($this->nombre_negrita) {
            $estilo .= "font-weight: bold;";
        }
        
        if ($this->nombre_cursiva) {
            $estilo .= "font-style: italic;";
        }
        
        return $estilo;
    }

    public function getEstiloProfesionAttribute()
    {
        $estilo = "font-size: {$this->tamaño_profesion}px;";
        $estilo .= "font-family: {$this->fuente_familia};";
        
        if ($this->profesion_negrita) {
            $estilo .= "font-weight: bold;";
        }
        
        if ($this->profesion_cursiva) {
            $estilo .= "font-style: italic;";
        }
        
        return $estilo;
    }
}