<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RolDocumental extends Model
{
    use HasFactory;

    protected $table = 'roles_documentales';
    protected $connection = 'pgsql';

    protected $fillable = [
        'nombre', 'slug', 'descripcion', 'permisos', 'nivel_acceso',
        'es_sistema', 'activo', 'creado_por'
    ];

    protected $casts = [
        'permisos' => 'array',
        'es_sistema' => 'boolean',
        'activo' => 'boolean',
    ];

    public function permisos()
    {
        return $this->hasMany(PermisoUsuarioDocumento::class, 'rol_id');
    }

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function tienePermiso($permiso)
    {
        return in_array($permiso, $this->permisos ?? []);
    }
}
