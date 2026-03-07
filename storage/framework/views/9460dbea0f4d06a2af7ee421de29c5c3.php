<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Total Posts</p>
        <p id="stat-total" class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['total']); ?></p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Published</p>
        <p id="stat-published" class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['published']); ?></p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Drafts</p>
        <p id="stat-drafts" class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['drafts']); ?></p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6">
        <p class="text-sm text-gray-500 font-medium">Users</p>
        <p id="stat-users" class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['users']); ?></p>
    </div>

</div>


<!-- Action Bar -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

    <div class="hidden md:block"></div>

    <a href="<?php echo e(route('admin.posts.create')); ?>"
       class="block w-full md:w-auto text-center bg-black text-white px-6 py-3 rounded-lg shadow hover:bg-gray-800 transition font-medium text-sm md:text-base">
        + Create New Post
    </a>

</div>


<!-- Responsive Table -->
<div class="w-full md:bg-white md:border md:border-gray-200 md:shadow-sm md:rounded-lg md:overflow-hidden">

<table class="w-full text-left text-sm md:text-base border-separate md:border-collapse space-y-4 md:space-y-0">

    <!-- Desktop Header -->
    <thead class="hidden md:table-header-group bg-gray-50 border-b border-gray-200">
        <tr>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Heading</th>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Download</th>
        </tr>
    </thead>

<tbody class="block md:table-row-group">

<?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

<tr class="flex flex-col md:table-row bg-white rounded-xl shadow-sm border border-gray-200 mb-4 md:mb-0 md:border-b md:border-gray-100 md:shadow-none hover:bg-gray-50 transition relative overflow-hidden">

    <!-- Heading -->
    <td class="order-1 block md:table-cell p-5 pb-2 md:p-4 text-gray-900">
        <div class="text-lg font-bold leading-tight md:text-sm md:font-normal">
            <?php echo e($post->heading); ?>

        </div>
    </td>

    <!-- Type -->
    <td class="order-2 block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase md:hidden">Type</span>
            <span class="capitalize font-medium text-gray-700">
                <?php echo e($post->latestOutput->output_type ?? 'N/A'); ?>

            </span>
        </div>
    </td>

    <!-- Category -->
    <td class="order-3 block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase md:hidden">Category</span>
            <span class="font-medium text-gray-700">
                <?php echo e($post->category->name ?? '-'); ?>

            </span>
        </div>
    </td>

    <!-- Status -->
    <td class="order-4 block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">

            <span class="text-xs font-semibold text-gray-400 uppercase md:hidden">Status</span>

            <button type="button"
                onclick="toggleStatus(<?php echo e($post->id); ?>)"
                id="status-btn-<?php echo e($post->id); ?>"
                class="px-2.5 py-0.5 rounded-full text-xs font-medium border inline-block
                <?php echo e(($post->status ?? 'draft') === 'processed'
                    ? 'bg-green-50 text-green-700 border-green-200'
                    : 'bg-yellow-50 text-yellow-700 border-yellow-200'); ?>">
                <?php echo e(ucfirst($post->status ?? 'draft')); ?>

            </button>

        </div>
    </td>

    <!-- Created By -->
    <td class="order-5 block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase md:hidden">Author</span>

            <span class="text-gray-600 font-medium">

                <?php if($post->admin): ?>
                    <?php echo e($post->admin->name); ?>

                <?php elseif($post->user): ?>
                    <?php echo e($post->user->name); ?>

                <?php else: ?>
                    Unknown
                <?php endif; ?>

            </span>
        </div>
    </td>

    <!-- Created -->
    <td class="order-6 block md:table-cell px-5 py-1 md:p-4">
        <div class="flex justify-between md:block items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase md:hidden">Created</span>
            <span class="text-gray-500 font-medium">
                <?php echo e($post->created_at->format('d M Y')); ?>

            </span>
        </div>
    </td>

    <!-- Download -->
    <td class="order-last block md:table-cell border-t border-gray-100 md:border-none mt-3 md:mt-0 p-0 md:p-4 bg-gray-50 md:bg-transparent">
        <a href="<?php echo e(route('admin.post.download', $post->id)); ?>"
           class="block w-full py-3 md:py-0 text-center md:text-left text-sm font-semibold text-blue-600 hover:text-blue-800 md:text-black md:underline md:bg-transparent">
            Preview
        </a>
    </td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

<tr class="flex md:table-row bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-0">
    <td colspan="7" class="w-full text-center p-8 text-gray-500 block md:table-cell">
        No posts available
    </td>
</tr>

<?php endif; ?>

</tbody>
</table>

<div class="hidden md:block px-4 py-3 border-t border-gray-200 bg-gray-50">
    <?php echo e($posts->links()); ?>

</div>

</div>

<div class="md:hidden mt-4 pb-10">
    <?php echo e($posts->links()); ?>

</div>


<script>

function toggleStatus(id) {

fetch('/admin/posts/' + id + '/toggle-status', {
method: "POST",
headers: {
"X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
"Accept": "application/json"
}
})
.then(res => res.json())
.then(data => {

const btn = document.getElementById(`status-btn-${id}`);

btn.innerText = data.label;

if (data.status === 'processed') {

btn.className =
"px-2.5 py-0.5 rounded-full text-xs font-medium border inline-block bg-green-50 text-green-700 border-green-200";

let drafts = document.getElementById('stat-drafts');
let published = document.getElementById('stat-published');

drafts.innerText = parseInt(drafts.innerText) - 1;
published.innerText = parseInt(published.innerText) + 1;

} else {

btn.className =
"px-2.5 py-0.5 rounded-full text-xs font-medium border inline-block bg-yellow-50 text-yellow-700 border-yellow-200";

let drafts = document.getElementById('stat-drafts');
let published = document.getElementById('stat-published');

drafts.innerText = parseInt(drafts.innerText) + 1;
published.innerText = parseInt(published.innerText) - 1;

}

});

}

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('Admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>