<?php

namespace App\Console\Commands;

use App\Jobs\SyncEstadoCuentaFromApiJob;
use Illuminate\Console\Command;

class SyncEstadoCuentaApiCommand extends Command
{
    protected $signature = 'estado-cuenta:sync-api {--user_id=}';

    protected $description = 'Ejecuta sincronización manual del módulo estado de cuenta con la API externa';

    public function handle(): int
    {
        SyncEstadoCuentaFromApiJob::dispatch($this->option('user_id') ? (int) $this->option('user_id') : null);

        $this->info('Sincronización enviada a cola correctamente.');

        return self::SUCCESS;
    }
}
