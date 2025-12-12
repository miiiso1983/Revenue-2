<?php $__env->startSection('title', 'Contracts'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Contracts</h1>
        <p class="text-gray-600 mt-1">Manage all contracts and invoices</p>
    </div>
    <?php if(auth()->user()->isAdmin()): ?>
    <a href="<?php echo e(route('contracts.create')); ?>" class="btn-primary">
        + Add New Contract
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form action="<?php echo e(route('contracts.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="label">Search</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                   placeholder="Invoice #, Client, App..." class="input-field">
        </div>
        <div>
            <label class="label">Client</label>
            <input type="text" name="client" value="<?php echo e(request('client')); ?>" 
                   placeholder="Client name" class="input-field">
        </div>
        <div>
            <label class="label">Currency</label>
            <select name="currency" class="input-field">
                <option value="">All</option>
                <option value="USD" <?php echo e(request('currency') == 'USD' ? 'selected' : ''); ?>>USD</option>
                <option value="IQD" <?php echo e(request('currency') == 'IQD' ? 'selected' : ''); ?>>IQD</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="btn-primary flex-1">Filter</button>
            <a href="<?php echo e(route('contracts.index')); ?>" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<!-- Contracts Table -->
<div class="card">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client Name</th>
                    <th>App Name</th>
                    <th>Amount</th>
                    <th>Duration</th>
                    <th>Frequency</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-semibold"><?php echo e($contract->invoice_number); ?></td>
                    <td><?php echo e($contract->client_name); ?></td>
                    <td><?php echo e($contract->app_name); ?></td>
                    <td><?php echo e(number_format($contract->amount, 2)); ?> <?php echo e($contract->currency); ?></td>
                    <td><?php echo e($contract->duration_months); ?> months</td>
                    <td><?php echo e(ucfirst($contract->installment_frequency)); ?></td>
                    <td><?php echo e($contract->invoice_date->format('M d, Y')); ?></td>
                    <td>
                        <div class="flex space-x-2">
                            <a href="<?php echo e(route('contracts.show', $contract)); ?>" 
                               class="text-blue-600 hover:text-blue-800">View</a>
                            <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('contracts.edit', $contract)); ?>" 
                               class="text-green-600 hover:text-green-800">Edit</a>
                            <form action="<?php echo e(route('contracts.destroy', $contract)); ?>" 
                                  method="POST" class="inline"
                                  onsubmit="return confirmDelete('Are you sure you want to delete this contract?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center text-gray-500 py-8">No contracts found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <?php echo e($contracts->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/mustafaaljaf/Revenue 2/resources/views/contracts/index.blade.php ENDPATH**/ ?>