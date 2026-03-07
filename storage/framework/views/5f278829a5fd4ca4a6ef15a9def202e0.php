<?php $__env->startSection('title', 'Create Post'); ?>

<?php $__env->startSection('content'); ?>

<div class="max-w-2xl mx-auto">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="<?php echo e(route('admin.dashboard')); ?>"
           class="inline-flex items-center text-sm text-gray-600 hover:text-black transition">
            ← Back to Dashboard
        </a>
    </div>


        <?php echo $__env->make('news._form', [
            'route' => route('admin.post.store'),
            'categories' => $categories,
            'templates' => $templates
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/admin/posts/create.blade.php ENDPATH**/ ?>