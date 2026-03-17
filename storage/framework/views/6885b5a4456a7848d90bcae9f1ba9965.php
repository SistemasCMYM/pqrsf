<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-slate-100 to-slate-50">
            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white/95 shadow-sm ring-1 ring-slate-200 overflow-hidden rounded-3xl">
                <?php echo e($slot); ?>

            </div>
        </div>
    </body>
</html>
<?php /**PATH /home/sysocoqv/pqrs/resources/views/layouts/guest.blade.php ENDPATH**/ ?>