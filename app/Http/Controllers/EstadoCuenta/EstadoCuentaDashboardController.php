<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Http\Controllers\Controller;
use App\Services\EstadoCuenta\EstadoCuentaSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EstadoCuentaDashboardController extends Controller
{
    public function __invoke(Request $request, EstadoCuentaSyncService $service): View
    {
        $user = Auth::user();

        $cedula = $request->string('cedula')->toString();
        if (! $user->hasRole('Administrador')) {
            $cedula = $user->document_number ?? '';
        }

        $filters = $request->only(['anio', 'mes', 'estado', 'municipio']);
        $data = $cedula ? $service->consultByCedula($cedula, $filters) : ['resumen' => null, 'detalle' => collect()];

        return view('estado-cuenta.dashboard', [
            'resumen' => $data['resumen'] ?? null,
            'detalle' => $data['detalle'] ?? collect(),
            'cedula' => $cedula,
            'filters' => $filters,
            'error' => $data['error'] ?? null,
        ]);
    }
}
