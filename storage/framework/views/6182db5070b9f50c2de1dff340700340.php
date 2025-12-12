<?php $__env->startSection('title', 'Create Contract'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Create New Contract</h1>
    <p class="text-gray-600 mt-1">Add a new contract to the system</p>
</div>

<div class="card max-w-3xl">
    <form action="<?php echo e(route('contracts.store')); ?>" method="POST" id="contractForm">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- App Name -->
            <div>
                <label for="app_name" class="label">Application Name <span class="text-red-500">*</span></label>
                <select id="app_name" name="app_name" class="input-field" required>
                    <option value="">Select Application</option>
                    <option value="Teami Pro" <?php echo e(old('app_name') == 'Teami Pro' ? 'selected' : ''); ?>>Teami Pro</option>
                    <option value="MaxCon" <?php echo e(old('app_name') == 'MaxCon' ? 'selected' : ''); ?>>MaxCon</option>
                    <option value="ConCure" <?php echo e(old('app_name') == 'ConCure' ? 'selected' : ''); ?>>ConCure</option>
                    <option value="MediCon" <?php echo e(old('app_name') == 'MediCon' ? 'selected' : ''); ?>>MediCon</option>
                    <option value="Connect Job" <?php echo e(old('app_name') == 'Connect Job' ? 'selected' : ''); ?>>Connect Job</option>
                </select>
                <?php $__errorArgs = ['app_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Client Name -->
            <div>
                <label for="client_name" class="label">Client Name <span class="text-red-500">*</span></label>
                <input type="text" id="client_name" name="client_name" value="<?php echo e(old('client_name')); ?>" 
                       class="input-field" required>
                <?php $__errorArgs = ['client_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Invoice Number -->
            <div>
                <label for="invoice_number" class="label">Invoice Number <span class="text-red-500">*</span></label>
                <input type="text" id="invoice_number" name="invoice_number" value="<?php echo e(old('invoice_number')); ?>" 
                       class="input-field" required>
                <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Invoice Date -->
            <div>
                <label for="invoice_date" class="label">Invoice Date <span class="text-red-500">*</span></label>
                <input type="date" id="invoice_date" name="invoice_date" value="<?php echo e(old('invoice_date')); ?>" 
                       class="input-field" required>
                <?php $__errorArgs = ['invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_months" class="label">Duration (Months) <span class="text-red-500">*</span></label>
                <input type="number" id="duration_months" name="duration_months" value="<?php echo e(old('duration_months')); ?>" 
                       min="1" max="120" class="input-field" required>
                <?php $__errorArgs = ['duration_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="label">Total Amount <span class="text-red-500">*</span></label>
                <input type="number" id="amount" name="amount" value="<?php echo e(old('amount')); ?>" 
                       step="0.01" min="0" class="input-field" required>
                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Currency -->
            <div>
                <label for="currency" class="label">Currency <span class="text-red-500">*</span></label>
                <select id="currency" name="currency" class="input-field" required>
                    <option value="">Select Currency</option>
                    <option value="USD" <?php echo e(old('currency') == 'USD' ? 'selected' : ''); ?>>USD</option>
                    <option value="IQD" <?php echo e(old('currency') == 'IQD' ? 'selected' : ''); ?>>IQD</option>
                </select>
                <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Installment Frequency -->
            <div>
                <label for="installment_frequency" class="label">Installment Frequency <span class="text-red-500">*</span></label>
                <select id="installment_frequency" name="installment_frequency" class="input-field" required>
                    <option value="">Select Frequency</option>
                    <option value="monthly" <?php echo e(old('installment_frequency') == 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                    <option value="quarterly" <?php echo e(old('installment_frequency') == 'quarterly' ? 'selected' : ''); ?>>Quarterly</option>
                    <option value="yearly" <?php echo e(old('installment_frequency') == 'yearly' ? 'selected' : ''); ?>>Yearly</option>
                </select>
                <?php $__errorArgs = ['installment_frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex space-x-4">
            <button type="submit" class="btn-primary">Create Contract</button>
            <a href="<?php echo e(route('contracts.index')); ?>" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/mustafaaljaf/Revenue 2/resources/views/contracts/create.blade.php ENDPATH**/ ?>