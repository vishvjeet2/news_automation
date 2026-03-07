<?php if($errors->any()): ?>
    <div class="bg-red-100 text-red-700 p-3 mb-4">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


<!--
|----------------------------------------------------------------------------
| News Generation Form
|----------------------------------------------------------------------------
| This form is used to create/generate news.
| $route decides where form data will go.
| - In User panel → goes to User PostController
| - In Admin panel → goes to Admin PostController
| When Generate button is clicked, form submits to that route.
-->
<div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8">
<form method="POST" action="<?php echo e($route); ?>" enctype="multipart/form-data" class="space-y-6">

    <!--
    |----------------------------------------------------------------------------
    | CSRF Protection
    |----------------------------------------------------------------------------
    | Required for Laravel form security.
    -->
    <?php echo csrf_field(); ?>

    <!--
    |----------------------------------------------------------------------------
    | Category Selection
    |----------------------------------------------------------------------------
    | User selects a category.
    | category_id will be sent to controller on submit.
    -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Select Category
        </label>

        <select name="category_id" required
            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            <option value="">-- Select Category --</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>">
                    <?php echo e($category->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <!--
    |----------------------------------------------------------------------------
    | Template Selection
    |----------------------------------------------------------------------------
    | When template changes, dynamic fields will be generated using JS.
    | data-type is used to detect text/image/video template.
    -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Select Template
        </label>

        <select id="templateType" name="template_type" required
            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">

            <option value="">-- Select Template --</option>

            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($template->id); ?>" data-type="<?php echo e($template->type); ?>">
                    <?php echo e($template->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </select>
    </div>

    <!--
    |----------------------------------------------------------------------------
    | Dynamic Fields Container
    |----------------------------------------------------------------------------
    | JS will inject Heading, Description, City,
    | and Image/Video upload fields here.
    -->
    <div id="dynamicFields" class="space-y-5"></div>

    <!--
    |----------------------------------------------------------------------------
    | Generate Button
    |----------------------------------------------------------------------------
    | On click:
    | 1. Form submits to $route
    | 2. Controller stores news
    | 3. NewsGeneratorService may process it
    -->
    <button type="submit" class="w-full bg-black text-white py-3 rounded-md hover:bg-gray-800 transition">
        Generate
    </button>
</form>
</div>

<script>
    /*
|----------------------------------------------------------------------------
| Dynamic Template Field Script
|----------------------------------------------------------------------------
| When user selects a template:
| - Script reads template type (text/image/video)
| - Generates required input fields
| - Adds character counter for description
*/
    const templateSelect = document.getElementById("templateType");

    if (templateSelect) {

        templateSelect.addEventListener("change", function() {

            // Get selected template
            let selected = this.options[this.selectedIndex];

            // Detect template type
            let type = selected.getAttribute('data-type');

            /*
            |----------------------------------------------------------------------------
            | Heading Field (Always Visible)
            |----------------------------------------------------------------------------
            */
            let fields = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Heading</label>
                <input type="text" name="heading"
                       maxlength="70"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            </div>
        `;

            // Default description limit
            let descriptionLimit = 300;

            /*
            |----------------------------------------------------------------------------
            | Text Template Rule
            |----------------------------------------------------------------------------
            | If template type is text, increase description limit.
            */
            if (type === 'text') {
                descriptionLimit = 400;
            }

            /*
            |----------------------------------------------------------------------------
            | Description Field + Character Counter
            |----------------------------------------------------------------------------
            */
            fields += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>

                <textarea name="description"
                          maxlength="${descriptionLimit}"
                          id="descriptionField"
                          class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black"></textarea>

                <p class="text-xs text-gray-500 mt-1">
                    Maximum ${descriptionLimit} characters allowed.
                </p>

                <p id="charCount" class="text-xs text-gray-400 mt-1">
                    0 / ${descriptionLimit}
                </p>
            </div>
        `;

            /*
            |----------------------------------------------------------------------------
            | City Field (Always Visible)
            |----------------------------------------------------------------------------
            */
            fields += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <input type="text" name="city"
                       maxlength="20"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            </div>
        `;

            /*
            |----------------------------------------------------------------------------
            | Image Template Rule
            |----------------------------------------------------------------------------
            | If template type is image, show image upload input.
            */
            if (type === 'image') {
                fields += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                    <input type="file" name="image"
                           class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
            `;
            }

            /*
            |----------------------------------------------------------------------------
            | Video Template Rule
            |----------------------------------------------------------------------------
            | If template type is video, show video upload input.
            */
            if (type === 'video') {
                fields += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Video</label>
                    <input type="file" name="video"
                        class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
            `;
            }

            // Inject generated fields
            document.getElementById("dynamicFields").innerHTML = fields;

            /*
            |----------------------------------------------------------------------------
            | Character Counter Logic
            |----------------------------------------------------------------------------
            | Updates live character count while typing description.
            */
            const textarea = document.getElementById("descriptionField");
            const counter = document.getElementById("charCount");

            if (textarea && counter) {
                textarea.addEventListener("input", function() {
                    counter.innerText = textarea.value.length + " / " + descriptionLimit;
                });
            }
        });

    }
</script>
<?php /**PATH C:\Users\Gumnaami BaBa\Desktop\news_automation\resources\views/news/_form.blade.php ENDPATH**/ ?>