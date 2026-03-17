<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-[#901227]">Bandeja de PQRSF</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))<div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            <form method="GET" class="grid gap-3 cmm-card p-4 md:grid-cols-7">
                <input name="radicado" value="{{ request('radicado') }}" placeholder="Radicado" class="rounded-lg border-[#e7e7e7]">
                <select name="pqrsf_tipo_id" class="rounded-lg border-[#e7e7e7]"><option value="">Tipo</option>@foreach($tipos as $t)<option value="{{ $t->id }}" @selected(request('pqrsf_tipo_id')==$t->id)>{{ $t->nombre }}</option>@endforeach</select>
                <select name="destinatario_id" class="rounded-lg border-[#e7e7e7]"><option value="">Destinatario</option>@foreach($destinatarios as $d)<option value="{{ $d->id }}" @selected(request('destinatario_id')==$d->id)>{{ $d->nombre }}</option>@endforeach</select>
                <select name="pqrsf_estado_id" class="rounded-lg border-[#e7e7e7]"><option value="">Estado</option>@foreach($estados as $e)<option value="{{ $e->id }}" @selected(request('pqrsf_estado_id')==$e->id)>{{ $e->nombre }}</option>@endforeach</select>
                <input name="documento" value="{{ request('documento') }}" placeholder="Documento" class="rounded-lg border-[#e7e7e7]">
                <input name="nombre" value="{{ request('nombre') }}" placeholder="Nombre" class="rounded-lg border-[#e7e7e7]">
                <button class="cmm-btn-primary">Filtrar</button>
            </form>

            <div class="overflow-hidden cmm-card">
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc] text-[#624133]"><tr><th class="px-4 py-3 text-left">Radicado</th><th class="px-4 py-3 text-left">Solicitante</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3">Destinatario</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Responsable</th><th class="px-4 py-3">SLA</th><th class="px-4 py-3"></th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-[#901227]">{{ $item->radicado }}</td>
                            <td class="px-4 py-3">{{ $item->nombres }} {{ $item->apellidos }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->tipo?->nombre }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->destinatario?->nombre ?? 'Sin área' }}</td>
                            <td class="px-4 py-3 text-center"><span class="cmm-badge">{{ $item->estado?->nombre }}</span></td>
                            <td class="px-4 py-3 text-center">{{ $item->asignadoA?->name ?? 'Sin asignar' }}</td>
                            <td class="px-4 py-3 text-center">@if($item->dias_restantes !== null)<span class="{{ $item->dias_restantes < 0 ? 'text-[#ca6261]' : ($item->dias_restantes <= 2 ? 'text-[#d4ac5a]' : 'text-[#1d5619]') }} font-semibold">{{ $item->dias_restantes }} días</span>@else - @endif</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('pqrsf.show', $item) }}" class="text-[#4b729f] hover:underline">Gestionar</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-[#4d4d4d]">Sin resultados</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
</x-app-layout>
