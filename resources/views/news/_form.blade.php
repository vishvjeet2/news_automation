<form method="POST"
      action="{{ $route }}"
      enctype="multipart/form-data"
      class="space-y-6">

    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Select Category
        </label>

        <select name="category_id"
                required
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            <option value="">-- Select Category --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Select Template
        </label>

        <select id="templateType"
                name="template_type"
                required
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">

            <option value="">-- Select Template --</option>

            @foreach ($templates as $template)
                <option value="{{ $template->id }}"
                        data-type="{{ $template->type }}">
                    {{ $template->name }}
                </option>
            @endforeach

        </select>
    </div>

    <div id="dynamicFields" class="space-y-5"></div>

    <button type="submit"
            class="w-full bg-black text-white py-3 rounded-md hover:bg-gray-800 transition">
        Generate
    </button>
</form>
<script>
const templateSelect = document.getElementById("templateType");

if (templateSelect) {

    templateSelect.addEventListener("change", function(){

        let selected = this.options[this.selectedIndex];
        let type = selected.getAttribute('data-type');

        let fields = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Heading</label>
                <input type="text" name="heading"
                       maxlength="70"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            </div>
        `;

        let descriptionLimit = 300;

        if (type === 'text') {
            descriptionLimit = 400;
        }

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

        fields += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <input type="text" name="city"
                       maxlength="20"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
            </div>
        `;

        if (type === 'image') {
            fields += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                    <input type="file" name="image"
                           class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
            `;
        }

        if (type === 'video') {
            fields += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Video</label>
                    <input type="file" name="video"
                           class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
            `;
        }

        document.getElementById("dynamicFields").innerHTML = fields;

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