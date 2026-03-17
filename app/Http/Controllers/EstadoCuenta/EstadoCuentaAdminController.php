<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateIntegrationConfigRequest;
use App\Models\ConfiguracionIntegracion;
use App\Models\ImportacionExcel;
use App\Models\SincronizacionApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EstadoCuentaAdminController extends Controller
{
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
