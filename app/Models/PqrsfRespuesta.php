<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PqrsfRespuesta extends Model
{
    use HasFactory;

    protected $table = 'pqrsf_respuestas';

    protected $fillable = ['pqrsf_id', 'user_id', 'tipo', 'mensaje', 'notificado'];

    protected function casts(): array
    {
        return ['notificado' => 'boolean'];
    }

    public function pqrsf(): BelongsTo
    {
        return $this->belongsTo(Pqrsf::class);
    }
}
