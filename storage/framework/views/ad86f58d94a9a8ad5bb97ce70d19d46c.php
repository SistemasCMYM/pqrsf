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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-xl font-semibold text-[#36574e]">Administración Estado de Cuenta</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?><div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
            <?php if(session('error')): ?><div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800"><?php echo e(session('error')); ?></div><?php endif; ?>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Importar Excel</h3>
                    <p class="mt-1 text-xs text-[#4d4d4d]">Puedes cargar archivos con resumen o solo detalle. El sistema consolida automáticamente.</p>
                    <form method="POST" action="<?php echo e(route('estado-cuenta.admin.import')); ?>" enctype="multipart/form-data" class="mt-4 space-y-3">
                        <?php echo csrf_field(); ?>
                        <input type="file" name="archivo" class="w-full rounded-lg border-[#e7e7e7]" required>
                        <input name="anio" placeholder="Año de carga" class="w-full rounded-lg border-[#e7e7e7]">
                        <button class="cmm-btn-primary">Procesar archivo</button>
                    </form>
                </div>

                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Configuración de integración</h3>
                    <form method="POST" action="<?php echo e(route('estado-cuenta.admin.config.update')); ?>" class="mt-4 grid gap-3">
                        <?php echo csrf_field(); ?>
                        <select name="fuente_activa" class="rounded-lg border-[#e7e7e7]">
                            <option value="excel" <?php if(optional($config)->fuente_activa==='excel'): echo 'selected'; endif; ?>>Excel</option>
                            <option value="api" <?php if(optional($config)->fuente_activa==='api'): echo 'selected'; endif; ?>>API</option>
                        </select>
                        <input name="api_base_url" value="<?php echo e(old('api_base_url', $config->api_base_url ?? '')); ?>" placeholder="API Base URL" class="rounded-lg border-[#e7e7e7]">
                        <input name="api_token" value="<?php echo e(old('api_token', $config->api_token ?? '')); ?>" placeholder="API Token" class="rounded-lg border-[#e7e7e7]">
                        <input name="api_timeout" value="<?php echo e(old('api_timeout', $config->api_timeout ?? 15)); ?>" placeholder="Timeout" class="rounded-lg border-[#e7e7e7]">
                        <button class="cmm-btn-secondary">Guardar configuración</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('estado-cuenta.admin.sync-api')); ?>" class="mt-4"><?php echo csrf_field(); ?><button class="rounded-lg border border-[#4b729f] px-4 py-2 text-sm text-[#4b729f]">Sincronizar API manual</button></form>
                </div>
            </div>

            <div class="cmm-card p-6">
                <h3 class="text-lg font-semibold text-[#624133]">Últimas importaciones</h3>
                <div class="mt-3 space-y-2 text-sm">
                    <?php $__currentLoopData = $imports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $import): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border border-[#e7e7e7] p-3">
                            <p class="font-semibold"><?php echo e($import->nombre_archivo); ?></p>
                            <p class="text-[#4d4d4d]">Estado: <?php echo e($import->estado); ?> | Total: <?php echo e($import->total_registros); ?> | Procesados: <?php echo e($import->procesados); ?> | Fecha: <?php echo e(optional($import->fecha_importacion)->format('Y-m-d H:i')); ?></p>
                        </div>
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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/estado-cuenta/admin/index.blade.php ENDPATH**/ ?>