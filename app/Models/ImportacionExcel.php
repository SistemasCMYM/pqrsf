<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacionExcel extends Model
{
    use HasFactory;

    protected $table = 'importaciones_excel';

    protected $fillable = [
        'modulo',
        'user_id',
        'nombre_archivo',
        'ruta_archivo',
        'estado',
        'total_registros',
        'procesados',
        'fallidos',
        'log_error',
        'fecha_importacion',
    ];

    protected function casts(): array
    {
        return ['fecha_importacion' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
