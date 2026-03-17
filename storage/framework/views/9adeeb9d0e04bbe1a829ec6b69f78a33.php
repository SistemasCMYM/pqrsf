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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-xl font-semibold text-[#36574e]">Estado de Cuenta Proyectos SYSO</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if($error): ?><div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"><?php echo e($error); ?></div><?php endif; ?>
            <form class="grid gap-3 cmm-card p-4 md:grid-cols-6">
                <?php if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Coordinador Estado Cuenta')): ?>
                    <input name="cedula" value="<?php echo e($cedula); ?>" placeholder="Cédula" class="rounded-lg border-[#e7e7e7]">
                <?php endif; ?>
                <input name="anio" value="<?php echo e($filters['anio'] ?? ''); ?>" placeholder="Año" class="rounded-lg border-[#e7e7e7]">
                <input name="mes" value="<?php echo e($filters['mes'] ?? ''); ?>" placeholder="Mes" class="rounded-lg border-[#e7e7e7]">
                <input name="estado" value="<?php echo e($filters['estado'] ?? ''); ?>" placeholder="Estado" class="rounded-lg border-[#e7e7e7]">
                <input name="municipio" value="<?php echo e($filters['municipio'] ?? ''); ?>" placeholder="Municipio" class="rounded-lg border-[#e7e7e7]">
                <button class="cmm-btn-primary">Aplicar filtros</button>
            </form>

            <?php if($resumen): ?>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total anticipos</p><p class="text-xl font-bold text-[#901227]">$<?php echo e(number_format((float)$resumen->anticipos_adiciones, 0, ',', '.')); ?></p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total legalizado</p><p class="text-xl font-bold text-[#36574e]">$<?php echo e(number_format((float)$resumen->legalizado_devoluciones, 0, ',', '.')); ?></p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Sin legalizar</p><p class="text-xl font-bold text-[#ca6261]">$<?php echo e(number_format((float)$resumen->sin_legalizar, 0, ',', '.')); ?></p></div>
                    <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Estado saldo</p><p class="text-sm font-semibold text-[#624133]"><?php echo e($resumen->estado_saldo); ?></p></div>
                </div>
            <?php endif; ?>

            <div class="overflow-hidden cmm-card">
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc] text-[#624133]"><tr><th class="px-4 py-3 text-left">Fecha ida</th><th class="px-4 py-3 text-left">Destino</th><th class="px-4 py-3 text-right">Anticipo</th><th class="px-4 py-3 text-right">Legalizado</th><th class="px-4 py-3 text-right">Saldo</th><th class="px-4 py-3 text-center">Estado</th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                        <?php $__empty_1 = true; $__currentLoopData = $detalle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3"><?php echo e(optional($d->fecha_ida)->format('Y-m-d')); ?></td>
                                <td class="px-4 py-3"><?php echo e($d->municipio_destino); ?></td>
                                <td class="px-4 py-3 text-right">$<?php echo e(number_format((float)$d->anticipo, 0, ',', '.')); ?></td>
                                <td class="px-4 py-3 text-right">$<?php echo e(number_format((float)$d->legalizado, 0, ',', '.')); ?></td>
                                <td class="px-4 py-3 text-right">$<?php echo e(number_format((float)$d->saldo_pendiente, 0, ',', '.')); ?></td>
                                <td class="px-4 py-3 text-center"><span class="cmm-badge"><?php echo e($d->estado); ?></span></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="px-4 py-8 text-center text-[#4d4d4d]">No hay registros para la consulta actual.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/estado-cuenta/dashboard.blade.php ENDPATH**/ ?>