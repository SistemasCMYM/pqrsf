<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportEstadoCuentaExcelRequest;
use App\Models\ImportacionExcel;
use App\Services\EstadoCuenta\ExcelAccountStatementImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ImportacionExcelController extends Controller
{
    public function store(ImportEstadoCuentaExcelRequest $request, ExcelAccountStatementImportService $importService): RedirectResponse
    {
        $file = $request->file('archivo');
        $path = $file->store('imports/estado-cuenta', 'public');

        $importacion = ImportacionExcel::query()->create([
            'modulo' => 'estado_cuenta',
            'user_id' => auth()->id(),
            'nombre_archivo' => $file->getClientOriginalName(),
            'ruta_archivo' => $path,
            'estado' => 'procesando',
            'fecha_importacion' => now(),
        ]);

        $anio = $request->integer('anio') ?: (int) now()->year;

        try {
            $summary = $importService->import(Storage::disk('public')->path($path), $importacion->id, $anio);

            $importacion->update([
                'estado' => 'completado',
                'total_registros' => (int) ($summary['total_rows'] ?? 0),
                'procesados' => (int) ($summary['total_rows'] ?? 0),
            ]);

            return back()->with('success', 'Importación procesada correctamente.');
        } catch (\Throwable $exception) {
            $importacion->update([
                'estado' => 'fallido',
                'log_error' => $exception->getMessage(),
            ]);

            return back()->with('error', 'La importación falló: '.$exception->getMessage());
        }
    }
}
