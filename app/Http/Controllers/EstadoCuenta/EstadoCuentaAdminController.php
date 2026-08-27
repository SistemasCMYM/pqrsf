<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Exports\EstadoCuentaGeneralExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateIntegrationConfigRequest;
use App\Models\ConfiguracionIntegracion;
use App\Models\EstadoCuentaResumen;
use App\Models\ImportacionExcel;
use App\Models\SincronizacionApi;
use App\Services\EstadoCuenta\DataSources\ViaticosApiDataSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EstadoCuentaAdminController extends Controller
{
    public function exportGeneral(ViaticosApiDataSource $viaticosApi): BinaryFileResponse
    {
        $resumenes = EstadoCuentaResumen::query()
            ->whereNotNull('cedula')
            ->orderByDesc('anio')
            ->orderByDesc('id')
            ->get();

        $usuarios = $resumenes
            ->groupBy('cedula')
            ->map(function ($resumenesUsuario, $cedula) use ($viaticosApi) {
                $ultimoResumen = $resumenesUsuario->first();
                $apiTotales = $viaticosApi->fetchTotalsByCedula((string) $cedula);
                $totalAnticipado = (float) $resumenesUsuario->sum('anticipos_adiciones')
                    + (float) data_get($apiTotales, 'total_anticipado', 0);
                $totalLegalizado = (float) $resumenesUsuario->sum('legalizado_devoluciones')
                    + (float) data_get($apiTotales, 'total_legalizado', 0);
                $saldoApi = max(
                    (float) data_get($apiTotales, 'total_anticipado', 0)
                    - (float) data_get($apiTotales, 'total_legalizado', 0),
                    0
                );
                $saldo = (float) $resumenesUsuario->sum('sin_legalizar') + $saldoApi;
                $neto = round($totalAnticipado - $totalLegalizado, 2);

                return (object) [
                    'usuario' => $ultimoResumen->id_asesor,
                    'nombre' => data_get($apiTotales, 'nombre') ?: $ultimoResumen->nombre_asesor,
                    'cedula' => $cedula,
                    'total_anticipado' => $totalAnticipado,
                    'total_legalizado' => $totalLegalizado,
                    'estado' => $neto > 0
                        ? 'SALDO A FAVOR DE SYSO'
                        : ($neto < 0 ? 'SALDO A FAVOR DEL ASESOR' : 'SALDO EN $0'),
                    'saldo' => $saldo,
                ];
            })
            ->values();

        return Excel::download(
            new EstadoCuentaGeneralExport($usuarios),
            'estado-cuenta-general.xlsx'
        );
    }

    public function index(): View
    {
        return view('estado-cuenta.admin.index', [
            'config' => ConfiguracionIntegracion::query()->first(),
            'imports' => ImportacionExcel::query()->latest()->paginate(10),
            'syncs' => SincronizacionApi::query()->latest()->paginate(10),
        ]);
    }

    public function updateConfig(UpdateIntegrationConfigRequest $request): RedirectResponse
    {
        ConfiguracionIntegracion::query()->updateOrCreate(
            ['modulo' => 'estado_cuenta'],
            [
                ...$request->validated(),
                'api_timeout' => $request->integer('api_timeout') ?: 15,
                'activo' => true,
            ]
        );

        return back()->with('success', 'Configuración actualizada.');
    }
}
