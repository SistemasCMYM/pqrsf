<?php

namespace App\Http\Controllers\EstadoCuenta;

use App\Http\Controllers\Controller;
use App\Jobs\SyncEstadoCuentaFromApiJob;
use Illuminate\Http\RedirectResponse;

class ApiSyncController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        SyncEstadoCuentaFromApiJob::dispatch(auth()->id());

        return back()->with('success', 'Sincronización enviada a cola.');
    }
}
