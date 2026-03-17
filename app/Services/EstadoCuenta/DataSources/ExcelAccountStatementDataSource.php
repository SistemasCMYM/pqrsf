<?php

namespace App\Services\EstadoCuenta\DataSources;

use App\Models\EstadoCuentaDetalle;
use App\Models\EstadoCuentaResumen;

class ExcelAccountStatementDataSource implements AccountStatementDataSourceInterface
{
    public function sync(array $context = []): array
    {
        return [
            'status' => 'ok',
            'message' => 'La fuente excel se sincroniza mediante el proceso de importación.',
            'meta' => $context,
        ];
    }

    public function fetchByCedula(string $cedula, array $filters = []): array
    {
        $resumen = EstadoCuentaResumen::query()
            ->where('cedula', $cedula)
            ->when($filters['anio'] ?? null, fn ($q, $anio) => $q->where('anio', $anio))
            ->latest('anio')
            ->first();

        $detalleQuery = EstadoCuentaDetalle::query()
            ->where('cedula', $cedula)
            ->when($filters['anio'] ?? null, fn ($q, $anio) => $q->where('anio', $anio))
            ->when($filters['mes'] ?? null, fn ($q, $mes) => $q->where('mes', $mes))
            ->when($filters['estado'] ?? null, fn ($q, $estado) => $q->where('estado', $estado))
            ->when($filters['municipio'] ?? null, fn ($q, $municipio) => $q->where('municipio_destino', 'like', "%{$municipio}%"))
            ->orderByDesc('fecha_ida');

        $detalle = (clone $detalleQuery)->get();

        if ($detalle->isNotEmpty()) {
            $anticipos = (float) $detalle->sum('anticipo');
            $legalizado = (float) $detalle->sum('legalizado');
            $neto = round($anticipos - $legalizado, 2);

            $sinLegalizar = $neto > 0 ? $neto : 0;
            $totalConsignar = $neto > 0 ? $neto : 0;
            $estadoSaldo = $neto > 0
                ? 'SALDO A FAVOR DE SYSO'
                : ($neto < 0 ? 'SALDO A FAVOR DEL ASESOR' : 'SALDO EN $0');

            if ($resumen) {
                $resumen->anticipos_adiciones = $anticipos;
                $resumen->legalizado_devoluciones = $legalizado;
                $resumen->sin_legalizar = $sinLegalizar;
                $resumen->total_consignar = $totalConsignar;
                $resumen->estado_saldo = $estadoSaldo;
            }
        }

        return ['resumen' => $resumen, 'detalle' => $detalle];
    }
}
