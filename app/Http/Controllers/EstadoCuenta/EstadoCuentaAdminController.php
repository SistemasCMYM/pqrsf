<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Exports\EstadoCuentaGeneralExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateIntegrationConfigRequest;
use App\Models\ConfiguracionIntegracion;
use App\Models\EstadoCuentaResumen;
use App\Models\ImportacionExcel;
use App\Models\SincronizacionApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EstadoCuentaAdminController extends Controller
{
    public function exportGeneral(): BinaryFileResponse
    {
        $resumenes = EstadoCuentaResumen::query()
            ->whereNotNull('cedula')
            ->orderByDesc('anio')
            ->orderByDesc('id')
            ->get();

        $usuarios = $resumenes
            ->groupBy('cedula')
            ->map(function ($resumenesUsuario, $cedula) {
                $ultimoResumen = $resumenesUsuario->first();

                return (object) [
                    'usuario' => $ultimoResumen->id_asesor,
                    'nombre' => $ultimoResumen->nombre_asesor,
                    'cedula' => $cedula,
                    'total_anticipado' => $resumenesUsuario->sum('anticipos_adiciones'),
                    'total_legalizado' => $resumenesUsuario->sum('legalizado_devoluciones'),
                    'estado' => $ultimoResumen->estado_saldo,
                    'saldo' => $resumenesUsuario->sum('sin_legalizar'),
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
