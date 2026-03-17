<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro PQRSF</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4ecdc]">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-[#901227]">Formulario Público PQRSF</h1>
            <a href="{{ route('home') }}" class="rounded-lg border border-[#4b729f] px-4 py-2 text-sm text-[#4b729f]">Inicio</a>
        </div>

        <div class="cmm-card p-6">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('public.pqrsf.store') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#624133]">Tipo de solicitud</label>
                    <select name="pqrsf_tipo_id" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required>
                        <option value="">Seleccione</option>
                        @foreach ($tipos as $tipo)<option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#624133]">Destinatario</label>
                    <select name="destinatario_id" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required>
                        <option value="">Seleccione área</option>
                        @foreach ($destinatarios as $destinatario)<option value="{{ $destinatario->id }}">{{ $destinatario->nombre }}</option>@endforeach
                    </select>
                </div>
                <div><label class="block text-sm">Nombres</label><input name="nombres" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></div>
                <div><label class="block text-sm">Apellidos</label><input name="apellidos" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></div>
                <div><label class="block text-sm">Tipo documento</label><input name="tipo_documento" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></div>
                <div><label class="block text-sm">Número documento</label><input name="numero_documento" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></div>
                <div><label class="block text-sm">Correo</label><input type="email" name="email" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></div>
                <div><label class="block text-sm">Teléfono</label><input name="telefono" class="mt-1 w-full rounded-lg border-[#e7e7e7]"></div>
                <div class="md:col-span-2"><label class="block text-sm">Ciudad</label><input name="ciudad" class="mt-1 w-full rounded-lg border-[#e7e7e7]"></div>
                <div class="md:col-span-2"><label class="block text-sm">Asunto</label><input name="asunto" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></div>
                <div class="md:col-span-2"><label class="block text-sm">Descripción detallada</label><textarea name="descripcion" rows="6" class="mt-1 w-full rounded-lg border-[#e7e7e7]" required></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm">Adjuntos (máx 5)</label><input type="file" name="files[]" multiple class="mt-1 w-full rounded-lg border-[#e7e7e7]"></div>
                <label class="md:col-span-2 flex items-start gap-2 text-sm"><input type="checkbox" name="acepta_tratamiento_datos" value="1" class="mt-1 rounded border-[#e7e7e7]" required> Acepto el tratamiento de datos personales.</label>
                <div class="md:col-span-2"><button class="cmm-btn-primary">Enviar PQRSF</button></div>
            </form>
        </div>
    </div>
</body>
</html>
