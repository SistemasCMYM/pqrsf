<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EstadoCuentaGeneralExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $usuarios)
    {
    }

    public function collection(): Collection
    {
        return $this->usuarios;
    }

    public function headings(): array
    {
        return ['Usuario', 'Nombre', 'Cédula', 'Total anticipado', 'Total legalizado', 'Estado', 'Saldo'];
    }

    public function map($usuario): array
    {
        return [
            data_get($usuario, 'usuario'),
            data_get($usuario, 'nombre'),
            data_get($usuario, 'cedula'),
            (float) data_get($usuario, 'total_anticipado', 0),
            (float) data_get($usuario, 'total_legalizado', 0),
            data_get($usuario, 'estado'),
            (float) data_get($usuario, 'saldo', 0),
        ];
    }
}