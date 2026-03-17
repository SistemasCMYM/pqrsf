<?php

namespace App\Services\Pqrsf;

use App\Models\ParametrizacionSla;
use App\Models\Pqrsf;
use App\Models\PqrsfDestinatario;
use App\Models\PqrsfEstado;
use App\Models\PqrsfHistorial;
use App\Models\User;
use App\Notifications\PqrsfAssignedNotification;
use App\Notifications\PqrsfCreatedNotification;
use App\Notifications\PqrsfStatusChangedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class PqrsfService
{
    public function createPublic(array $data, array $files = []): Pqrsf
    {
        return DB::transaction(function () use ($data, $files): Pqrsf {
            $tipoId = (int) $data['pqrsf_tipo_id'];
            $sla = ParametrizacionSla::query()
                ->where('activo', true)
                ->where('pqrsf_tipo_id', $tipoId)
                ->orderByDesc('id')
                ->first();

            $dias = $sla?->dias_respuesta ?? 15;
            $estadoInicial = PqrsfEstado::query()->where('slug', 'radicada')->firstOrFail();

            $pqrsf = Pqrsf::query()->create([
                ...$data,
                'canal_ingreso' => $data['canal_ingreso'] ?? 'web',
                'radicado' => $this->buildRadicado(),
                'destinatario_original_id' => $data['destinatario_id'] ?? null,
                'pqrsf_estado_id' => $estadoInicial->id,
                'fecha_limite_respuesta' => now()->addDays($dias),
            ]);

            $assignedTo = null;
            if (! empty($data['destinatario_id'])) {
                $assignedTo = PqrsfDestinatario::query()
                    ->where('id', (int) $data['destinatario_id'])
                    ->value('responsable_user_id');
            }

            if (! $assignedTo && Schema::hasTable('pqrsf_tipo_responsables')) {
                $assignedTo = DB::table('pqrsf_tipo_responsables')
                    ->where('pqrsf_tipo_id', $tipoId)
                    ->value('user_id');
            }

            if ($assignedTo) {
                $pqrsf->update(['asignado_a' => $assignedTo]);
            }

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $path = $file->store('pqrsf_adjuntos', 'public');
                $pqrsf->adjuntos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'tamano' => $file->getSize() ?: 0,
                ]);
            }

            $this->logChange($pqrsf, null, $estadoInicial->id, null, null, 'Creación pública de PQRSF');

            $admins = User::role(['Administrador', 'Admin PQRSF', 'Gestor PQRSF'])->get();
            Notification::send($admins, new PqrsfCreatedNotification($pqrsf));

            return $pqrsf;
        });
    }

    public function assign(Pqrsf $pqrsf, ?int $assignedTo, ?string $observation = null): Pqrsf
    {
        $before = $pqrsf->asignado_a;

        $pqrsf->update(['asignado_a' => $assignedTo]);

        $this->logChange($pqrsf, $pqrsf->pqrsf_estado_id, $pqrsf->pqrsf_estado_id, $before, $assignedTo, $observation, 'Asignación');

        if ($assignedTo) {
            $user = User::query()->find($assignedTo);
            $user?->notify(new PqrsfAssignedNotification($pqrsf));
        }

        return $pqrsf->fresh();
    }

    public function changeStatus(Pqrsf $pqrsf, int $estadoNuevoId, ?string $observation = null): Pqrsf
    {
        $estadoAnterior = $pqrsf->pqrsf_estado_id;

        $payload = ['pqrsf_estado_id' => $estadoNuevoId];

        $estado = PqrsfEstado::query()->find($estadoNuevoId);
        if ($estado?->es_cierre) {
            $payload['fecha_cierre'] = now();
        }

        $pqrsf->update($payload);

        $this->logChange($pqrsf, $estadoAnterior, $estadoNuevoId, $pqrsf->asignado_a, $pqrsf->asignado_a, $observation, 'Cambio de estado');

        $receivers = User::role(['Administrador', 'Admin PQRSF'])->get();
        Notification::send($receivers, new PqrsfStatusChangedNotification($pqrsf));

        return $pqrsf->fresh();
    }

    public function redirectDestinatario(Pqrsf $pqrsf, int $destinatarioId, ?string $observacion = null): Pqrsf
    {
        $destinatario = PqrsfDestinatario::query()->findOrFail($destinatarioId);
        $beforeDestinatario = $pqrsf->destinatario_id;
        $beforeResponsable = $pqrsf->asignado_a;

        $pqrsf->update([
            'destinatario_id' => $destinatarioId,
            'asignado_a' => $destinatario->responsable_user_id ?: $pqrsf->asignado_a,
        ]);

        $this->logChange(
            $pqrsf,
            $pqrsf->pqrsf_estado_id,
            $pqrsf->pqrsf_estado_id,
            $beforeResponsable,
            $pqrsf->asignado_a,
            $observacion ?: 'Redireccionada a '.$destinatario->nombre,
            'Redirección de destinatario'
        );

        PqrsfHistorial::query()->create([
            'pqrsf_id' => $pqrsf->id,
            'user_id' => auth()->id(),
            'accion' => 'Cambio de destinatario',
            'observacion' => 'De '.$this->destinatarioNombre($beforeDestinatario).' a '.$destinatario->nombre,
            'metadata' => ['destinatario_anterior_id' => $beforeDestinatario, 'destinatario_nuevo_id' => $destinatarioId],
            'created_at' => now(),
        ]);

        return $pqrsf->fresh();
    }

    public function markOverdue(): int
    {
        $estadoVencida = PqrsfEstado::query()->where('slug', 'vencida')->first();
        if (! $estadoVencida) {
            return 0;
        }

        $count = 0;

        Pqrsf::query()
            ->where('vencida', false)
            ->whereNotNull('fecha_limite_respuesta')
            ->where('fecha_limite_respuesta', '<', now())
            ->chunkById(100, function ($items) use (&$count, $estadoVencida): void {
                foreach ($items as $pqrsf) {
                    $before = $pqrsf->pqrsf_estado_id;

                    $pqrsf->update([
                        'vencida' => true,
                        'pqrsf_estado_id' => $estadoVencida->id,
                    ]);

                    $this->logChange($pqrsf, $before, $estadoVencida->id, $pqrsf->asignado_a, $pqrsf->asignado_a, 'Marcada automáticamente como vencida', 'SLA vencido');
                    $count++;
                }
            });

        return $count;
    }

    private function buildRadicado(): string
    {
        return 'PQRSF-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    private function logChange(Pqrsf $pqrsf, ?int $estadoAnterior, ?int $estadoNuevo, ?int $responsableAnterior, ?int $responsableNuevo, ?string $observacion, string $accion = 'Actualización'): void
    {
        PqrsfHistorial::query()->create([
            'pqrsf_id' => $pqrsf->id,
            'user_id' => auth()->id(),
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => $estadoNuevo,
            'responsable_anterior_id' => $responsableAnterior,
            'responsable_nuevo_id' => $responsableNuevo,
            'accion' => $accion,
            'observacion' => $observacion,
            'metadata' => ['ip' => request()?->ip()],
            'created_at' => now(),
        ]);
    }

    private function destinatarioNombre(?int $id): string
    {
        if (! $id) {
            return 'Sin destinatario';
        }

        return PqrsfDestinatario::query()->where('id', $id)->value('nombre') ?? 'Sin destinatario';
    }
}
