<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-[#36574e]">Administración Estado de Cuenta</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))<div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>@endif

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Importar Excel</h3>
                    <p class="mt-1 text-xs text-[#4d4d4d]">Puedes cargar archivos con resumen o solo detalle. El sistema consolida automáticamente.</p>
                    <form method="POST" action="{{ route('estado-cuenta.admin.import') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        <input type="file" name="archivo" class="w-full rounded-lg border-[#e7e7e7]" required>
                        <input name="anio" placeholder="Año de carga" class="w-full rounded-lg border-[#e7e7e7]">
                        <button class="cmm-btn-primary">Procesar archivo</button>
                    </form>
                </div>

                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Configuración de integración</h3>
                    <form method="POST" action="{{ route('estado-cuenta.admin.config.update') }}" class="mt-4 grid gap-3">
                        @csrf
                        <select name="fuente_activa" class="rounded-lg border-[#e7e7e7]">
                            <option value="excel" @selected(optional($config)->fuente_activa==='excel')>Excel</option>
                            <option value="api" @selected(optional($config)->fuente_activa==='api')>API</option>
                        </select>
                        <input name="api_base_url" value="{{ old('api_base_url', $config->api_base_url ?? '') }}" placeholder="API Base URL" class="rounded-lg border-[#e7e7e7]">
                        <input name="api_token" value="{{ old('api_token', $config->api_token ?? '') }}" placeholder="API Token" class="rounded-lg border-[#e7e7e7]">
                        <input name="api_timeout" value="{{ old('api_timeout', $config->api_timeout ?? 15) }}" placeholder="Timeout" class="rounded-lg border-[#e7e7e7]">
                        <button class="cmm-btn-secondary">Guardar configuración</button>
                    </form>
                    <form method="POST" action="{{ route('estado-cuenta.admin.sync-api') }}" class="mt-4">@csrf<button class="rounded-lg border border-[#4b729f] px-4 py-2 text-sm text-[#4b729f]">Sincronizar API manual</button></form>
                </div>
            </div>

            <div class="cmm-card p-6">
                <h3 class="text-lg font-semibold text-[#624133]">Últimas importaciones</h3>
                <div class="mt-3 space-y-2 text-sm">
                    @foreach($imports as $import)
                        <div class="rounded-lg border border-[#e7e7e7] p-3">
                            <p class="font-semibold">{{ $import->nombre_archivo }}</p>
                            <p class="text-[#4d4d4d]">Estado: {{ $import->estado }} | Total: {{ $import->total_registros }} | Procesados: {{ $import->procesados }} | Fecha: {{ optional($import->fecha_importacion)->format('Y-m-d H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
