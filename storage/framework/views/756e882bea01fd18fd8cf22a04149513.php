<?php $__env->startSection('title', 'Create Post'); ?>

<?php $__env->startSection('content'); ?>

<div class="max-w-2xl mx-auto">

    <?php echo $__env->make('news._form', [
        'route' => route('posts.generate'),
        'categories' => $categories,
        'templates' => $templates
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/news/news.blade.php ENDPATH**/ ?>