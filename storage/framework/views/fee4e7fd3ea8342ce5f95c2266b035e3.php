<?php $__env->startSection('title', 'Pivot Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Revenue & Installment Pivot Report</h1>
    <p class="text-gray-600 mt-1">Monthly breakdown by client</p>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form action="<?php echo e(route('reports.pivot')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4">
        <div>
            <label class="label">Start Month</label>
            <input type="month" name="start" value="<?php echo e($startDate); ?>" class="input-field">
        </div>
        <div>
            <label class="label">End Month</label>
            <input type="month" name="end" value="<?php echo e($endDate); ?>" class="input-field">
        </div>
        <div>
            <label class="label">Client</label>
            <input type="text" name="client" value="<?php echo e($clientFilter); ?>"
                   placeholder="Client name" class="input-field">
        </div>
        <div>
            <label class="label">App Name</label>
            <select name="app_name" class="input-field">
                <option value="">All Apps</option>
                <?php $__currentLoopData = $appNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($appName); ?>" <?php echo e($appFilter == $appName ? 'selected' : ''); ?>>
                    <?php echo e($appName); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Currency</label>
            <select name="currency" class="input-field">
                <option value="">All</option>
                <option value="USD" <?php echo e($currency == 'USD' ? 'selected' : ''); ?>>USD</option>
                <option value="IQD" <?php echo e($currency == 'IQD' ? 'selected' : ''); ?>>IQD</option>
            </select>
        </div>
        <div>
            <label class="label">Data Type</label>
            <select name="data_type" class="input-field">
                <option value="both" <?php echo e($dataType == 'both' ? 'selected' : ''); ?>>Both</option>
                <option value="revenue" <?php echo e($dataType == 'revenue' ? 'selected' : ''); ?>>Revenue Only</option>
                <option value="installments" <?php echo e($dataType == 'installments' ? 'selected' : ''); ?>>Installments Only</option>
                <option value="discount" <?php echo e($dataType == 'discount' ? 'selected' : ''); ?>>Discount Only</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="btn-primary flex-1">Filter</button>
            <a href="<?php echo e(route('reports.pivot')); ?>" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<!-- Export Buttons -->
<div class="mb-4 flex space-x-4">
    <a href="<?php echo e(route('export')); ?>?start=<?php echo e($startDate); ?>&end=<?php echo e($endDate); ?>&currency=<?php echo e($currency); ?>&client=<?php echo e($clientFilter); ?>&app_name=<?php echo e($appFilter); ?>&data_type=<?php echo e($dataType); ?>"
       class="btn-primary">
        📥 Export to Excel
    </a>
    <button onclick="window.print()" class="btn-secondary">
        🖨️ Print Report
    </button>
</div>

<!-- Pivot Table -->
<div class="card overflow-x-auto">
    <?php if(count($pivotData['clients']) > 0): ?>
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 sticky top-0">
            <tr>
                <th class="border px-4 py-2 text-left font-semibold">Client Name</th>
                <th class="border px-4 py-2 text-left font-semibold">Invoice Numbers</th>
                <?php $__currentLoopData = $pivotData['months']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th class="border px-4 py-2 text-center font-semibold whitespace-nowrap">
                    <?php echo e(\Carbon\Carbon::parse($month)->format('M Y')); ?>

                </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
	        <tbody>
	            <?php $__currentLoopData = $pivotData['clients']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	            <tr class="hover:bg-gray-50">
	                <td class="border px-4 py-2 font-semibold"><?php echo e($client['client_name']); ?></td>
	                <td class="border px-4 py-2 text-sm">
	                    <?php echo e(implode(', ', $client['invoices'])); ?>

	                </td>
	                <?php $__currentLoopData = $pivotData['months']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	                <td class="border px-4 py-2 text-sm">
	                    <?php
	                        $monthData = $client['months'][$month] ?? ['revenue' => 0, 'installments' => 0, 'discount' => 0, 'currency' => ''];
	                        $showRevenue = in_array($dataType, ['both', 'revenue']);
	                        $showInstallments = in_array($dataType, ['both', 'installments']);
		                        $showDiscount = in_array($dataType, ['both', 'discount']);
		                        $hasData = ($showRevenue && $monthData['revenue'] > 0)
		                            || ($showInstallments && $monthData['installments'] > 0)
		                            || ($showDiscount && $monthData['discount'] > 0);
	                    ?>
	                    <?php if($hasData): ?>
	                        <div class="text-center">
	                            <?php if($showRevenue): ?>
	                            <div class="text-blue-600 font-semibold">
	                                Rev: <?php echo e(number_format($monthData['revenue'], 2)); ?>

	                            </div>
	                            <?php endif; ?>
	                            <?php if($showInstallments): ?>
	                            <div class="text-green-600">
	                                Inst: <?php echo e(number_format($monthData['installments'], 2)); ?>

	                            </div>
	                            <?php endif; ?>
		                            <?php if($showDiscount && $monthData['discount'] > 0): ?>
	                            <div class="text-red-600">
	                                Disc: <?php echo e(number_format($monthData['discount'], 2)); ?>

	                            </div>
	                            <?php endif; ?>
	                            <div class="text-xs text-gray-500"><?php echo e($monthData['currency']); ?></div>
	                        </div>
	                    <?php else: ?>
	                        <div class="text-center text-gray-400">-</div>
	                    <?php endif; ?>
	                </td>
	                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	            </tr>
	            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	            <!-- Total Row -->
	            <tr class="bg-indigo-50 font-bold">
	                <td class="border px-4 py-2 text-right">Total</td>
	                <td class="border px-4 py-2"></td>
	                <?php $__currentLoopData = $pivotData['months']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	                <td class="border px-4 py-2 text-sm bg-indigo-50">
	                    <?php
	                        $monthTotals = $pivotData['month_totals'][$month] ?? [
	                            'by_currency' => [],
	                            'all' => ['revenue' => 0, 'installments' => 0, 'discount' => 0],
	                        ];
	                        $showRevenueTotal      = in_array($dataType, ['both', 'revenue']);
	                        $showInstallmentsTotal = in_array($dataType, ['both', 'installments']);
	                        $showDiscountTotal     = in_array($dataType, ['both', 'discount']);
	                        $allTotals = $monthTotals['all'];
	                        $hasAnyTotal = ($showRevenueTotal && $allTotals['revenue'] > 0)
	                            || ($showInstallmentsTotal && $allTotals['installments'] > 0)
	                            || ($showDiscountTotal && $allTotals['discount'] > 0);
	                    ?>

	                    <?php if($hasAnyTotal): ?>
	                        <?php if($currency): ?>
	                            <?php
	                                $vals = $monthTotals['by_currency'][$currency] ?? ['revenue' => 0, 'installments' => 0, 'discount' => 0];
	                                $hasCurrencyTotal = ($showRevenueTotal && $vals['revenue'] > 0)
	                                    || ($showInstallmentsTotal && $vals['installments'] > 0)
	                                    || ($showDiscountTotal && $vals['discount'] > 0);
	                            ?>
	                            <?php if($hasCurrencyTotal): ?>
	                                <div class="text-center">
	                                    <?php if($showRevenueTotal && $vals['revenue'] > 0): ?>
	                                        <div class="text-blue-700">
	                                            Rev: <?php echo e(number_format($vals['revenue'], 2)); ?>

	                                        </div>
	                                    <?php endif; ?>
	                                    <?php if($showInstallmentsTotal && $vals['installments'] > 0): ?>
	                                        <div class="text-green-700">
	                                            Inst: <?php echo e(number_format($vals['installments'], 2)); ?>

	                                        </div>
	                                    <?php endif; ?>
	                                    <?php if($showDiscountTotal && $vals['discount'] > 0): ?>
	                                        <div class="text-red-700">
	                                            Disc: <?php echo e(number_format($vals['discount'], 2)); ?>

	                                        </div>
	                                    <?php endif; ?>
	                                    <div class="text-xs text-gray-500"><?php echo e($currency); ?></div>
	                                </div>
	                            <?php else: ?>
	                                <div class="text-center text-gray-400">-</div>
	                            <?php endif; ?>
	                        <?php else: ?>
	                            <div class="text-center text-xs space-y-1">
	                                <?php $__currentLoopData = $monthTotals['by_currency']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur => $vals): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	                                    <?php
	                                        $lineHasData = ($showRevenueTotal && $vals['revenue'] > 0)
	                                            || ($showInstallmentsTotal && $vals['installments'] > 0)
	                                            || ($showDiscountTotal && $vals['discount'] > 0);
	                                    ?>
	                                    <?php if($lineHasData): ?>
	                                        <div class="mb-1">
	                                            <div class="font-semibold text-gray-700"><?php echo e($cur); ?></div>
	                                            <?php if($showRevenueTotal && $vals['revenue'] > 0): ?>
	                                                <div class="text-blue-700">
	                                                    Rev: <?php echo e(number_format($vals['revenue'], 2)); ?>

	                                                </div>
	                                            <?php endif; ?>
	                                            <?php if($showInstallmentsTotal && $vals['installments'] > 0): ?>
	                                                <div class="text-green-700">
	                                                    Inst: <?php echo e(number_format($vals['installments'], 2)); ?>

	                                                </div>
	                                            <?php endif; ?>
	                                            <?php if($showDiscountTotal && $vals['discount'] > 0): ?>
	                                                <div class="text-red-700">
	                                                    Disc: <?php echo e(number_format($vals['discount'], 2)); ?>

	                                                </div>
	                                            <?php endif; ?>
	                                        </div>
	                                    <?php endif; ?>
	                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	                            </div>
	                        <?php endif; ?>
	                    <?php else: ?>
	                        <div class="text-center text-gray-400">-</div>
	                    <?php endif; ?>
	                </td>
	                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	            </tr>
	        </tbody>
    </table>
    <?php else: ?>
    <div class="text-center py-12 text-gray-500">
        <p class="text-lg">No data found for the selected filters</p>
        <p class="text-sm mt-2">Try adjusting your filter criteria</p>
    </div>
    <?php endif; ?>
</div>

<!-- Legend -->
<div class="card mt-6">
    <h3 class="font-bold text-gray-800 mb-2">Legend:</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-blue-600 font-semibold">Rev:</span> 
            <span class="text-gray-700">Monthly revenue allocation for that month</span>
        </div>
        <div>
            <span class="text-green-600 font-semibold">Inst:</span> 
            <span class="text-gray-700">Installment amount due in that month</span>
        </div>
	        <div>
	            <span class="text-red-600 font-semibold">Disc:</span> 
	            <span class="text-gray-700">Discount amount allocated to that month</span>
	        </div>
    </div>
</div>

<?php if(isset($pivotData['total_discount'])): ?>
	<div class="card mt-4">
	    <h3 class="font-bold text-gray-800 mb-2">Total Discounts for Selected Period:</h3>
	    <p class="text-lg font-semibold">
	        <?php echo e(number_format($pivotData['total_discount'], 2)); ?>

	        <?php if($currency): ?>
	            <?php echo e($currency); ?>

	        <?php endif; ?>
	    </p>
	    <?php if(!$currency): ?>
	        <p class="text-xs text-gray-500 mt-1">Note: Total may include multiple currencies. Use the Currency filter for a single-currency total.</p>
	    <?php endif; ?>
	</div>
<?php endif; ?>

<style>
    @media print {
        nav, footer, .no-print, button, a.btn-primary, a.btn-secondary {
            display: none !important;
        }
        .card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/mustafaaljaf/Revenue 2/resources/views/reports/pivot.blade.php ENDPATH**/ ?>