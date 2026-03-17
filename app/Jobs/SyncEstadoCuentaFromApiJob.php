<?php

namespace App\Jobs;

use App\Models\SincronizacionApi;
use App\Services\EstadoCuenta\EstadoCuentaSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncEstadoCuentaFromApiJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ?int $userId = null)
    {
    }

    public function handle(EstadoCuentaSyncService $syncService): void
    {
        $sync = SincronizacionApi::query()->create([
            'modulo' => 'estado_cuenta',
            'user_id' => $this->userId,
            'estado' => 'procesando',
            'fecha_inicio' => now(),
        ]);

        $result = $syncService->sync(['trigger' => 'job']);

        $sync->update([
            'estado' => ($result['status'] ?? 'error') === 'ok' ? 'completado' : 'fallido',
            'response_summary' => $result,
            'fecha_fin' => now(),
            'log_error' => $result['message'] ?? null,
        ]);
    }
}
