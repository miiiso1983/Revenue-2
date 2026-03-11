<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Revenue Management System'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-100 font-sans">
    <?php if(auth()->guard()->check()): ?>
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-xl font-bold">Revenue Management</a>
                    <div class="ml-10 flex space-x-4">
                        <a href="<?php echo e(route('dashboard')); ?>" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Dashboard</a>
                        <a href="<?php echo e(route('contracts.index')); ?>" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Contracts</a>
                        <a href="<?php echo e(route('reports.pivot')); ?>" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Reports</a>
                        <?php if(auth()->user()->isAdmin()): ?>
                        <a href="<?php echo e(route('bulk-upload.index')); ?>" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Bulk Upload</a>
                        <a href="<?php echo e(route('audit-logs.index')); ?>" class="px-3 py-2 rounded-md hover:bg-white/10 transition">Audit Logs</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm"><?php echo e(auth()->user()->username); ?> (<?php echo e(ucfirst(auth()->user()->role)); ?>)</span>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 bg-white/20 rounded-md hover:bg-white/30 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if(session('success')): ?>
    <div class="flash-message max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="flash-message max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="flash-message max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-12 py-6 border-t">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600">
            <p>&copy; <?php echo e(date('Y')); ?> Revenue Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

<?php /**PATH /Users/mustafaaljaf/Revenue 2/resources/views/layouts/app.blade.php ENDPATH**/ ?>