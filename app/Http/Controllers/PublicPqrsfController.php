<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicPqrsfRequest;
use App\Models\PqrsfDestinatario;
use App\Models\PqrsfTipo;
use App\Services\Pqrsf\PqrsfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicPqrsfController extends Controller
{
    public function create(): View
    {
        return view('public.pqrsf.create', [
            'tipos' => PqrsfTipo::query()->where('activo', true)->orderBy('nombre')->get(),
            'destinatarios' => PqrsfDestinatario::query()->where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(StorePublicPqrsfRequest $request, PqrsfService $service): RedirectResponse
    {
        $payload = $request->validated();
        $files = $request->file('files', []);
        unset($payload['files']);

        $pqrsf = $service->createPublic($payload, $files);

        if ((bool) env('PQRSF_SEND_CONFIRMATION_EMAIL', false)) {
            Mail::raw(
                "Tu solicitud PQRSF fue recibida correctamente. Radicado: {$pqrsf->radicado}",
                function ($message) use ($pqrsf): void {
                    $message->to($pqrsf->email)->subject('Confirmación de radicado PQRSF');
                }
            );
        }

        return redirect()->route('public.pqrsf.success', ['radicado' => $pqrsf->radicado])
            ->with('success', 'Tu solicitud fue radicada correctamente.');
    }

    public function success(string $radicado): View
    {
        return view('public.pqrsf.success', compact('radicado'));
    }
}
