<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="text-xl font-semibold leading-tight text-[#0f2d5c]">
            <?php echo e(auth()->user()->hasRole('Asesor') ? 'Panel de asesor' : 'Dashboard administrativo'); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 <?php echo e(auth()->user()->hasRole('Asesor') ? 'lg:grid-cols-2' : 'lg:grid-cols-3'); ?> lg:px-8">
            <a href="<?php echo e(auth()->user()->hasRole('Asesor') ? route('pqrsf.index') : route('pqrsf.dashboard')); ?>" class="cmm-card p-6 hover:shadow-md">
                <p class="text-xs uppercase tracking-[0.2em] text-[#4b729f]">Módulo 1</p>
                <h3 class="mt-2 text-2xl font-semibold text-[#901227]">Gestión PQRSF</h3>
                <p class="mt-3 text-sm text-[#4d4d4d]">
                    <?php echo e(auth()->user()->hasRole('Asesor') ? 'Consulta el estado de tus solicitudes registradas.' : 'Bandeja, trazabilidad, SLA, asignación y cierre.'); ?>

                </p>
            </a>
            <a href="<?php echo e(route('estado-cuenta.dashboard')); ?>" class="cmm-card p-6 hover:shadow-md">
                <p class="text-xs uppercase tracking-[0.2em] text-[#4b729f]">Módulo 2</p>
                <h3 class="mt-2 text-2xl font-semibold text-[#36574e]">Estado de Cuenta SYSO</h3>
                <p class="mt-3 text-sm text-[#4d4d4d]">Consulta por cédula, resumen y detalle de movimientos.</p>
            </a>
            <?php if(auth()->user()->hasAnyRole(['Administrador','Coordinador Estado Cuenta'])): ?>
                <a href="<?php echo e(route('estado-cuenta.admin.index')); ?>" class="cmm-card p-6 hover:shadow-md">
                    <p class="text-xs uppercase tracking-[0.2em] text-[#4b729f]">Operación</p>
                    <h3 class="mt-2 text-2xl font-semibold text-[#624133]">Panel de integraciones</h3>
                    <p class="mt-3 text-sm text-[#4d4d4d]">Importación Excel, configuración y sincronización API.</p>
                </a>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/sysocoqv/pqrs/resources/views/dashboard.blade.php ENDPATH**/ ?>