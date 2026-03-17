<?php

namespace App\Http\Controllers\Pqrsf;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPqrsfRequest;
use App\Http\Requests\StorePqrsfResponseRequest;
use App\Http\Requests\UpdatePqrsfRequest;
use App\Models\Pqrsf;
use App\Models\PqrsfDestinatario;
use App\Models\PqrsfEstado;
use App\Models\PqrsfRespuesta;
use App\Models\PqrsfTipo;
use App\Models\User;
use App\Services\Pqrsf\PqrsfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PqrsfManagementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Pqrsf::class);
        $user = $request->user();

        $items = Pqrsf::query()
            ->with(['tipo', 'estado', 'asignadoA', 'destinatario'])
            ->when($user?->hasRole('Asesor'), fn ($q) => $q->where('numero_documento', $user->document_number))
            ->when($request->filled('radicado'), fn ($q) => $q->where('radicado', 'like', '%'.$request->string('radicado').'%'))
            ->when($request->filled('pqrsf_tipo_id'), fn ($q) => $q->where('pqrsf_tipo_id', $request->integer('pqrsf_tipo_id')))
            ->when($request->filled('pqrsf_estado_id'), fn ($q) => $q->where('pqrsf_estado_id', $request->integer('pqrsf_estado_id')))
            ->when($request->filled('prioridad'), fn ($q) => $q->where('prioridad', $request->string('prioridad')))
            ->when($request->filled('asignado_a'), fn ($q) => $q->where('asignado_a', $request->integer('asignado_a')))
            ->when($request->filled('documento'), fn ($q) => $q->where('numero_documento', 'like', '%'.$request->string('documento').'%'))
            ->when($request->filled('nombre'), fn ($q) => $q->whereRaw("concat(nombres, ' ', apellidos) like ?", ['%'.$request->string('nombre').'%']))
            ->when($request->filled('destinatario_id'), fn ($q) => $q->where('destinatario_id', $request->integer('destinatario_id')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pqrsf.index', [
            'items' => $items,
            'tipos' => PqrsfTipo::query()->orderBy('nombre')->get(),
            'estados' => PqrsfEstado::query()->orderBy('nombre')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'destinatarios' => PqrsfDestinatario::query()->where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function show(Pqrsf $pqrsf): View
    {
        Gate::authorize('view', $pqrsf);

        $pqrsf->load(['tipo', 'estado', 'asignadoA', 'destinatario', 'destinatarioOriginal', 'historial.user', 'adjuntos', 'respuestas']);

        return view('pqrsf.show', [
            'pqrsf' => $pqrsf,
            'estados' => PqrsfEstado::query()->orderBy('nombre')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'destinatarios' => PqrsfDestinatario::query()->where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdatePqrsfRequest $request, Pqrsf $pqrsf, PqrsfService $service): RedirectResponse
    {
        Gate::authorize('update', $pqrsf);

        $data = $request->validated();

        if (isset($data['asignado_a'])) {
            $service->assign($pqrsf, (int) $data['asignado_a'], $data['observacion'] ?? null);
        }

        if (isset($data['pqrsf_estado_id'])) {
            $service->changeStatus($pqrsf, (int) $data['pqrsf_estado_id'], $data['observacion'] ?? null);
        }

        if (! empty($data['destinatario_id']) && (int) $data['destinatario_id'] !== (int) $pqrsf->destinatario_id) {
            $service->redirectDestinatario($pqrsf, (int) $data['destinatario_id'], $data['observacion'] ?? null);
        }

        if (isset($data['prioridad'])) {
            $pqrsf->update(['prioridad' => $data['prioridad']]);
        }

        return back()->with('success', 'PQRSF actualizada correctamente.');
    }

    public function assign(AssignPqrsfRequest $request, Pqrsf $pqrsf, PqrsfService $service): RedirectResponse
    {
        Gate::authorize('update', $pqrsf);

        $service->assign($pqrsf, (int) $request->integer('asignado_a'), $request->string('observacion')->toString());

        return back()->with('success', 'Responsable asignado correctamente.');
    }

    public function addResponse(StorePqrsfResponseRequest $request, Pqrsf $pqrsf): RedirectResponse
    {
        Gate::authorize('update', $pqrsf);

        $response = PqrsfRespuesta::query()->create([
            'pqrsf_id' => $pqrsf->id,
            'user_id' => auth()->id(),
            'tipo' => $request->string('tipo')->toString(),
            'mensaje' => $request->string('mensaje')->toString(),
            'notificado' => $request->boolean('notificado'),
        ]);

        if ($response->tipo === 'respuesta_ciudadano') {
            $pqrsf->update(['fecha_respuesta' => now(), 'respuesta_final' => $response->mensaje]);
        }

        if ($response->tipo === 'cierre') {
            $estadoCerrada = PqrsfEstado::query()->where('slug', 'cerrada')->first();
            if ($estadoCerrada) {
                $pqrsf->update(['pqrsf_estado_id' => $estadoCerrada->id, 'fecha_cierre' => now()]);
            }
        }

        return back()->with('success', 'Registro guardado en trazabilidad.');
    }
}
