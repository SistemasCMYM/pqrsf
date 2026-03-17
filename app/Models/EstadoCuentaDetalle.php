<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoCuentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'estado_cuenta_detalle';

    protected $fillable = [
        'estado_cuenta_resumen_id',
        'importacion_excel_id',
        'fuente_datos',
        'cedula',
        'ciudad_origen',
        'municipio_destino',
        'fecha_ida',
        'fecha_regreso',
        'fecha_pago_anticipo',
        'mes',
        'anio',
        'anticipo',
        'legalizado',
        'saldo_pendiente',
        'estado',
        'hash_registro',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ida' => 'date',
            'fecha_regreso' => 'date',
            'fecha_pago_anticipo' => 'date',
            'anticipo' => 'decimal:2',
            'legalizado' => 'decimal:2',
            'saldo_pendiente' => 'decimal:2',
        ];
    }

    public function resumen(): BelongsTo
    {
        return $this->belongsTo(EstadoCuentaResumen::class, 'estado_cuenta_resumen_id');
    }
}
