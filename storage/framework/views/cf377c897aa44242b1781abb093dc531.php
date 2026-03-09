<?php $__env->startSection('title', 'Manage Categories'); ?>

<?php $__env->startSection('content'); ?>

    <div class="max-w-3xl mx-auto">

        <?php if(session('success')): ?>
            <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8 mb-8">
            <h2 class="text-lg font-semibold text-black mb-6">Add Category</h2>

            <form method="POST" action="<?php echo e(route('admin.categories.store')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="categoryName"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">

                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug" id="categorySlug"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">

                    <p class="text-xs text-gray-500 mt-1">
                        Note: Slug must be unique. It will be used in URLs.
                    </p>

                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition">
                    Save Category
                </button>
            </form>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-medium text-gray-600">Name</th>
                        <th class="p-4 text-sm font-medium text-gray-600">Slug</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-gray-100">
                            <td class="p-4"><?php echo e($category->name); ?></td>
                            <td class="p-4"><?php echo e($category->slug); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="2" class="p-6 text-center text-gray-400">
                                No categories found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <?php echo e($categories->links()); ?>

        </div>

    </div>

<?php $__env->stopSection(); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');

    if (!nameInput || !slugInput) return;

    nameInput.addEventListener('input', function () {

        let slug = nameInput.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        slugInput.value = slug;
    });

});
</script>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>