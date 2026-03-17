<?php

namespace App\Services\EstadoCuenta;

use App\Models\EstadoCuentaDetalle;
use App\Models\EstadoCuentaResumen;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ExcelAccountStatementImportService
{
    public function import(string $filePath, int $importacionId, int $anio): array
    {
        $sheets = Excel::toArray([], $filePath);

        $summaryRows = 0;
        $detailRows = 0;
        $aggregates = [];

        foreach ($sheets as $sheet) {
            $rows = $this->normalizeSheet($sheet);

            foreach ($rows as $row) {
                $cedula = $this->normalizeCedula($this->pick($row, ['cedula', 'documento', 'numero_documento', 'id_asesor']));
                if (! $cedula) {
                    continue;
                }

                $movementFlag = $this->pick($row, ['municipio_a_visitar', 'fecha_de_ida', 'saldo_pendiente_por_gestionar']);
                if ($movementFlag) {
                    $detailRows++;
                    $this->upsertDetail($row, $importacionId, $anio, $cedula);
                    $this->aggregate($aggregates, $row, $cedula, $anio);
                    continue;
                }

                $summaryRows++;
                $this->upsertSummary($row, $importacionId, $anio, $cedula);
            }
        }

        if (! empty($aggregates)) {
            foreach ($aggregates as $cedula => $payload) {
                $calc = $this->buildCalculatedFields(
                    (float) $payload['anticipos_adiciones'],
                    (float) $payload['legalizado_devoluciones']
                );

                EstadoCuentaResumen::query()->updateOrCreate(
                    ['cedula' => $cedula, 'anio' => $anio],
                    [
                        'importacion_excel_id' => $importacionId,
                        'fuente_datos' => 'excel',
                        'id_asesor' => $payload['id_asesor'],
                        'nombre_asesor' => $payload['nombre_asesor'],
                        'anticipos_adiciones' => $payload['anticipos_adiciones'],
                        'legalizado_devoluciones' => $payload['legalizado_devoluciones'],
                        'sin_legalizar' => $calc['sin_legalizar'],
                        'estado_saldo' => $calc['estado_saldo'],
                        'total_consignar' => $calc['total_consignar'],
                    ]
                );
            }
        }

        return [
            'summary_rows' => $summaryRows,
            'detail_rows' => $detailRows,
            'total_rows' => $summaryRows + $detailRows,
        ];
    }

    private function normalizeSheet(array $sheet): array
    {
        $header = [];
        $output = [];

        foreach ($sheet as $row) {
            if (empty(array_filter($row, fn ($value) => $value !== null && $value !== ''))) {
                continue;
            }

            if ($header === []) {
                $header = array_map(fn ($value) => $this->normalizeHeading((string) $value), $row);
                continue;
            }

            $assoc = [];
            foreach ($header as $index => $key) {
                if (! $key) {
                    continue;
                }
                $assoc[$key] = $row[$index] ?? null;
            }

            $output[] = $assoc;
        }

        return $output;
    }

    private function normalizeHeading(string $value): string
    {
        $value = Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        return $value;
    }

    private function pick(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = Arr::get($row, $key);
            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function normalizeCedula(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $digits = preg_replace('/\\D+/', '', $value);
        if ($digits === null || strlen($digits) < 5) {
            return null;
        }

        return ltrim($digits, '0') ?: $digits;
    }

    private function toMoney(mixed $value): float
    {
        return (float) preg_replace('/[^\d\.-]/', '', (string) $value);
    }

    private function toDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function upsertSummary(array $row, int $importacionId, int $anio, string $cedula): void
    {
        EstadoCuentaResumen::query()->updateOrCreate(
            ['cedula' => $cedula, 'anio' => $anio],
            [
                'importacion_excel_id' => $importacionId,
                'fuente_datos' => 'excel',
                'id_asesor' => $this->pick($row, ['id_asesor']) ?? $cedula,
                'nombre_asesor' => $this->pick($row, ['nombre_asesor']) ?? 'Sin nombre',
                'anticipos_adiciones' => $this->toMoney($row['anticipos_adiciones'] ?? 0),
                'legalizado_devoluciones' => $this->toMoney($row['legalizado_devoluciones'] ?? 0),
                'sin_legalizar' => $this->toMoney($row['sin_legalizar_2025'] ?? $row['sin_legalizar'] ?? 0),
                'estado_saldo' => $this->pick($row, ['estado_de_saldo', 'estado_saldo']) ?? 'N/A',
                'valor_liquidacion' => $this->toMoney($row['valor_liquidacion'] ?? 0),
                'fecha_retiro' => $this->toDate($row['fecha_retiro'] ?? null),
                'estado_actual' => $this->pick($row, ['estado_actual']) ?? 'N/A',
                'anticipos_solicitado_anio' => $this->toMoney($row['anticipos_solicitado_este_ano_2026'] ?? 0),
                'total_consignar' => $this->toMoney($row['total_a_consignar'] ?? 0),
            ]
        );
    }

    private function upsertDetail(array $row, int $importacionId, int $anio, string $cedula): void
    {
        $fechaIda = $this->toDate($row['fecha_de_ida'] ?? null);
        $hash = hash('sha256', implode('|', [
            $cedula,
            (string) ($row['municipio_a_visitar'] ?? ''),
            (string) ($row['fecha_de_ida'] ?? ''),
            (string) ($row['anticipos_adiciones'] ?? 0),
        ]));

        $resumenId = EstadoCuentaResumen::query()->where('cedula', $cedula)->where('anio', $anio)->value('id');

        EstadoCuentaDetalle::query()->updateOrCreate(
            ['hash_registro' => $hash],
            [
                'estado_cuenta_resumen_id' => $resumenId,
                'importacion_excel_id' => $importacionId,
                'fuente_datos' => 'excel',
                'cedula' => $cedula,
                'ciudad_origen' => (string) ($row['ciudad_de_origen'] ?? ''),
                'municipio_destino' => (string) ($row['municipio_a_visitar'] ?? ''),
                'fecha_ida' => $fechaIda,
                'fecha_regreso' => $this->toDate($row['fecha_de_regreso'] ?? null),
                'fecha_pago_anticipo' => $this->toDate($row['fecha_de_pago_anticipo'] ?? $row['fecha_pago_anticipo'] ?? null),
                'mes' => is_numeric($row['mes'] ?? null) ? (int) $row['mes'] : ($fechaIda ? (int) \Carbon\Carbon::parse($fechaIda)->month : null),
                'anio' => $anio,
                'anticipo' => $this->toMoney($row['anticipos_adiciones'] ?? 0),
                'legalizado' => $this->toMoney($row['legalizado_devoluciones'] ?? 0),
                'saldo_pendiente' => $this->toMoney($row['saldo_pendiente_por_gestionar'] ?? 0),
                'estado' => (string) ($row['estado'] ?? ''),
            ]
        );
    }

    private function aggregate(array &$aggregates, array $row, string $cedula, int $anio): void
    {
        $aggregates[$cedula] ??= [
            'id_asesor' => $this->pick($row, ['id_asesor']) ?? $cedula,
            'nombre_asesor' => $this->pick($row, ['nombre_asesor']) ?? $cedula,
            'anticipos_adiciones' => 0,
            'legalizado_devoluciones' => 0,
            'sin_legalizar' => 0,
            'estado_saldo' => 'Pendiente',
            'valor_liquidacion' => 0,
            'fecha_retiro' => null,
            'estado_actual' => 'Activo',
            'anticipos_solicitado_anio' => 0,
            'total_consignar' => 0,
            'anio' => $anio,
        ];

        $aggregates[$cedula]['anticipos_adiciones'] += $this->toMoney($row['anticipos_adiciones'] ?? 0);
        $aggregates[$cedula]['legalizado_devoluciones'] += $this->toMoney($row['legalizado_devoluciones'] ?? 0);
        $aggregates[$cedula]['anticipos_solicitado_anio'] += $this->toMoney($row['anticipos_adiciones'] ?? 0);
    }

    private function buildCalculatedFields(float $anticipos, float $legalizado): array
    {
        $neto = round($anticipos - $legalizado, 2);

        if ($neto > 0) {
            return [
                'sin_legalizar' => $neto,
                'total_consignar' => $neto,
                'estado_saldo' => 'SALDO A FAVOR DE SYSO',
            ];
        }

        if ($neto < 0) {
            return [
                'sin_legalizar' => 0,
                'total_consignar' => 0,
                'estado_saldo' => 'SALDO A FAVOR DEL ASESOR',
            ];
        }

        return [
            'sin_legalizar' => 0,
            'total_consignar' => 0,
            'estado_saldo' => 'SALDO EN $0',
        ];
    }
}
