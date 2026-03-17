<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PqrsfAdjunto extends Model
{
    use HasFactory;

    protected $table = 'pqrsf_adjuntos';

    protected $fillable = ['pqrsf_id', 'cargado_por', 'nombre_original', 'ruta', 'mime_type', 'tamano'];

    public function pqrsf(): BelongsTo
    {
        return $this->belongsTo(Pqrsf::class);
    }
}
