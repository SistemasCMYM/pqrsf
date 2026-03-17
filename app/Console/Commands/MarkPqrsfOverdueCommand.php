<?php

namespace App\Console\Commands;

use App\Services\Pqrsf\PqrsfService;
use Illuminate\Console\Command;

class MarkPqrsfOverdueCommand extends Command
{
    protected $signature = 'pqrsf:mark-overdue';

    protected $description = 'Marca PQRSF vencidas automáticamente según fecha límite';

    public function handle(PqrsfService $service): int
    {
        $count = $service->markOverdue();

        $this->info("PQRSF vencidas marcadas: {$count}");

        return self::SUCCESS;
    }
}
