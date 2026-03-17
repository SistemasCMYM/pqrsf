<?php

namespace App\Services\EstadoCuenta;

use App\Services\EstadoCuenta\DataSources\AccountStatementDataSourceInterface;

class EstadoCuentaSyncService
{
    public function __construct(private readonly AccountStatementDataSourceInterface $dataSource)
    {
    }

    public function sync(array $context = []): array
    {
        return $this->dataSource->sync($context);
    }

    public function consultByCedula(string $cedula, array $filters = []): array
    {
        return $this->dataSource->fetchByCedula($cedula, $filters);
    }
}
