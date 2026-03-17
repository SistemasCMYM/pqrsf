<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionIntegracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones_integracion';

    protected $fillable = [
        'modulo',
        'fuente_activa',
        'api_base_url',
        'api_token',
        'api_username',
        'api_password',
        'api_timeout',
        'opciones',
        'activo',
    ];

    protected function casts(): array
    {
        return ['opciones' => 'array', 'activo' => 'boolean'];
    }
}
