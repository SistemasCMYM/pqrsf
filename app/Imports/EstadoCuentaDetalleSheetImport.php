<?php

namespace App\Imports;

use App\Models\EstadoCuentaDetalle;
use App\Models\EstadoCuentaResumen;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EstadoCuentaDetalleSheetImport implements ToCollection, WithHeadingRow
{
    public function __construct(private readonly int $importacionId, private readonly int $anio)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $cedula = trim((string) ($row['cedula'] ?? $row['documento'] ?? ''));
            if ($cedula === '') {
                continue;
            }

            $resumenId = EstadoCuentaResumen::query()->where('cedula', $cedula)->where('anio', $this->anio)->value('id');
            $fechaIda = $this->toDate($row['fecha_de_ida'] ?? null);

            $hash = hash('sha256', implode('|', [
                $cedula,
                (string) ($row['municipio_a_visitar'] ?? ''),
                (string) ($row['fecha_de_ida'] ?? ''),
                (string) ($row['anticipos_adiciones'] ?? 0),
            ]));

            EstadoCuentaDetalle::query()->updateOrCreate(
                ['hash_registro' => $hash],
                [
                    'estado_cuenta_resumen_id' => $resumenId,
                    'importacion_excel_id' => $this->importacionId,
                    'fuente_datos' => 'excel',
                    'cedula' => $cedula,
                    'ciudad_origen' => (string) ($row['ciudad_de_origen'] ?? ''),
                    'municipio_destino' => (string) ($row['municipio_a_visitar'] ?? ''),
                    'fecha_ida' => $fechaIda,
                    'fecha_regreso' => $this->toDate($row['fecha_de_regreso'] ?? null),
                    'fecha_pago_anticipo' => $this->toDate($row['fecha_pago_anticipo'] ?? null),
                    'mes' => $this->toMonth($row['mes'] ?? null, $fechaIda),
                    'anio' => $this->anio,
                    'anticipo' => $this->toMoney($row['anticipos_adiciones'] ?? 0),
                    'legalizado' => $this->toMoney($row['legalizado_devoluciones'] ?? 0),
                    'saldo_pendiente' => $this->toMoney($row['saldo_pendiente_por_gestionar'] ?? 0),
                    'estado' => (string) ($row['estado'] ?? ''),
                ]
            );
        }
    }

    private function toMoney(mixed $value): float
    {
        return (float) preg_replace('/[^\d\.-]/', '', (string) $value);
    }

    private function toDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function toMonth(mixed $value, ?string $fallbackDate): ?int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return (int) \Carbon\Carbon::parse('1 '.$value.' '.$this->anio)->month;
            } catch (\Throwable) {
            }
        }

        if ($fallbackDate) {
            return (int) \Carbon\Carbon::parse($fallbackDate)->month;
        }

        return null;
    }
}
