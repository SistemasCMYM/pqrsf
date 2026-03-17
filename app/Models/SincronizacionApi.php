<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SincronizacionApi extends Model
{
    use HasFactory;

    protected $table = 'sincronizaciones_api';

    protected $fillable = [
        'modulo',
        'user_id',
        'estado',
        'total_registros',
        'procesados',
        'fallidos',
        'request_payload',
        'response_summary',
        'log_error',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_summary' => 'array',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
        ];
    }
}
