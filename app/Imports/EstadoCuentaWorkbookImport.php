<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EstadoCuentaWorkbookImport implements WithMultipleSheets
{
    public function __construct(private readonly int $importacionId, private readonly int $anio)
    {
    }

    public function sheets(): array
    {
        return [
            0 => new EstadoCuentaResumenSheetImport($this->importacionId, $this->anio),
            1 => new EstadoCuentaDetalleSheetImport($this->importacionId, $this->anio),
        ];
    }
}
