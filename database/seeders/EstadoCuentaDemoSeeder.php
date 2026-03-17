<?php

namespace Database\Seeders;

use App\Models\ConfiguracionIntegracion;
use App\Models\EstadoCuentaDetalle;
use App\Models\EstadoCuentaResumen;
use App\Models\User;
use Illuminate\Database\Seeder;

class EstadoCuentaDemoSeeder extends Seeder
{
    public function run(): void
    {
        ConfiguracionIntegracion::query()->updateOrCreate(
            ['modulo' => 'estado_cuenta'],
            ['fuente_activa' => 'excel', 'activo' => true, 'api_timeout' => 15]
        );

        $external = User::query()->where('email', 'asesor@pqrsf.local')->first();
        if (! $external) {
            return;
        }

        $resumen = EstadoCuentaResumen::query()->updateOrCreate(
            ['cedula' => '123456789', 'anio' => 2026],
            [
                'fuente_datos' => 'excel',
                'id_asesor' => 'A-100',
                'nombre_asesor' => 'Asesor Externo',
                'anticipos_adiciones' => 3500000,
                'legalizado_devoluciones' => 3200000,
                'sin_legalizar' => 300000,
                'estado_saldo' => 'Saldo a favor de SYSO',
                'valor_liquidacion' => 280000,
                'estado_actual' => 'Activo',
                'anticipos_solicitado_anio' => 4100000,
                'total_consignar' => 280000,
            ]
        );

        EstadoCuentaDetalle::query()->updateOrCreate(
            ['hash_registro' => hash('sha256', 'demo-1')],
            [
                'estado_cuenta_resumen_id' => $resumen->id,
                'fuente_datos' => 'excel',
                'cedula' => '123456789',
                'ciudad_origen' => 'Bogotá',
                'municipio_destino' => 'Medellín',
                'fecha_ida' => '2026-02-10',
                'fecha_regreso' => '2026-02-13',
                'fecha_pago_anticipo' => '2026-02-08',
                'mes' => 2,
                'anio' => 2026,
                'anticipo' => 1000000,
                'legalizado' => 900000,
                'saldo_pendiente' => 100000,
                'estado' => 'Pendiente',
            ]
        );
    }
}
