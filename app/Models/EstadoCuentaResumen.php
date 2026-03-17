<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCuentaResumen extends Model
{
    use HasFactory;

    protected $table = 'estado_cuenta_resumen';

    protected $fillable = [
        'importacion_excel_id',
        'fuente_datos',
        'id_asesor',
        'cedula',
        'nombre_asesor',
        'anticipos_adiciones',
        'legalizado_devoluciones',
        'sin_legalizar',
        'estado_saldo',
        'valor_liquidacion',
        'fecha_retiro',
        'estado_actual',
        'anticipos_solicitado_anio',
        'total_consignar',
        'anio',
    ];

    protected function casts(): array
    {
        return [
            'fecha_retiro' => 'date',
            'anticipos_adiciones' => 'decimal:2',
            'legalizado_devoluciones' => 'decimal:2',
            'sin_legalizar' => 'decimal:2',
            'valor_liquidacion' => 'decimal:2',
            'anticipos_solicitado_anio' => 'decimal:2',
            'total_consignar' => 'decimal:2',
        ];
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(EstadoCuentaDetalle::class, 'estado_cuenta_resumen_id');
    }
}
