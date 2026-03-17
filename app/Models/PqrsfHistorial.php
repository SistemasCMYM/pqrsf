<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PqrsfHistorial extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'pqrsf_historial';

    protected $fillable = [
        'pqrsf_id',
        'user_id',
        'estado_anterior_id',
        'estado_nuevo_id',
        'responsable_anterior_id',
        'responsable_nuevo_id',
        'accion',
        'observacion',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function pqrsf(): BelongsTo
    {
        return $this->belongsTo(Pqrsf::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
