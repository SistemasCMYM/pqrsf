<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-[#901227]">Configuración PQRSF</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Destinatarios (áreas)</h3>
                    <form method="POST" action="{{ route('pqrsf.config.destinatarios.store') }}" class="mt-4 grid gap-3">
                        @csrf
                        <input name="nombre" class="rounded-lg border-[#e7e7e7]" placeholder="Ej: Gestión Humana" required>
                        <select name="responsable_user_id" class="rounded-lg border-[#e7e7e7]">
                            <option value="">Sin responsable por defecto</option>
                            @foreach($responsables as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                        </select>
                        <label class="text-sm"><input type="checkbox" name="activo" value="1" checked> Activo</label>
                        <button class="cmm-btn-primary">Crear destinatario</button>
                    </form>
                    <div class="mt-4 space-y-2">
                        @foreach($destinatarios as $d)
                            <form method="POST" action="{{ route('pqrsf.config.destinatarios.update', $d) }}" class="grid grid-cols-1 gap-2 rounded-lg border border-[#e7e7e7] p-3 md:grid-cols-4">
                                @csrf @method('PATCH')
                                <div class="md:col-span-2 text-sm font-semibold">{{ $d->nombre }}</div>
                                <select name="responsable_user_id" class="rounded-lg border-[#e7e7e7] text-sm">
                                    <option value="">Sin responsable</option>
                                    @foreach($responsables as $r)<option value="{{ $r->id }}" @selected($d->responsable_user_id==$r->id)>{{ $r->name }}</option>@endforeach
                                </select>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs"><input type="checkbox" name="activo" value="1" @checked($d->activo)> Activo</label>
                                    <button class="cmm-btn-secondary text-xs">Guardar</button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Parametrizar tiempos SLA</h3>
                    <form method="POST" action="{{ route('pqrsf.config.sla') }}" class="mt-4 grid gap-3">
                        @csrf
                        <select name="pqrsf_tipo_id" class="rounded-lg border-[#e7e7e7]" required>
                            <option value="">Tipo de PQRSF</option>
                            @foreach($tipos as $t)<option value="{{ $t->id }}">{{ $t->nombre }}</option>@endforeach
                        </select>
                        <select name="prioridad" class="rounded-lg border-[#e7e7e7]" required>
                            @foreach(['baja','media','alta','critica'] as $p)<option value="{{ $p }}">{{ ucfirst($p) }}</option>@endforeach
                        </select>
                        <input type="number" name="dias_respuesta" class="rounded-lg border-[#e7e7e7]" placeholder="Días de respuesta" required>
                        <button class="cmm-btn-primary">Guardar SLA</button>
                    </form>
                </div>

                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Responsables por tipo (líneas)</h3>
                    <form method="POST" action="{{ route('pqrsf.config.responsables') }}" class="mt-4 grid gap-3">
                        @csrf
                        <select name="pqrsf_tipo_id" class="rounded-lg border-[#e7e7e7]" required>
                            <option value="">Tipo de PQRSF</option>
                            @foreach($tipos as $t)<option value="{{ $t->id }}">{{ $t->nombre }}</option>@endforeach
                        </select>
                        <select name="responsables[]" multiple class="rounded-lg border-[#e7e7e7] h-36">
                            @foreach($responsables as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                        </select>
                        <button class="cmm-btn-secondary">Guardar responsables</button>
                    </form>
                </div>
            </div>

            <div class="cmm-card p-6">
                <h3 class="mb-3 text-lg font-semibold text-[#624133]">Resumen actual SLA</h3>
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc]"><tr><th class="px-3 py-2 text-left">Tipo</th><th class="px-3 py-2">Prioridad</th><th class="px-3 py-2">Días</th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                        @foreach($slas as $sla)
                            <tr><td class="px-3 py-2">{{ $sla->tipo?->nombre }}</td><td class="px-3 py-2 text-center">{{ ucfirst($sla->prioridad) }}</td><td class="px-3 py-2 text-center">{{ $sla->dias_respuesta }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
