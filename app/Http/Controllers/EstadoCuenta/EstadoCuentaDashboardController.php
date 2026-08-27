<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Exports\EstadoCuentaSigiExport;
use App\Http\Controllers\Controller;
use App\Services\EstadoCuenta\EstadoCuentaSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\EstadoCuentaResumen;
use App\Models\EstadoCuentaDetalle;

class EstadoCuentaDashboardController extends Controller
{
    public function export(Request $request, EstadoCuentaSyncService $service): BinaryFileResponse
    {
        $user = Auth::user();
        $cedula = $request->string('cedula')->toString();

        if (! $user->hasRole('Administrador')) {
            $cedula = $user->document_number ?? '';
        }

        abort_if($cedula === '', 422, 'No se encontró una cédula para exportar.');

        $filters = $request->only(['anio', 'mes', 'estado', 'municipio']);
        $data = $service->consultByCedula($cedula, $filters);

        abort_if(! empty($data['error']), 502, $data['error']);

        $dbDetalle = EstadoCuentaDetalle::where('cedula', $cedula)
            ->when($filters['anio'] ?? null, fn ($q, $anio) => $q->where('anio', $anio))
            ->when($filters['mes'] ?? null, fn ($q, $mes) => $q->where('mes', $mes))
            ->when($filters['estado'] ?? null, fn ($q, $estado) => $q->where('estado', $estado))
            ->when($filters['municipio'] ?? null, fn ($q, $municipio) => $q->where('municipio_destino', 'like', "%{$municipio}%"))
            ->orderByDesc('fecha_ida')
            ->get();

        $detalle = collect($data['detalle'] ?? [])
            ->map(function ($item) {
                if (is_array($item)) {
                    $item['fuente_exportacion'] = 'SIGI';

                    return (object) $item;
                }

                $item->fuente_exportacion = 'SIGI';

                return $item;
            })
            ->concat($dbDetalle->map(function (EstadoCuentaDetalle $item) {
                $item->fuente_exportacion = 'Base de datos local';

                return $item;
            }));

        return Excel::download(
            new EstadoCuentaSigiExport($detalle),
            'estado-cuenta-sigi-'.$cedula.'.xlsx'
        );
    }

    public function __invoke(Request $request, EstadoCuentaSyncService $service): View
    {
        $user = Auth::user();

        $cedula = $request->string('cedula')->toString();
        if (! $user->hasRole('Administrador')) {
            $cedula = $user->document_number ?? '';
        }

        $filters = $request->only(['anio', 'mes', 'estado', 'municipio']);
        $data = $cedula ? $service->consultByCedula($cedula, $filters) : ['resumen' => null, 'detalle' => collect()];

        // Consulta base de datos local
        $dbResumen = null;
        $dbDetalle = collect();
        if ($cedula) {
            $dbResumen = EstadoCuentaResumen::where('cedula', $cedula)
                ->when($filters['anio'] ?? null, fn ($q, $anio) => $q->where('anio', $anio))
                ->latest('anio')
                ->first();
            $dbDetalle = EstadoCuentaDetalle::where('cedula', $cedula)
                ->when($filters['anio'] ?? null, fn ($q, $anio) => $q->where('anio', $anio))
                ->when($filters['mes'] ?? null, fn ($q, $mes) => $q->where('mes', $mes))
                ->when($filters['estado'] ?? null, fn ($q, $estado) => $q->where('estado', $estado))
                ->when($filters['municipio'] ?? null, fn ($q, $municipio) => $q->where('municipio_destino', 'like', "%{$municipio}%"))
                ->orderByDesc('fecha_ida')
                ->get();
        }

        return view('estado-cuenta.dashboard', [
            'resumen' => $data['resumen'] ?? null,
            'detalle' => $data['detalle'] ?? collect(),
            'cedula' => $cedula,
            'filters' => $filters,
            'error' => $data['error'] ?? null,
            'dbResumen' => $dbResumen,
            'dbDetalle' => $dbDetalle,
        ]);
    }
}
