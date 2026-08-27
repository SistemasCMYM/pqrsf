<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EstadoCuentaSigiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $detalle)
    {
    }

    public function collection(): Collection
    {
        return $this->detalle;
    }

    public function headings(): array
    {
        return ['Item', 'Fecha ida', 'Destino', 'Anticipo', 'Legalizado', 'Saldo', 'Estado', 'Fuente'];
    }

    public function map($detalle): array
    {
        $fechaIda = data_get($detalle, 'fecha_ida');

        return [
            data_get($detalle, 'item'),
            $fechaIda instanceof \DateTimeInterface ? $fechaIda->format('Y-m-d') : $fechaIda,
            data_get($detalle, 'municipio_destino'),
            (float) data_get($detalle, 'anticipo', 0),
            (float) data_get($detalle, 'legalizado', 0),
            (float) data_get($detalle, 'saldo_pendiente', 0),
            data_get($detalle, 'estado'),
            data_get($detalle, 'fuente_exportacion'),
        ];
    }
}