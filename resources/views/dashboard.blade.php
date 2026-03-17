<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-[#0f2d5c]">
            {{ auth()->user()->hasRole('Asesor') ? 'Panel de asesor' : 'Dashboard administrativo' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 {{ auth()->user()->hasRole('Asesor') ? 'lg:grid-cols-2' : 'lg:grid-cols-3' }} lg:px-8">
            <a href="{{ auth()->user()->hasRole('Asesor') ? route('pqrsf.index') : route('pqrsf.dashboard') }}" class="cmm-card p-6 hover:shadow-md">
                <p class="text-xs uppercase tracking-[0.2em] text-[#4b729f]">Módulo 1</p>
                <h3 class="mt-2 text-2xl font-semibold text-[#901227]">Gestión PQRSF</h3>
                <p class="mt-3 text-sm text-[#4d4d4d]">
                    {{ auth()->user()->hasRole('Asesor') ? 'Consulta el estado de tus solicitudes registradas.' : 'Bandeja, trazabilidad, SLA, asignación y cierre.' }}
                </p>
            </a>
            <a href="{{ route('estado-cuenta.dashboard') }}" class="cmm-card p-6 hover:shadow-md">
                <p class="text-xs uppercase tracking-[0.2em] text-[#4b729f]">Módulo 2</p>
                <h3 class="mt-2 text-2xl font-semibold text-[#36574e]">Estado de Cuenta SYSO</h3>
                <p class="mt-3 text-sm text-[#4d4d4d]">Consulta por cédula, resumen y detalle de movimientos.</p>
            </a>
            @if(auth()->user()->hasAnyRole(['Administrador','Coordinador Estado Cuenta']))
                <a href="{{ route('estado-cuenta.admin.index') }}" class="cmm-card p-6 hover:shadow-md">
                    <p class="text-xs uppercase tracking-[0.2em] text-[#4b729f]">Operación</p>
                    <h3 class="mt-2 text-2xl font-semibold text-[#624133]">Panel de integraciones</h3>
                    <p class="mt-3 text-sm text-[#4d4d4d]">Importación Excel, configuración y sincronización API.</p>
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
