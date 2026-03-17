<?php

namespace App\Http\Controllers\Pqrsf;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePqrsfSlaRequest;
use App\Http\Requests\UpdatePqrsfTipoResponsablesRequest;
use App\Models\ParametrizacionSla;
use App\Models\PqrsfDestinatario;
use App\Models\PqrsfTipo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PqrsfConfigController extends Controller
{
    public function index(): View
    {
        $tipos = PqrsfTipo::query()->orderBy('nombre')->get();

        return view('pqrsf.config', [
            'tipos' => $tipos,
            'slas' => ParametrizacionSla::query()->with('tipo')->orderByDesc('id')->get(),
            'responsables' => User::role(['Administrador', 'Admin PQRSF', 'Gestor PQRSF'])->orderBy('name')->get(),
            'maps' => Schema::hasTable('pqrsf_tipo_responsables')
                ? DB::table('pqrsf_tipo_responsables')->get()->groupBy('pqrsf_tipo_id')
                : collect(),
            'destinatarios' => PqrsfDestinatario::query()->with('responsable')->orderBy('nombre')->get(),
        ]);
    }

    public function updateSla(UpdatePqrsfSlaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        ParametrizacionSla::query()->updateOrCreate(
            ['pqrsf_tipo_id' => $data['pqrsf_tipo_id'], 'prioridad' => $data['prioridad']],
            ['dias_respuesta' => $data['dias_respuesta'], 'activo' => true]
        );

        return back()->with('success', 'SLA actualizado correctamente.');
    }

    public function updateResponsables(UpdatePqrsfTipoResponsablesRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tipoId = (int) $data['pqrsf_tipo_id'];

        if (! Schema::hasTable('pqrsf_tipo_responsables')) {
            return back()->with('error', 'Falta ejecutar migraciones del sistema. Ejecuta: php artisan migrate');
        }

        DB::table('pqrsf_tipo_responsables')->where('pqrsf_tipo_id', $tipoId)->delete();

        foreach ($data['responsables'] ?? [] as $userId) {
            DB::table('pqrsf_tipo_responsables')->insert([
                'pqrsf_tipo_id' => $tipoId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Responsables por tipo actualizados.');
    }

    public function storeDestinatario(): RedirectResponse
    {
        $data = request()->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'responsable_user_id' => ['nullable', 'exists:users,id'],
            'activo' => ['nullable', 'boolean'],
        ]);

        PqrsfDestinatario::query()->create([
            'nombre' => $data['nombre'],
            'slug' => \Illuminate\Support\Str::slug($data['nombre'].'-'.time()),
            'responsable_user_id' => $data['responsable_user_id'] ?? null,
            'activo' => (bool) ($data['activo'] ?? true),
        ]);

        return back()->with('success', 'Destinatario creado correctamente.');
    }

    public function updateDestinatario(PqrsfDestinatario $destinatario): RedirectResponse
    {
        $data = request()->validate([
            'responsable_user_id' => ['nullable', 'exists:users,id'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $destinatario->update([
            'responsable_user_id' => $data['responsable_user_id'] ?? null,
            'activo' => (bool) ($data['activo'] ?? false),
        ]);

        return back()->with('success', 'Destinatario actualizado.');
    }
}
