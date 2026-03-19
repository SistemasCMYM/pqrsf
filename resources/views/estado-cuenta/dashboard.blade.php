<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-[#36574e]">Estado de Cuenta Proyectos SYSO</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if($error)<div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $error }}</div>@endif
            @if($cedula && !$error && !$resumen)<div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-700">No hay registros para la cédula <strong>{{ $cedula }}</strong>. Verifica que el número sea correcto.</div>@endif
            <form class="grid gap-3 cmm-card p-4 md:grid-cols-6">
                @if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Coordinador Estado Cuenta'))
                    <input name="cedula" value="{{ $cedula }}" placeholder="Cédula" class="rounded-lg border-[#e7e7e7]">
                @endif
                <input name="anio" value="{{ $filters['anio'] ?? '' }}" placeholder="Año" class="rounded-lg border-[#e7e7e7]">
                <input name="mes" value="{{ $filters['mes'] ?? '' }}" placeholder="Mes" class="rounded-lg border-[#e7e7e7]">
                <input name="estado" value="{{ $filters['estado'] ?? '' }}" placeholder="Estado" class="rounded-lg border-[#e7e7e7]">
                <input name="municipio" value="{{ $filters['municipio'] ?? '' }}" placeholder="Municipio" class="rounded-lg border-[#e7e7e7]">
                <button class="cmm-btn-primary">Aplicar filtros</button>
            </form>

            {{-- Resultados desde la API --}}
            <h3 class="text-lg font-semibold text-[#36574e] mt-8 mb-2">Resultados desde la API</h3>
            @if($resumen)
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total anticipos</p><p class="text-xl font-bold text-[#901227]">${{ number_format((float)$resumen->anticipos_adiciones, 0, ',', '.') }}</p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total legalizado</p><p class="text-xl font-bold text-[#36574e]">${{ number_format((float)$resumen->legalizado_devoluciones, 0, ',', '.') }}</p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Sin legalizar</p><p class="text-xl font-bold text-[#ca6261]">${{ number_format((float)$resumen->sin_legalizar, 0, ',', '.') }}</p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Estado saldo</p><p class="text-sm font-semibold text-[#624133]">{{ $resumen->estado_saldo }}</p></div>
                </div>
            @endif
            <div class="overflow-hidden cmm-card mt-2 mb-8">
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc] text-[#624133]"><tr><th class="px-4 py-3 text-left">Fecha ida</th><th class="px-4 py-3 text-left">Destino</th><th class="px-4 py-3 text-right">Anticipo</th><th class="px-4 py-3 text-right">Legalizado</th><th class="px-4 py-3 text-right">Saldo</th><th class="px-4 py-3 text-center">Estado</th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                        @forelse($detalle as $d)
                            <tr>
                                <td class="px-4 py-3">{{ optional($d->fecha_ida)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $d->municipio_destino }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float)$d->anticipo, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float)$d->legalizado, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float)$d->saldo_pendiente, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center"><span class="cmm-badge">{{ $d->estado }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-[#4d4d4d]">No hay registros para la consulta actual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Resultados desde la base de datos local --}}
            <h3 class="text-lg font-semibold text-[#36574e] mt-8 mb-2">Resultados desde la base de datos local</h3>
            @if($dbResumen)
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total anticipos</p><p class="text-xl font-bold text-[#901227]">${{ number_format((float)$dbResumen->anticipos_adiciones, 0, ',', '.') }}</p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total legalizado</p><p class="text-xl font-bold text-[#36574e]">${{ number_format((float)$dbResumen->legalizado_devoluciones, 0, ',', '.') }}</p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Sin legalizar</p><p class="text-xl font-bold text-[#ca6261]">${{ number_format((float)$dbResumen->sin_legalizar, 0, ',', '.') }}</p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Estado saldo</p><p class="text-sm font-semibold text-[#624133]">{{ $dbResumen->estado_saldo }}</p></div>
                </div>
            @endif
            <div class="overflow-hidden cmm-card mt-2">
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc] text-[#624133]"><tr><th class="px-4 py-3 text-left">Fecha ida</th><th class="px-4 py-3 text-left">Destino</th><th class="px-4 py-3 text-right">Anticipo</th><th class="px-4 py-3 text-right">Legalizado</th><th class="px-4 py-3 text-right">Saldo</th><th class="px-4 py-3 text-center">Estado</th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                        @forelse($dbDetalle as $d)
                            <tr>
                                <td class="px-4 py-3">{{ optional($d->fecha_ida)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $d->municipio_destino }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float)$d->anticipo, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float)$d->legalizado, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float)$d->saldo_pendiente, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center"><span class="cmm-badge">{{ $d->estado }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-[#4d4d4d]">No hay registros para la consulta actual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
