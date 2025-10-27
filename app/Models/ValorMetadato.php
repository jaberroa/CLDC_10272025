<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorMetadato extends Model
{
    use HasFactory;

    protected $table = 'valores_metadatos';

    protected $fillable = [
        'documento_id',
        'campo_id',
        'valor',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoGestion::class, 'documento_id');
    }

    public function campo()
    {
        return $this->belongsTo(CampoMetadato::class, 'campo_id');
    }
}
