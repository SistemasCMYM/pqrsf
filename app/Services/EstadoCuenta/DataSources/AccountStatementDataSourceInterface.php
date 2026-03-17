<?php

namespace App\Services\EstadoCuenta\DataSources;

interface AccountStatementDataSourceInterface
{
    public function sync(array $context = []): array;

    public function fetchByCedula(string $cedula, array $filters = []): array;
}
