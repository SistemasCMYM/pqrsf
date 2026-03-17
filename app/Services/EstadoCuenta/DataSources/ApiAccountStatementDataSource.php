<?php

namespace App\Services\EstadoCuenta\DataSources;

use App\Models\SincronizacionApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiAccountStatementDataSource implements AccountStatementDataSourceInterface
{
    public function sync(array $context = []): array
    {
        $config = config('account_statement.api');

        try {
            $response = Http::timeout((int) $config['timeout'])
                ->withToken($config['token'])
                ->acceptJson()
                ->get(rtrim((string) $config['base_url'], '/').'/account-statements/sync', $context);

            if (! $response->successful()) {
                Log::warning('Sincronizacion API fallida', ['status' => $response->status(), 'body' => $response->body()]);

                return ['status' => 'error', 'message' => 'La API respondió con error', 'body' => $response->json()];
            }

            return ['status' => 'ok', 'body' => $response->json()];
        } catch (\Throwable $exception) {
            Log::error('Error de conectividad API estado de cuenta', ['error' => $exception->getMessage()]);

            return ['status' => 'error', 'message' => $exception->getMessage()];
        }
    }

    public function fetchByCedula(string $cedula, array $filters = []): array
    {
        $config = config('account_statement.api');

        try {
            $response = Http::timeout((int) $config['timeout'])
                ->withToken($config['token'])
                ->acceptJson()
                ->get(rtrim((string) $config['base_url'], '/').'/account-statements/'.$cedula, $filters);

            if (! $response->successful()) {
                return ['resumen' => null, 'detalle' => collect(), 'error' => 'No se pudo consultar la API'];
            }

            $payload = $response->json();

            return [
                'resumen' => $payload['resumen'] ?? null,
                'detalle' => collect($payload['detalle'] ?? []),
            ];
        } catch (\Throwable $exception) {
            SincronizacionApi::query()->create([
                'modulo' => 'estado_cuenta',
                'estado' => 'error',
                'log_error' => $exception->getMessage(),
                'fecha_inicio' => now(),
                'fecha_fin' => now(),
            ]);

            return ['resumen' => null, 'detalle' => collect(), 'error' => $exception->getMessage()];
        }
    }
}
