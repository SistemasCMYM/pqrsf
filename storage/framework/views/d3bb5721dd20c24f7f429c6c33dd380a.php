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
     <?php $__env->slot('header', null, []); ?> <div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-[#901227]">Dashboard PQRSF</h2><a href="<?php echo e(route('pqrsf.config.index')); ?>" class="cmm-btn-secondary">Configurar SLA y responsables</a></div> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Total</p><p class="text-2xl font-bold text-[#901227]"><?php echo e($total); ?></p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Abiertas</p><p class="text-2xl font-bold text-[#4b729f]"><?php echo e($abiertas); ?></p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Cerradas</p><p class="text-2xl font-bold text-[#1d5619]"><?php echo e($cerradas); ?></p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Vencidas</p><p class="text-2xl font-bold text-[#ca6261]"><?php echo e($vencidas); ?></p></div>
                <div class="cmm-card p-4"><p class="text-xs text-[#4b729f]">Próximas</p><p class="text-2xl font-bold text-[#d4ac5a]"><?php echo e($proximasVencer); ?></p></div>
                <a href="<?php echo e(route('pqrsf.index')); ?>" class="cmm-btn-primary flex items-center justify-center">Ver bandeja</a>
            </div>
            <div class="cmm-card p-6">
                <h3 class="mb-4 text-lg font-semibold text-[#624133]">Distribución por tipo</h3>
                <div class="grid gap-3 md:grid-cols-3">
                    <?php $__currentLoopData = $porTipo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $cantidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border border-[#e7e7e7] p-3"><p class="text-sm text-[#4d4d4d]"><?php echo e($tipo); ?></p><p class="text-xl font-bold text-[#901227]"><?php echo e($cantidad); ?></p></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/pqrsf/dashboard.blade.php ENDPATH**/ ?>