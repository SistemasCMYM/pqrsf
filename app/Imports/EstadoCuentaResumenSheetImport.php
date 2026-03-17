<?php

namespace App\Imports;

use App\Models\EstadoCuentaResumen;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EstadoCuentaResumenSheetImport implements ToCollection, WithHeadingRow
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

            EstadoCuentaResumen::query()->updateOrCreate(
                ['cedula' => $cedula, 'anio' => $this->anio],
                [
                    'importacion_excel_id' => $this->importacionId,
                    'fuente_datos' => 'excel',
                    'id_asesor' => (string) ($row['id_asesor'] ?? ''),
                    'nombre_asesor' => (string) ($row['nombre_asesor'] ?? 'Sin nombre'),
                    'anticipos_adiciones' => $this->toMoney($row['anticipos_adiciones'] ?? 0),
                    'legalizado_devoluciones' => $this->toMoney($row['legalizado_devoluciones'] ?? 0),
                    'sin_legalizar' => $this->toMoney($row['sin_legalizar_2025'] ?? $row['sin_legalizar'] ?? 0),
                    'estado_saldo' => (string) ($row['estado_de_saldo'] ?? ''),
                    'valor_liquidacion' => $this->toMoney($row['valor_liquidacion'] ?? 0),
                    'fecha_retiro' => $this->toDate($row['fecha_retiro'] ?? null),
                    'estado_actual' => (string) ($row['estado_actual'] ?? ''),
                    'anticipos_solicitado_anio' => $this->toMoney($row['anticipos_solicitado_este_ano_2026'] ?? 0),
                    'total_consignar' => $this->toMoney($row['total_a_consignar'] ?? 0),
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
}
