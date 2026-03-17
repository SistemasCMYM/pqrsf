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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-xl font-semibold text-[#901227]">Bandeja de PQRSF</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?><div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
            <form method="GET" class="grid gap-3 cmm-card p-4 md:grid-cols-7">
                <input name="radicado" value="<?php echo e(request('radicado')); ?>" placeholder="Radicado" class="rounded-lg border-[#e7e7e7]">
                <select name="pqrsf_tipo_id" class="rounded-lg border-[#e7e7e7]"><option value="">Tipo</option><?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>" <?php if(request('pqrsf_tipo_id')==$t->id): echo 'selected'; endif; ?>><?php echo e($t->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <select name="destinatario_id" class="rounded-lg border-[#e7e7e7]"><option value="">Destinatario</option><?php $__currentLoopData = $destinatarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d->id); ?>" <?php if(request('destinatario_id')==$d->id): echo 'selected'; endif; ?>><?php echo e($d->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <select name="pqrsf_estado_id" class="rounded-lg border-[#e7e7e7]"><option value="">Estado</option><?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>" <?php if(request('pqrsf_estado_id')==$e->id): echo 'selected'; endif; ?>><?php echo e($e->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <input name="documento" value="<?php echo e(request('documento')); ?>" placeholder="Documento" class="rounded-lg border-[#e7e7e7]">
                <input name="nombre" value="<?php echo e(request('nombre')); ?>" placeholder="Nombre" class="rounded-lg border-[#e7e7e7]">
                <button class="cmm-btn-primary">Filtrar</button>
            </form>

            <div class="overflow-hidden cmm-card">
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc] text-[#624133]"><tr><th class="px-4 py-3 text-left">Radicado</th><th class="px-4 py-3 text-left">Solicitante</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3">Destinatario</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Responsable</th><th class="px-4 py-3">SLA</th><th class="px-4 py-3"></th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-[#901227]"><?php echo e($item->radicado); ?></td>
                            <td class="px-4 py-3"><?php echo e($item->nombres); ?> <?php echo e($item->apellidos); ?></td>
                            <td class="px-4 py-3 text-center"><?php echo e($item->tipo?->nombre); ?></td>
                            <td class="px-4 py-3 text-center"><?php echo e($item->destinatario?->nombre ?? 'Sin área'); ?></td>
                            <td class="px-4 py-3 text-center"><span class="cmm-badge"><?php echo e($item->estado?->nombre); ?></span></td>
                            <td class="px-4 py-3 text-center"><?php echo e($item->asignadoA?->name ?? 'Sin asignar'); ?></td>
                            <td class="px-4 py-3 text-center"><?php if($item->dias_restantes !== null): ?><span class="<?php echo e($item->dias_restantes < 0 ? 'text-[#ca6261]' : ($item->dias_restantes <= 2 ? 'text-[#d4ac5a]' : 'text-[#1d5619]')); ?> font-semibold"><?php echo e($item->dias_restantes); ?> días</span><?php else: ?> - <?php endif; ?></td>
                            <td class="px-4 py-3 text-right"><a href="<?php echo e(route('pqrsf.show', $item)); ?>" class="text-[#4b729f] hover:underline">Gestionar</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="px-4 py-8 text-center text-[#4d4d4d]">Sin resultados</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php echo e($items->links()); ?>

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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/pqrsf/index.blade.php ENDPATH**/ ?>