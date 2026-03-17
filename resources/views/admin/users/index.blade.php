<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-[#901227]">Administrador de usuarios</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif

            <div class="cmm-card p-5">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 md:grid-cols-5">
                    <input name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, correo o documento" class="rounded-lg border-[#e7e7e7] md:col-span-2">
                    <select name="role" class="rounded-lg border-[#e7e7e7]">
                        <option value="">Rol</option>
                        @foreach($roles as $role)<option value="{{ $role }}" @selected(request('role')===$role)>{{ $role }}</option>@endforeach
                    </select>
                    <select name="status" class="rounded-lg border-[#e7e7e7]">
                        <option value="">Estado</option>
                        <option value="active" @selected(request('status')==='active')>Activo</option>
                        <option value="inactive" @selected(request('status')==='inactive')>Inactivo</option>
                    </select>
                    <div class="flex gap-2">
                        <button class="cmm-btn-primary w-full">Buscar</button>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[#e7e7e7] px-4 text-sm">Limpiar</a>
                    </div>
                </form>
            </div>

            <div class="cmm-card p-6">
                <h3 class="text-lg font-semibold text-[#624133]">Crear usuario</h3>
                <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 grid gap-3 md:grid-cols-3">
                    @csrf
                    <input name="name" placeholder="Nombre" class="rounded-lg border-[#e7e7e7]" required>
                    <input name="email" placeholder="Correo" class="rounded-lg border-[#e7e7e7]" required>
                    <input name="document_type" placeholder="Tipo documento" class="rounded-lg border-[#e7e7e7]" required>
                    <input name="document_number" placeholder="Número documento" class="rounded-lg border-[#e7e7e7]" required>
                    <input name="phone" placeholder="Teléfono" class="rounded-lg border-[#e7e7e7]">
                    <input name="city" placeholder="Ciudad" class="rounded-lg border-[#e7e7e7]">
                    <input type="password" name="password" placeholder="Contraseña" class="rounded-lg border-[#e7e7e7]" required>
                    <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" class="rounded-lg border-[#e7e7e7]" required>
                    <select name="roles[]" multiple class="rounded-lg border-[#e7e7e7] h-28" required>
                        @foreach($roles as $role)<option value="{{ $role }}">{{ $role }}</option>@endforeach
                    </select>
                    <div class="md:col-span-3"><button class="cmm-btn-primary">Crear usuario</button></div>
                </form>
            </div>

            <div class="cmm-card p-6">
                <h3 class="text-lg font-semibold text-[#624133]">Carga masiva de usuarios</h3>
                <p class="mt-1 text-xs text-[#4d4d4d]">Campos mínimos: <code>email</code> y <code>document_number</code>. Si falta <code>name</code>, el sistema lo genera automáticamente.</p>
                <p class="mt-1 text-xs text-[#4d4d4d]">Columnas recomendadas: <code>name,email,document_type,document_number,phone,city,roles,password,status</code>. Roles separados por coma.</p>
                <a href="{{ route('admin.users.template') }}" class="mt-3 inline-flex rounded-lg border border-[#2563eb] px-3 py-1.5 text-xs font-semibold text-[#2563eb]">Descargar plantilla CSV</a>
                <form method="POST" action="{{ route('admin.users.bulk') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 md:grid-cols-3">
                    @csrf
                    <input type="file" name="archivo" class="rounded-lg border-[#e7e7e7]" required>
                    <select name="default_role" class="rounded-lg border-[#e7e7e7]">
                        <option value="">Rol por defecto (opcional)</option>
                        @foreach($roles as $role)<option value="{{ $role }}">{{ $role }}</option>@endforeach
                    </select>
                    <button class="cmm-btn-primary">Procesar carga masiva</button>
                </form>
            </div>

            <div class="cmm-card overflow-hidden">
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc]"><tr><th class="px-4 py-3 text-left">Usuario</th><th class="px-4 py-3">Documento</th><th class="px-4 py-3">Roles</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3"></th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                        @foreach($users as $u)
                        <tr>
                            <td class="px-4 py-3">{{ $u->name }}<div class="text-xs text-[#4d4d4d]">{{ $u->email }}</div></td>
                            <td class="px-4 py-3 text-center">{{ $u->document_type }} {{ $u->document_number }}</td>
                            <td class="px-4 py-3 text-center">{{ $u->roles->pluck('name')->join(', ') }}</td>
                            <td class="px-4 py-3 text-center">{{ $u->status }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.users.update', $u) }}" class="grid items-center gap-2 md:grid-cols-2">
                                    @csrf @method('PATCH')
                                    <input type="email" name="email" value="{{ old('email', $u->email) }}" placeholder="Correo" class="rounded-lg border-[#e7e7e7] text-xs md:col-span-2" required>
                                    <select name="roles[]" multiple class="rounded-lg border-[#e7e7e7] h-20 text-xs" required>
                                        @foreach($roles as $role)<option value="{{ $role }}" @selected($u->roles->contains('name', $role))>{{ $role }}</option>@endforeach
                                    </select>
                                    <select name="status" class="rounded-lg border-[#e7e7e7] text-xs">
                                        <option value="active" @selected($u->status==='active')>Activo</option>
                                        <option value="inactive" @selected($u->status==='inactive')>Inactivo</option>
                                    </select>
                                    <input type="password" name="password" placeholder="Nueva contraseña (opcional)" class="rounded-lg border-[#e7e7e7] text-xs">
                                    <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" class="rounded-lg border-[#e7e7e7] text-xs">
                                    <button class="cmm-btn-secondary text-xs md:col-span-2">Actualizar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
