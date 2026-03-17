<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="text-lg font-bold text-[#0f2d5c]">SYSO Portal</a>
                </div>
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Inicio</x-nav-link>
                    @if(auth()->user()->hasRole('Asesor'))
                        <x-nav-link :href="route('pqrsf.index')" :active="request()->routeIs('pqrsf.*')">Mis PQRSF</x-nav-link>
                    @else
                        <x-nav-link :href="route('pqrsf.dashboard')" :active="request()->routeIs('pqrsf.*')">PQRSF</x-nav-link>
                    @endif
                    <x-nav-link :href="route('estado-cuenta.dashboard')" :active="request()->routeIs('estado-cuenta.*')">Estado de cuenta</x-nav-link>
                    @role('Administrador')
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Usuarios</x-nav-link>
                    <x-nav-link :href="route('admin.holding.companies.index')" :active="request()->routeIs('admin.holding.*')">Holding</x-nav-link>
                    @endrole
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-[#4d4d4d] transition hover:text-[#901227] focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-[#4d4d4d] hover:bg-[#f4ecdc] hover:text-[#901227]">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-[#e7e7e7] sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Inicio</x-responsive-nav-link>
            @if(auth()->user()->hasRole('Asesor'))
                <x-responsive-nav-link :href="route('pqrsf.index')" :active="request()->routeIs('pqrsf.*')">Mis PQRSF</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('pqrsf.dashboard')" :active="request()->routeIs('pqrsf.*')">PQRSF</x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('estado-cuenta.dashboard')" :active="request()->routeIs('estado-cuenta.*')">Estado de cuenta</x-responsive-nav-link>
        </div>
    </div>
</nav>
