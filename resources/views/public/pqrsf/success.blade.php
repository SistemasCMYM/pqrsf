<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radicado generado</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-[#f4ecdc] px-6">
    <div class="cmm-card w-full max-w-2xl p-8 text-center">
        <p class="text-sm uppercase tracking-[0.2em] text-[#1d5619]">Solicitud registrada</p>
        <h1 class="mt-3 text-3xl font-bold text-[#901227]">Radicado: {{ $radicado }}</h1>
        <p class="mt-4 text-[#4d4d4d]">Conserva este número para seguimiento. Un analista atenderá tu solicitud.</p>
        <a href="{{ route('public.pqrsf.create') }}" class="mt-6 inline-block cmm-btn-primary">Registrar otra PQRSF</a>
    </div>
</body>
</html>
