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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-xl font-semibold text-slate-800">Detalle <?php echo e($pqrsf->radicado); ?></h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="space-y-6 lg:col-span-2">
                <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div><?php endif; ?>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold">Información general</h3>
                    <dl class="mt-4 grid gap-2 text-sm md:grid-cols-2">
                        <div><dt class="text-slate-500">Solicitante</dt><dd><?php echo e($pqrsf->nombres); ?> <?php echo e($pqrsf->apellidos); ?></dd></div>
                        <div><dt class="text-slate-500">Documento</dt><dd><?php echo e($pqrsf->tipo_documento); ?> <?php echo e($pqrsf->numero_documento); ?></dd></div>
                        <div><dt class="text-slate-500">Tipo</dt><dd><?php echo e($pqrsf->tipo?->nombre); ?></dd></div>
                        <div><dt class="text-slate-500">Destinatario</dt><dd><?php echo e($pqrsf->destinatario?->nombre ?? 'Sin área'); ?></dd></div>
                        <div><dt class="text-slate-500">Estado</dt><dd><?php echo e($pqrsf->estado?->nombre); ?></dd></div>
                        <div class="md:col-span-2"><dt class="text-slate-500">Asunto</dt><dd><?php echo e($pqrsf->asunto); ?></dd></div>
                        <div class="md:col-span-2"><dt class="text-slate-500">Descripción</dt><dd><?php echo e($pqrsf->descripcion); ?></dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="mb-3 text-lg font-semibold">Trazabilidad</h3>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $pqrsf->historial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-lg border border-slate-200 p-3 text-sm">
                                <p class="font-semibold text-slate-800"><?php echo e($h->accion); ?> - <?php echo e(optional($h->created_at)->format('Y-m-d H:i')); ?></p>
                                <p class="text-slate-600"><?php echo e($h->observacion ?? 'Sin observación'); ?></p>
                                <p class="text-xs text-slate-500">Usuario: <?php echo e($h->user?->name ?? 'Sistema'); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $pqrsf)): ?>
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold">Gestión</h3>
                        <form method="POST" action="<?php echo e(route('pqrsf.update', $pqrsf)); ?>" class="mt-4 space-y-3">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <select name="pqrsf_estado_id" class="w-full rounded-lg border-slate-300">
                                <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>" <?php if($pqrsf->pqrsf_estado_id==$e->id): echo 'selected'; endif; ?>><?php echo e($e->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select name="destinatario_id" class="w-full rounded-lg border-slate-300">
                                <?php $__currentLoopData = $destinatarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d->id); ?>" <?php if($pqrsf->destinatario_id==$d->id): echo 'selected'; endif; ?>><?php echo e($d->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select name="asignado_a" class="w-full rounded-lg border-slate-300"><option value="">Sin asignar</option><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>" <?php if($pqrsf->asignado_a==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                            <select name="prioridad" class="w-full rounded-lg border-slate-300">
                                <?php $__currentLoopData = ['baja','media','alta','critica']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>" <?php if($pqrsf->prioridad==$p): echo 'selected'; endif; ?>><?php echo e(ucfirst($p)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <textarea name="observacion" class="w-full rounded-lg border-slate-300" placeholder="Observación de cambio"></textarea>
                            <button class="w-full rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Actualizar</button>
                        </form>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold">Registrar nota/respuesta</h3>
                        <form method="POST" action="<?php echo e(route('pqrsf.responses.store', $pqrsf)); ?>" class="mt-4 space-y-3">
                            <?php echo csrf_field(); ?>
                            <select name="tipo" class="w-full rounded-lg border-slate-300">
                                <option value="nota_interna">Nota interna</option>
                                <option value="respuesta_ciudadano">Respuesta al ciudadano</option>
                                <option value="cierre">Cierre de caso</option>
                            </select>
                            <textarea name="mensaje" class="w-full rounded-lg border-slate-300" rows="4" required></textarea>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="notificado" value="1"> Notificar al solicitante</label>
                            <button class="w-full rounded-lg bg-cyan-600 px-4 py-2 font-semibold text-white">Guardar</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold">Seguimiento</h3>
                        <p class="mt-2 text-sm text-slate-600">Puedes consultar el estado y trazabilidad de tu PQRSF. Para cambios internos, un gestor realizará la actualización.</p>
                    </div>
                <?php endif; ?>
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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/pqrsf/show.blade.php ENDPATH**/ ?>