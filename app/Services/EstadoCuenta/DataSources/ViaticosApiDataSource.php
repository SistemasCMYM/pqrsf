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

    public function fetchTotalsByCedula(string $cedula): ?array
    {
        $cacheKey = 'viaticos_totals_'.md5($this->sanitizeCedula($cedula));
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        try {
            $path = str_replace(
                '{cedula}',
                $this->sanitizeCedula($cedula),
                (string) config('account_statement.viaticos.totals_path')
            );

            $url = rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
            $maxRetries = max(1, (int) config('account_statement.viaticos.totals_max_retries', 3));
            $response = null;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                $response = Http::timeout($this->timeout)
                    ->withToken($this->getToken())
                    ->acceptJson()
                    ->get($url);

                if ($response->status() === 401) {
                    Cache::forget('viaticos_oauth_token');
                    continue;
                }

                if ($response->status() !== 429 || $attempt === $maxRetries) {
                    break;
                }

                $retryAfter = (int) $response->header('Retry-After', 0);
                $delaySeconds = $retryAfter > 0 ? $retryAfter : $attempt;
                sleep($delaySeconds);
            }

            if ($response?->status() === 401) {
                $response = Http::timeout($this->timeout)
                    ->withToken($this->getToken())
                    ->acceptJson()
                    ->get($url);
            }

            if (! $response || ! $response->successful()) {
                Log::warning('No se pudieron obtener totales SIGI', [
                    'cedula' => $cedula,
                    'status' => $response?->status(),
                ]);

                return null;
            }

            $data = $response->json('data', []);

            $totals = [
                'nombre' => trim((string) ($data['nombre'] ?? '').' '.($data['apellido'] ?? '')),
                'total_anticipado' => (float) ($data['valores_totales'] ?? 0),
                'total_legalizado' => (float) ($data['valores_legalizados'] ?? $data['valoresLegalizados'] ?? 0),
            ];

            Cache::put(
                $cacheKey,
                $totals,
                now()->addMinutes((int) config('account_statement.viaticos.totals_cache_minutes', 10))
            );

            return $totals;
        } catch (\Throwable $exception) {
            Log::warning('Error consultando totales SIGI', [
                'cedula' => $cedula,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    // -----------------------------------------------------------------
    // Consulta anticipos pendientes por cédula y mapea al formato
    // que espera EstadoCuentaDashboardController → vista dashboard
    // -----------------------------------------------------------------
    public function fetchByCedula(string $cedula, array $filters = []): array
{
    try {
        $cedulaSanitizada = $this->sanitizeCedula($cedula);
        
        Log::info('Consultando API Viáticos por identificación', [
            'identificacion_original' => $cedula,
            'identificacion_sanitizada' => $cedulaSanitizada,
            'endpoint' => "{$this->baseUrl}/api/v1/asesores/{$cedulaSanitizada}/viaticos-pendientes",
        ]);

        $response = Http::timeout($this->timeout)
            ->withToken($this->getToken())
            ->acceptJson()
            ->get("{$this->baseUrl}/api/v1/asesores/{$cedulaSanitizada}/viaticos-pendientes");

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
                ->get("{$this->baseUrl}/api/v1/asesores/{$cedulaSanitizada}/viaticos-pendientes");
        }

        if ($response->status() === 500) {
            Log::error('Error 500 en API Viáticos', [
                'identificacion' => $cedula,
                'url' => "{$this->baseUrl}/api/v1/asesores/{$cedulaSanitizada}/viaticos-pendientes",
                'body' => $response->body(),
            ]);
            
            return [
                'resumen' => null,
                'detalle' => collect(),
                'dbResumen' => null,
                'dbDetalle' => collect(),
                'error' => "El servicio de viáticos no está disponible en este momento. Por favor, intente más tarde.",
            ];
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
                'dbResumen' => null,
                'dbDetalle' => collect(),
                'error' => "No se encontraron datos para la identificación: {$cedula} (HTTP {$response->status()})",
            ];
        }

        $payload = $response->json();
        $items = $payload['data'] ?? $payload ?? [];

        // --- Filtros opcionales ---
        $detalle = collect($items)
            ->when($filters['estado'] ?? null, function ($c, $estado) {
                $estadoBuscar = strtolower(trim($estado));
                return $c->filter(function ($item) use ($estadoBuscar) {
                    $estadoActual = strtolower($this->mapEstadoViatico($item['viatico']['estado_viatico_id'] ?? null));
                    return str_contains($estadoActual, $estadoBuscar);
                });
            })
            ->when($filters['anio'] ?? null, function ($c, $anio) {
                return $c->filter(function ($item) use ($anio) {
                    $fechaInicio = $item['viatico']['fecha_inicio'] ?? null;
                    if (!$fechaInicio) return false;
                    return Carbon::parse($fechaInicio)->year == $anio;
                });
            })
            ->when($filters['mes'] ?? null, function ($c, $mes) {
                return $c->filter(function ($item) use ($mes) {
                    $fechaInicio = $item['viatico']['fecha_inicio'] ?? null;
                    if (!$fechaInicio) return false;
                    return Carbon::parse($fechaInicio)->month == $mes;
                });
            })
            ->when($filters['municipio'] ?? null, function ($c, $municipio) {
                $municipioBuscar = strtolower(trim($municipio));
                return $c->filter(function ($item) use ($municipioBuscar) {
                    $municipioDestino = is_array($item['viatico']['municipio_destino'] ?? null) 
                        ? reset($item['viatico']['municipio_destino']) 
                        : ($item['viatico']['municipio_destino'] ?? '');
                    return str_contains(strtolower($municipioDestino), $municipioBuscar);
                });
            })
            // --- Mapeo CORREGIDO ---
            ->map(function ($item) {
                // Obtener anticipo
                $anticipo = (float) ($item['valores_viatico']['total'] ?? 0);
                
                $legalizado = 0;
                
                // Intentar obtener de valores_legalizados (nueva estructura)
                if (isset($item['valores_legalizados']['total'])) {
                    $legalizado = (float) $item['valores_legalizados']['total'];
                } 
                // Fallback a legalizacion.valor_total (estructura antigua por compatibilidad)
                elseif (isset($item['legalizacion']['valor_total'])) {
                    $legalizado = (float) $item['legalizacion']['valor_total'];
                }
                
                // Calcular saldo pendiente: Anticipo - Legalizado
                $saldoPendiente = $anticipo - $legalizado;
                $idLegalizacion = $item['legalizacion']['id'] ?? 'N/A';

                // Obtener municipio destino
                $municipioDestino = 'N/A';
                if (isset($item['viatico']['municipio_destino'])) {
                    if (is_array($item['viatico']['municipio_destino'])) {
                        $municipioDestino = reset($item['viatico']['municipio_destino']) ?: 'N/A';
                    } else {
                        $municipioDestino = $item['viatico']['municipio_destino'];
                    }
                }

                return (object) [
                    'item' => $item['viatico']['id'] ?? 'N/A',
                    'id_legalizacion' => $idLegalizacion,
                    'fecha_ida' => isset($item['viatico']['fecha_inicio']) 
                        ? Carbon::parse($item['viatico']['fecha_inicio']) 
                        : null,
                    'municipio_destino' => $municipioDestino,
                    'anticipo' => $anticipo,
                    'legalizado' => $legalizado,
                    'saldo_pendiente' => $saldoPendiente,
                    'estado' => $this->mapEstadoViatico($item['viatico']['estado_viatico_id'] ?? null),
                ];
            })
            ->values();

        // --- Resumen calculado ---
        $resumen = null;
        $dbResumen = null;

        if ($detalle->isNotEmpty()) {
            $anticipos = (float) $detalle->sum('anticipo');
            $legalizado = (float) $detalle->sum('legalizado');
            $neto = round($anticipos - $legalizado, 2);

            $estadoSaldo = $neto > 0 
                ? 'SALDO A FAVOR DE SYSO'
                : ($neto < 0 ? 'SALDO A FAVOR DEL ASESOR' : 'SALDO EN $0');

            $resumen = (object) [
                'anticipos_adiciones' => $anticipos,
                'legalizado_devoluciones' => $legalizado,
                'sin_legalizar' => $neto > 0 ? $neto : 0,
                'total_consignar' => $neto > 0 ? $neto : 0,
                'estado_saldo' => $estadoSaldo,
            ];

            $dbResumen = clone $resumen;
        }

        return [
            'resumen' => $resumen,
            'detalle' => $detalle,
            'dbResumen' => $dbResumen,
            'dbDetalle' => $detalle,
            'error' => null,
        ];

    } catch (\Throwable $exception) {
        SincronizacionApi::query()->create([
            'modulo' => 'viaticos_api',
            'estado' => 'error',
            'log_error' => $exception->getMessage() . "\n" . $exception->getTraceAsString(),
            'fecha_inicio' => now(),
            'fecha_fin' => now(),
        ]);

        Log::error('Error ViaticosApiDataSource', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'cedula' => $cedula,
        ]);

        return [
            'resumen' => null,
            'detalle' => collect(),
            'dbResumen' => null,
            'dbDetalle' => collect(),
            'error' => 'Error de conectividad con la API de Viáticos. Por favor, intente más tarde.',
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