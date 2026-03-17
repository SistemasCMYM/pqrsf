<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(optional($activeBrand)->name ? optional($activeBrand)->name.' | Portal' : config('app.name', 'Portal')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen">
    <?php
        $brand = $activeBrand ?? null;
        $supportUrl = optional($brand)->support_booking_url ?: env('SUPPORT_BOOKING_URL', '#');
        $logoUrl = optional($brand)->logo_path ? asset('storage/'.optional($brand)->logo_path) : null;
        $animUrl = optional($brand)->animation_path ? asset('storage/'.optional($brand)->animation_path) : null;
    ?>

    <header class="mx-auto max-w-7xl px-6 pt-8 lg:px-10">
        <div class="home-glass flex items-center justify-between p-4">
            <div class="flex items-center gap-3">
                <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="logo" class="h-12 w-36 rounded-xl bg-white/80 object-contain p-1">
                <?php else: ?>
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-[#1d4ed8] to-[#0f766e] text-sm font-bold text-white">SY</div>
                <?php endif; ?>
            </div>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="cmm-btn-secondary">Ir al panel</a>
            <?php else: ?>
                <a href="<?php echo e(route('brand.login', ['slug' => optional($brand)->slug ?? 'syso'])); ?>" class="cmm-btn-secondary">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 pb-12 pt-8 lg:px-10">
        <section class="home-glass relative overflow-hidden p-8 md:p-10">
            <div class="absolute -right-12 -top-12 h-56 w-56 rounded-full bg-[#1d4ed8]/20 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 h-56 w-56 rounded-full bg-[#0f766e]/20 blur-2xl"></div>

            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb]">Bienvenido</p>
            <h1 class="mt-3 max-w-4xl text-3xl font-semibold leading-tight text-[#0f2d5c] md:text-5xl">
                Te acompañamos en la gestión de tus solicitudes y consultas
            </h1>
            <p class="mt-4 max-w-3xl text-base text-[#334155]">
                <?php echo e(optional($brand)->intro ?? 'Aquí puedes registrar una PQRSF, consultar tu estado de cuenta y agendar una sesión de soporte con un consultor.'); ?>

            </p>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <a href="<?php echo e(route('public.pqrsf.create')); ?>" class="home-action">
                    <p class="text-sm font-semibold text-[#0f2d5c]">Registrar PQRSF</p>
                    <p class="mt-1 text-xs text-[#475569]">Peticiones, quejas, reclamos, sugerencias y felicitaciones.</p>
                    <span class="mt-3 inline-flex rounded-xl bg-[#0f2d5c] px-3 py-1.5 text-xs font-semibold text-white">Ir al formulario</span>
                </a>
                <a href="<?php echo e(route('estado-cuenta.dashboard')); ?>" class="home-action">
                    <p class="text-sm font-semibold text-[#0f766e]">Consultar estado de cuenta</p>
                    <p class="mt-1 text-xs text-[#475569]">Visualiza anticipos, legalizado, sin legalizar y detalle histórico.</p>
                    <span class="mt-3 inline-flex rounded-xl bg-[#0f766e] px-3 py-1.5 text-xs font-semibold text-white">Ir a consulta</span>
                </a>
                <a href="<?php echo e($supportUrl); ?>" target="_blank" rel="noopener" class="home-action">
                    <p class="text-sm font-semibold text-[#2563eb]">Agendar soporte</p>
                    <p class="mt-1 text-xs text-[#475569]">Programa una sesión con un consultor por Microsoft Booking.</p>
                    <span class="mt-3 inline-flex rounded-xl bg-[#2563eb] px-3 py-1.5 text-xs font-semibold text-white">Agendar cita</span>
                </a>
            </div>

            <?php if($animUrl): ?>
                <div class="mt-7 rounded-2xl border border-white/80 bg-white/60 p-2">
                    <img src="<?php echo e($animUrl); ?>" alt="animación" class="h-44 w-full rounded-xl object-cover">
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
<?php /**PATH /home/sysocoqv/pqrs/resources/views/welcome.blade.php ENDPATH**/ ?>