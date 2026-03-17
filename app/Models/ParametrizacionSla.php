<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParametrizacionSla extends Model
{
    use HasFactory;

    protected $table = 'parametrizaciones_sla';

    protected $fillable = ['pqrsf_tipo_id', 'prioridad', 'dias_respuesta', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(PqrsfTipo::class, 'pqrsf_tipo_id');
    }
}
