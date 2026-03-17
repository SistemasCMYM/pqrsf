<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-800">Detalle {{ $pqrsf->radicado }}</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="space-y-6 lg:col-span-2">
                @if (session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold">Información general</h3>
                    <dl class="mt-4 grid gap-2 text-sm md:grid-cols-2">
                        <div><dt class="text-slate-500">Solicitante</dt><dd>{{ $pqrsf->nombres }} {{ $pqrsf->apellidos }}</dd></div>
                        <div><dt class="text-slate-500">Documento</dt><dd>{{ $pqrsf->tipo_documento }} {{ $pqrsf->numero_documento }}</dd></div>
                        <div><dt class="text-slate-500">Tipo</dt><dd>{{ $pqrsf->tipo?->nombre }}</dd></div>
                        <div><dt class="text-slate-500">Destinatario</dt><dd>{{ $pqrsf->destinatario?->nombre ?? 'Sin área' }}</dd></div>
                        <div><dt class="text-slate-500">Estado</dt><dd>{{ $pqrsf->estado?->nombre }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-slate-500">Asunto</dt><dd>{{ $pqrsf->asunto }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-slate-500">Descripción</dt><dd>{{ $pqrsf->descripcion }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="mb-3 text-lg font-semibold">Trazabilidad</h3>
                    <div class="space-y-3">
                        @foreach($pqrsf->historial as $h)
                            <div class="rounded-lg border border-slate-200 p-3 text-sm">
                                <p class="font-semibold text-slate-800">{{ $h->accion }} - {{ optional($h->created_at)->format('Y-m-d H:i') }}</p>
                                <p class="text-slate-600">{{ $h->observacion ?? 'Sin observación' }}</p>
                                <p class="text-xs text-slate-500">Usuario: {{ $h->user?->name ?? 'Sistema' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @can('update', $pqrsf)
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold">Gestión</h3>
                        <form method="POST" action="{{ route('pqrsf.update', $pqrsf) }}" class="mt-4 space-y-3">
                            @csrf @method('PATCH')
                            <select name="pqrsf_estado_id" class="w-full rounded-lg border-slate-300">
                                @foreach($estados as $e)<option value="{{ $e->id }}" @selected($pqrsf->pqrsf_estado_id==$e->id)>{{ $e->nombre }}</option>@endforeach
                            </select>
                            <select name="destinatario_id" class="w-full rounded-lg border-slate-300">
                                @foreach($destinatarios as $d)<option value="{{ $d->id }}" @selected($pqrsf->destinatario_id==$d->id)>{{ $d->nombre }}</option>@endforeach
                            </select>
                            <select name="asignado_a" class="w-full rounded-lg border-slate-300"><option value="">Sin asignar</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected($pqrsf->asignado_a==$u->id)>{{ $u->name }}</option>@endforeach</select>
                            <select name="prioridad" class="w-full rounded-lg border-slate-300">
                                @foreach(['baja','media','alta','critica'] as $p)<option value="{{ $p }}" @selected($pqrsf->prioridad==$p)>{{ ucfirst($p) }}</option>@endforeach
                            </select>
                            <textarea name="observacion" class="w-full rounded-lg border-slate-300" placeholder="Observación de cambio"></textarea>
                            <button class="w-full rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Actualizar</button>
                        </form>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold">Registrar nota/respuesta</h3>
                        <form method="POST" action="{{ route('pqrsf.responses.store', $pqrsf) }}" class="mt-4 space-y-3">
                            @csrf
                            <select name="tipo" class="w-full rounded-lg border-slate-300">
                                <option value="nota_interna">Nota interna</option>
                                <option value="respuesta_ciudadano">Respuesta al ciudadano</option>
                                <option value="cierre">Cierre de caso</option>
                            </select>
                            <textarea name="mensaje" class="w-full rounded-lg border-slate-300" rows="4" required></textarea>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="notificado" value="1"> Notificar al solicitante</label>
                            <button class="w-full rounded-lg bg-cyan-600 px-4 py-2 font-semibold text-white">Guardar</button>
                        </form>
                    </div>
                @else
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold">Seguimiento</h3>
                        <p class="mt-2 text-sm text-slate-600">Puedes consultar el estado y trazabilidad de tu PQRSF. Para cambios internos, un gestor realizará la actualización.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
