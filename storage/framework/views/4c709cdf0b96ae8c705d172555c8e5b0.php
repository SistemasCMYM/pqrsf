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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-xl font-semibold text-[#901227]">Configuración PQRSF</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?><div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
            <?php if($errors->any()): ?><div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800"><?php echo e($errors->first()); ?></div><?php endif; ?>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Destinatarios (áreas)</h3>
                    <form method="POST" action="<?php echo e(route('pqrsf.config.destinatarios.store')); ?>" class="mt-4 grid gap-3">
                        <?php echo csrf_field(); ?>
                        <input name="nombre" class="rounded-lg border-[#e7e7e7]" placeholder="Ej: Gestión Humana" required>
                        <select name="responsable_user_id" class="rounded-lg border-[#e7e7e7]">
                            <option value="">Sin responsable por defecto</option>
                            <?php $__currentLoopData = $responsables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <label class="text-sm"><input type="checkbox" name="activo" value="1" checked> Activo</label>
                        <button class="cmm-btn-primary">Crear destinatario</button>
                    </form>
                    <div class="mt-4 space-y-2">
                        <?php $__currentLoopData = $destinatarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <form method="POST" action="<?php echo e(route('pqrsf.config.destinatarios.update', $d)); ?>" class="grid grid-cols-1 gap-2 rounded-lg border border-[#e7e7e7] p-3 md:grid-cols-4">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <div class="md:col-span-2 text-sm font-semibold"><?php echo e($d->nombre); ?></div>
                                <select name="responsable_user_id" class="rounded-lg border-[#e7e7e7] text-sm">
                                    <option value="">Sin responsable</option>
                                    <?php $__currentLoopData = $responsables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>" <?php if($d->responsable_user_id==$r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs"><input type="checkbox" name="activo" value="1" <?php if($d->activo): echo 'checked'; endif; ?>> Activo</label>
                                    <button class="cmm-btn-secondary text-xs">Guardar</button>
                                </div>
                            </form>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Parametrizar tiempos SLA</h3>
                    <form method="POST" action="<?php echo e(route('pqrsf.config.sla')); ?>" class="mt-4 grid gap-3">
                        <?php echo csrf_field(); ?>
                        <select name="pqrsf_tipo_id" class="rounded-lg border-[#e7e7e7]" required>
                            <option value="">Tipo de PQRSF</option>
                            <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="prioridad" class="rounded-lg border-[#e7e7e7]" required>
                            <?php $__currentLoopData = ['baja','media','alta','critica']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>"><?php echo e(ucfirst($p)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input type="number" name="dias_respuesta" class="rounded-lg border-[#e7e7e7]" placeholder="Días de respuesta" required>
                        <button class="cmm-btn-primary">Guardar SLA</button>
                    </form>
                </div>

                <div class="cmm-card p-6">
                    <h3 class="text-lg font-semibold text-[#624133]">Responsables por tipo (líneas)</h3>
                    <form method="POST" action="<?php echo e(route('pqrsf.config.responsables')); ?>" class="mt-4 grid gap-3">
                        <?php echo csrf_field(); ?>
                        <select name="pqrsf_tipo_id" class="rounded-lg border-[#e7e7e7]" required>
                            <option value="">Tipo de PQRSF</option>
                            <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="responsables[]" multiple class="rounded-lg border-[#e7e7e7] h-36">
                            <?php $__currentLoopData = $responsables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button class="cmm-btn-secondary">Guardar responsables</button>
                    </form>
                </div>
            </div>

            <div class="cmm-card p-6">
                <h3 class="mb-3 text-lg font-semibold text-[#624133]">Resumen actual SLA</h3>
                <table class="min-w-full divide-y divide-[#e7e7e7] text-sm">
                    <thead class="bg-[#f4ecdc]"><tr><th class="px-3 py-2 text-left">Tipo</th><th class="px-3 py-2">Prioridad</th><th class="px-3 py-2">Días</th></tr></thead>
                    <tbody class="divide-y divide-[#e7e7e7]">
                        <?php $__currentLoopData = $slas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td class="px-3 py-2"><?php echo e($sla->tipo?->nombre); ?></td><td class="px-3 py-2 text-center"><?php echo e(ucfirst($sla->prioridad)); ?></td><td class="px-3 py-2 text-center"><?php echo e($sla->dias_respuesta); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/pqrsf/config.blade.php ENDPATH**/ ?>