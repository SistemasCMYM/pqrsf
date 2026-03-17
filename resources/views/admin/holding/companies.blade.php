<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-[#011842]">Holding: empresas y branding</h2></x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))<div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif

            <div class="cmm-card p-6">
                <h3 class="text-lg font-semibold text-[#624133]">Crear empresa del holding</h3>
                <form method="POST" action="{{ route('admin.holding.companies.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 md:grid-cols-3">
                    @csrf
                    <input name="name" placeholder="Nombre" class="rounded-xl border-[#e7e7e7]" required>
                    <input name="slug" placeholder="Slug (ej: syso)" class="rounded-xl border-[#e7e7e7]" required>
                    <input name="tagline" placeholder="Tagline" class="rounded-xl border-[#e7e7e7]">
                    <textarea name="intro" rows="3" placeholder="Introducción para home" class="rounded-xl border-[#e7e7e7] md:col-span-2"></textarea>
                    <input name="support_booking_url" placeholder="Link booking Office 365" class="rounded-xl border-[#e7e7e7]">
                    <div><label class="text-xs">Logo</label><input type="file" name="logo" class="mt-1 w-full rounded-xl border-[#e7e7e7]"></div>
                    <div><label class="text-xs">GIF/animación</label><input type="file" name="animation" class="mt-1 w-full rounded-xl border-[#e7e7e7]"></div>
                    <div class="space-y-1 text-sm">
                        <label class="block"><input type="checkbox" name="is_default" value="1"> Marca por defecto</label>
                        <label class="block"><input type="checkbox" name="active" value="1" checked> Activa</label>
                    </div>
                    <div class="md:col-span-3"><button class="cmm-btn-primary">Crear empresa</button></div>
                </form>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @foreach($companies as $company)
                    <div class="cmm-card p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-[#901227]">{{ $company->name }}</h4>
                                <p class="text-xs text-[#4d4d4d]">/{{ $company->slug }} {{ $company->is_default ? '· por defecto' : '' }}</p>
                                <p class="text-xs text-[#2563eb]">{{ route('brand.login', $company->slug) }}</p>
                            </div>
                            <div class="flex gap-2">
                                @if($company->logo_path)<img src="{{ asset('storage/'.$company->logo_path) }}" class="h-12 w-36 rounded-lg object-contain bg-white/70 p-1" alt="logo">@endif
                                @if($company->animation_path)<img src="{{ asset('storage/'.$company->animation_path) }}" class="h-10 w-16 rounded-lg object-cover" alt="animacion">@endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.holding.companies.update', $company) }}" enctype="multipart/form-data" class="grid gap-2">
                            @csrf @method('PATCH')
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nombre empresa</label>
                                <input name="name" value="{{ $company->name }}" class="w-full rounded-xl border-[#e7e7e7]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Tagline</label>
                                <input name="tagline" value="{{ $company->tagline }}" class="w-full rounded-xl border-[#e7e7e7]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Introducción del Home</label>
                                <textarea name="intro" rows="2" class="w-full rounded-xl border-[#e7e7e7]">{{ $company->intro }}</textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">URL de Booking soporte</label>
                                <input name="support_booking_url" value="{{ $company->support_booking_url }}" class="w-full rounded-xl border-[#e7e7e7]">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Logo</label>
                                    <input type="file" name="logo" class="w-full rounded-xl border-[#e7e7e7] text-xs">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">GIF / Animación</label>
                                    <input type="file" name="animation" class="w-full rounded-xl border-[#e7e7e7] text-xs">
                                </div>
                            </div>
                            <div class="flex gap-4 pt-1 text-sm">
                                <label><input type="checkbox" name="is_default" value="1" @checked($company->is_default)> Por defecto</label>
                                <label><input type="checkbox" name="active" value="1" @checked($company->active)> Activa</label>
                            </div>
                            <button class="cmm-btn-secondary">Guardar cambios</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
