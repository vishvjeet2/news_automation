<?php $__env->startSection('title', 'File Preview'); ?>

<?php $__env->startSection('content'); ?>

<div class="max-w-xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">

        <h2 class="text-xl font-semibold mb-6">
            Your File is Ready ✅
        </h2>

        <img src="<?php echo e(asset($image)); ?>" class="mx-auto rounded-lg shadow-sm max-w-sm">

        <a href="<?php echo e(asset($image)); ?>" download
        class="inline-block mt-6 px-6 py-3 bg-black text-white rounded-lg">
        ⬇ Download
        </a>

        <div class="mt-6">
            <a href="<?php echo e(route('admin.dashboard')); ?>"
               class="text-gray-600 hover:underline">
                ← Back to Dashboard
            </a>
        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('Admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/news/adminpreview.blade.php ENDPATH**/ ?>