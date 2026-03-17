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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-xl font-semibold text-[#011842]">Holding: empresas y branding</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <?php if(session('success')): ?><div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
            <?php if($errors->any()): ?><div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800"><?php echo e($errors->first()); ?></div><?php endif; ?>

            <div class="cmm-card p-6">
                <h3 class="text-lg font-semibold text-[#624133]">Crear empresa del holding</h3>
                <form method="POST" action="<?php echo e(route('admin.holding.companies.store')); ?>" enctype="multipart/form-data" class="mt-4 grid gap-3 md:grid-cols-3">
                    <?php echo csrf_field(); ?>
                    <input name="name" placeholder="Nombre" class="rounded-xl border-[#e7e7e7]" required>
                    <input name="slug" placeholder="Slug (ej: syso)" class="rounded-xl border-[#e7e7e7]" required>
                    <input name="tagline" placeholder="Tagline" class="rounded-xl border-[#e7e7e7]">
                    <textarea name="intro" rows="3" placeholder="Introducción para home" class="rounded-xl border-[#e7e7e7] md:col-span-2"></textarea>
                    <input name="support_booking_url" placeholder="Link booking Office 365" class="rounded-xl border-[#e7e7e7]">
                    <div><label class="text-xs">Logo</label><input type="file" name="logo" class="mt-1 w-full rounded-xl border-[#e7e7e7]"></div>
                    <div><label class="text-xs">GIF/animación</label><input type="file" name="animation" class="mt-1 w-full rounded-xl border-[#e7e7e7]"></div>
                    <div class="space-y-1 text-sm">
                        <label class="block"><input type="checkbox" name="is_default" value="1"> Marca por defecto</label>
                        <label class="block"><input type="checkbox" name="active" value="1" checked> Activa</label>
                    </div>
                    <div class="md:col-span-3"><button class="cmm-btn-primary">Crear empresa</button></div>
                </form>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cmm-card p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-[#901227]"><?php echo e($company->name); ?></h4>
                                <p class="text-xs text-[#4d4d4d]">/<?php echo e($company->slug); ?> <?php echo e($company->is_default ? '· por defecto' : ''); ?></p>
                                <p class="text-xs text-[#2563eb]"><?php echo e(route('brand.login', $company->slug)); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <?php if($company->logo_path): ?><img src="<?php echo e(asset('storage/'.$company->logo_path)); ?>" class="h-12 w-36 rounded-lg object-contain bg-white/70 p-1" alt="logo"><?php endif; ?>
                                <?php if($company->animation_path): ?><img src="<?php echo e(asset('storage/'.$company->animation_path)); ?>" class="h-10 w-16 rounded-lg object-cover" alt="animacion"><?php endif; ?>
                            </div>
                        </div>
                        <form method="POST" action="<?php echo e(route('admin.holding.companies.update', $company)); ?>" enctype="multipart/form-data" class="grid gap-2">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nombre empresa</label>
                                <input name="name" value="<?php echo e($company->name); ?>" class="w-full rounded-xl border-[#e7e7e7]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Tagline</label>
                                <input name="tagline" value="<?php echo e($company->tagline); ?>" class="w-full rounded-xl border-[#e7e7e7]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Introducción del Home</label>
                                <textarea name="intro" rows="2" class="w-full rounded-xl border-[#e7e7e7]"><?php echo e($company->intro); ?></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">URL de Booking soporte</label>
                                <input name="support_booking_url" value="<?php echo e($company->support_booking_url); ?>" class="w-full rounded-xl border-[#e7e7e7]">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Logo</label>
                                    <input type="file" name="logo" class="w-full rounded-xl border-[#e7e7e7] text-xs">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">GIF / Animación</label>
                                    <input type="file" name="animation" class="w-full rounded-xl border-[#e7e7e7] text-xs">
                                </div>
                            </div>
                            <div class="flex gap-4 pt-1 text-sm">
                                <label><input type="checkbox" name="is_default" value="1" <?php if($company->is_default): echo 'checked'; endif; ?>> Por defecto</label>
                                <label><input type="checkbox" name="active" value="1" <?php if($company->active): echo 'checked'; endif; ?>> Activa</label>
                            </div>
                            <button class="cmm-btn-secondary">Guardar cambios</button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH /home/sysocoqv/pqrs/resources/views/admin/holding/companies.blade.php ENDPATH**/ ?>