<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-[#901227]">Dashboard PQRSF</h2><a href="{{ route('pqrsf.config.index') }}" class="cmm-btn-secondary">Configurar SLA y responsables</a></div></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total</p><p class="text-2xl font-bold text-[#901227]">{{ $total }}</p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Abiertas</p><p class="text-2xl font-bold text-[#4b729f]">{{ $abiertas }}</p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Cerradas</p><p class="text-2xl font-bold text-[#1d5619]">{{ $cerradas }}</p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Vencidas</p><p class="text-2xl font-bold text-[#ca6261]">{{ $vencidas }}</p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Próximas</p><p class="text-2xl font-bold text-[#d4ac5a]">{{ $proximasVencer }}</p></div>
                <a href="{{ route('pqrsf.index') }}" class="cmm-btn-primary flex items-center justify-center">Ver bandeja</a>
            </div>
            <div class="cmm-card p-6">
                <h3 class="mb-4 text-lg font-semibold text-[#624133]">Distribución por tipo</h3>
                <div class="grid gap-3 md:grid-cols-3">
                    @foreach ($porTipo as $tipo => $cantidad)
                        <div class="rounded-lg border border-[#e7e7e7] p-3"><p class="text-sm text-[#4d4d4d]">{{ $tipo }}</p><p class="text-xl font-bold text-[#901227]">{{ $cantidad }}</p></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
