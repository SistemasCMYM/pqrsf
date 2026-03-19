<?php

namespace App\Services\EstadoCuenta\DataSources;

use App\Models\SincronizacionApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ViaticosApiDataSource implements AccountStatementDataSourceInterface
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $scope;
    private int    $timeout;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->baseUrl      = config('account_statement.viaticos.base_url');
        $this->clientId     = config('account_statement.viaticos.client_id');
        $this->clientSecret = config('account_statement.viaticos.client_secret');
        $this->scope        = config('account_statement.viaticos.scope', 'read-anticipos');
        $this->timeout      = (int) config('account_statement.viaticos.timeout', 15);
        $this->username     = config('account_statement.viaticos.username');
        $this->password     = config('account_statement.viaticos.password');
    }

    // -----------------------------------------------------------------
    // No aplica sync para esta fuente (el Excel sí lo usa)
    // -----------------------------------------------------------------
    public function sync(array $context = []): array
    {
        return [
            'status'  => 'ok',
            'message' => 'La fuente Viáticos API no requiere sincronización manual.',
        ];
    }

    // -----------------------------------------------------------------
    // Consulta anticipos pendientes por cédula y mapea al formato
    // que espera EstadoCuentaDashboardController → vista dashboard
    // -----------------------------------------------------------------
    public function fetchByCedula(string $cedula, array $filters = []): array
    {
        try {
            // Log detalles de la consulta
            Log::info('Consultando API Viáticos por identificación', [
                'identificacion_original' => $cedula,
                'identificacion_sanitizada' => $this->sanitizeCedula($cedula),
                'endpoint' => "{$this->baseUrl}/api/v1/asesores/{$cedula}/viaticos-pendientes",
            ]);

            $response = Http::timeout($this->timeout)
                ->withToken($this->getToken())
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/asesores/{$cedula}/viaticos-pendientes");

            Log::info('Respuesta de API Viáticos', [
                'status' => $response->status(),
                'identificacion' => $cedula,
                'response_size' => strlen($response->body()),
            ]);

            // Token expirado → renovar y reintentar una vez
            if ($response->status() === 401) {
                Log::warning('Token expirado, renovando...', ['identificacion' => $cedula]);
                Cache::forget('viaticos_oauth_token');

                $response = Http::timeout($this->timeout)
                    ->withToken($this->getToken())
                    ->acceptJson()
                    ->get("{$this->baseUrl}/api/v1/asesores/{$cedula}/viaticos-pendientes");
            }

            if (! $response->successful()) {
                Log::error('Consulta no exitosa a API Viáticos', [
                    'status' => $response->status(),
                    'identificacion' => $cedula,
                    'body' => $response->body(),
                ]);
                return [
                    'resumen' => null,
                    'detalle' => collect(),
                    'error'   => "No se encontraron datos para la identificación: {$cedula} (HTTP {$response->status()})",
                ];
            }

            $payload = $response->json();
            $items   = $payload ?? [];
            
            // Si la respuesta tiene una estructura diferente, adaptar
            if (isset($payload['data'])) {
                $items = $payload['data'];
            } elseif (!is_array($payload)) {
                $items = [];
            }

            // --- Filtros opcionales que vienen del formulario ---
            $detalle = collect($items)
                ->when($filters['estado'] ?? null, fn ($c, $estado) =>
                    $c->filter(fn ($i) => 
                        str_contains(
                            strtolower($i['viatico']['estado_viatico_id'] ?? ''),
                            strtolower($estado)
                        )
                    )
                )
                ->when($filters['anio'] ?? null, fn ($c, $anio) =>
                    $c->filter(fn ($i) =>
                        Carbon::parse($i['viatico']['fecha_inicio'] ?? null)
                            ->year == $anio
                    )
                )
                ->when($filters['mes'] ?? null, fn ($c, $mes) =>
                    $c->filter(fn ($i) =>
                        Carbon::parse($i['viatico']['fecha_inicio'] ?? null)
                            ->month == $mes
                    )
                )
                // --- Mapeo al formato que espera la vista ---
                ->map(fn ($item) => (object) [
                    'fecha_ida'         => Carbon::parse($item['viatico']['fecha_inicio'] ?? null),
                    'municipio_destino' => is_array($item['viatico']['municipio_destino'] ?? null) 
                        ? (reset($item['viatico']['municipio_destino']) ?: 'N/A')
                        : ($item['viatico']['municipio_destino'] ?? 'N/A'),
                    'anticipo'          => (float) ($item['valores_viatico']['total'] ?? 0),
                    'legalizado'        => (float) ($item['legalizacion']['valor_total'] ?? 0),
                    'saldo_pendiente'   => (float) (($item['legalizacion']['valor_total'] ?? 0) 
                                        - ($item['valores_viatico']['total'] ?? 0)),
                    'estado'            => $this->mapEstadoViatico($item['viatico']['estado_viatico_id'] ?? null),
                ])
                ->values();

            // --- Resumen calculado igual que ExcelAccountStatementDataSource ---
            $resumen = null;

            if ($detalle->isNotEmpty()) {
                $anticipos   = (float) $detalle->sum('anticipo');
                $legalizado  = (float) $detalle->sum('legalizado');
                $neto        = round($anticipos - $legalizado, 2);

                $resumen = (object) [
                    'anticipos_adiciones'     => $anticipos,
                    'legalizado_devoluciones' => $legalizado,
                    'sin_legalizar'           => $neto > 0 ? $neto : 0,
                    'total_consignar'         => $neto > 0 ? $neto : 0,
                    'estado_saldo'            => $neto > 0
                        ? 'SALDO A FAVOR DE SYSO'
                        : ($neto < 0 ? 'SALDO A FAVOR DEL ASESOR' : 'SALDO EN $0'),
                ];
            }

            return [
                'resumen' => $resumen,
                'detalle' => $detalle,
            ];

        } catch (\Throwable $exception) {
            SincronizacionApi::query()->create([
                'modulo'       => 'viaticos_api',
                'estado'       => 'error',
                'log_error'    => $exception->getMessage(),
                'fecha_inicio' => now(),
                'fecha_fin'    => now(),
            ]);

            Log::error('Error ViaticosApiDataSource', ['error' => $exception->getMessage()]);

            return [
                'resumen' => null,
                'detalle' => collect(),
                'error'   => 'Error de conectividad con la API de Viáticos',
            ];
        }
    }

    // -----------------------------------------------------------------
    // Token OAuth2 cacheado 23 horas para no pedirlo en cada consulta
    // -----------------------------------------------------------------
    private function getToken(): string
    {
        return Cache::remember('viaticos_oauth_token', 1380, function () {
            try {
                Log::info('Intentando autenticación OAuth con API de Viáticos', [
                    'url' => "{$this->baseUrl}/oauth/token",
                    'client_id' => $this->clientId,
                    'timeout' => $this->timeout,
                ]);

                $response = Http::timeout($this->timeout)
                    ->post("{$this->baseUrl}/oauth/token", [
                        'grant_type'    => 'password',
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'username'      => $this->username, 
                        'password'      => $this->password, 
                        'scope'         => $this->scope,
                    ]);

                    Log::info('Respuesta de autenticación OAuth', [
                        'status' => $response->status(),
                        'response_size' => strlen($response->body()),
                    ]);

                if ($response->failed()) {
                    $errorMsg = "HTTP {$response->status()}: " . $response->body();
                    Log::error('Fallo en autenticación OAuth de Viáticos', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    throw new \RuntimeException("No se pudo autenticar con la API de Viáticos. {$errorMsg}");
                }

                Log::info('Autenticación OAuth exitosa con API de Viáticos');
                return $response->json('access_token');
            } catch (\Throwable $e) {
                Log::error('Error en OAuth Viáticos', [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                ]);
                throw $e;
            }
        });
    }

    // -----------------------------------------------------------------
    // Sanitizar cédula (remover puntos, espacios, etc.)
    // -----------------------------------------------------------------
    private function sanitizeCedula(string $cedula): string
    {
        return str_replace(['.', '-', ' '], '', trim($cedula));
    }

    // -----------------------------------------------------------------
    // Mapear ID de estado de viático a descripción legible
    // -----------------------------------------------------------------
    private function mapEstadoViatico($estadoId): string
    {
        $estados = [
            1 => 'Radicado',
            2 => 'En Revisión Tesorería',
            3 => 'Rechazado',
            4 => 'En Revisión Gerente',
            5 => 'Aprobado (Pendiente)',
            6 => 'Legalizado',
            7 => 'Cancelado',
        ];

        return $estados[$estadoId] ?? "Estado {$estadoId}";
    }
}