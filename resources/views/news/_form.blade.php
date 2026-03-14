<div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8">

    <div id="aiRiskSection" class="hidden mb-6">
        <div id="aiRiskBox" class="border rounded-lg p-4 border-red-400 bg-red-50">
            <h3 class="text-sm font-semibold mb-2">AI Safety Warning</h3>
            <p id="aiRiskMessage" class="text-sm text-red-700"></p>
            <div class="mt-3">
                <button type="button" id="acknowledgeRiskBtn"
                    class="text-sm px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
                    I Understand
                </button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ $route }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Category</label>
                <select name="category_id" required
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select News Type</label>
                <select id="templateType" name="news_type" required
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
                    <option value="">-- Select Type --</option>
                    <option value="image" data-type="image">Template with image</option>
                    <option value="video" data-type="video">Template with video</option>
                    <option value="text" data-type="text">Template with text</option>
                </select>
                @error('news_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Template</label>
                <select name="template_type" required
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black">
                    <option value="">-- Select Template --</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
                @error('template_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="mt-4">
            <button type="button" id="aiAssistBtn"
                class="px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded hover:bg-blue-100 text-sm font-medium transition">
                ✨ AI Assist
            </button>
        </div>

        <div id="dynamicFields" class="space-y-5"></div>

        <div class="pt-4">
            <button type="submit"
                class="w-full bg-black text-white py-3 rounded-md hover:bg-gray-800 transition font-bold">
                Generate News
            </button>
        </div>
    </form>
    <div id="aiCaptionSection" class="hidden mt-8">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-gray-700">AI Generated Caption (for X)</h3>
                <div class="space-x-2">
                    <button type="button" id="copyCaptionBtn"
                        class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Copy</button>
                    <button type="button" id="clearCaptionBtn"
                        class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Clear</button>
                </div>
            </div>
            <textarea id="aiCaptionText" maxlength="280" class="w-full border border-gray-300 rounded-md px-4 py-2" rows="4"></textarea>
            <div class="flex justify-between text-xs text-gray-500 mt-2">
                <span>Maximum 280 characters.</span>
                <span id="captionCounter">0 / 280</span>
            </div>
        </div>
    </div>
</div>

<div id="aiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-3xl rounded-lg shadow-xl p-6 overflow-y-auto max-h-[90vh]">
        <h2 class="text-xl font-bold mb-4">AI News Assistant</h2>
        <textarea id="aiRawInput" class="w-full border border-gray-300 rounded-md px-4 py-2 mb-4" rows="6"
            placeholder="Paste raw news content or points here..."></textarea>

        <div class="flex justify-end space-x-3 mb-6">
            <button type="button" id="aiCloseBtn"
                class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">Cancel</button>
            <button type="button" id="aiAnalyzeBtn"
                class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">Analyze Content</button>
        </div>

        <div id="aiLoading" class="hidden text-center py-4">
            <div
                class="animate-spin inline-block w-6 h-6 border-4 border-current border-t-transparent text-black rounded-full mb-2">
            </div>
            <p class="text-sm text-gray-500">Processing News...</p>
        </div>

        <div id="aiPreviewSection" class="hidden border-t pt-4 space-y-4">
            <h3 class="font-bold text-gray-800">AI Suggestion Preview</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Headline</label>
                    <div id="aiHeadlinePreview" class="border p-3 rounded bg-gray-50 text-sm italic"></div>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Detected City</label>
                    <div id="aiCityPreview" class="border p-3 rounded bg-gray-50 text-sm font-semibold"></div>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Description</label>
                <div id="aiDescriptionPreview" class="border p-3 rounded bg-gray-50 text-sm"></div>
            </div>
            
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Caption</label>
                <div id="aiCaptionPreview" class="border p-3 rounded bg-gray-50 text-sm"></div>
            </div>

            <div id="aiRiskPreview" class="hidden p-3 border border-red-400 bg-red-50 rounded text-sm text-red-700">
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" id="aiApplyBtn"
                    class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold">Apply to Form</button>
                <button type="button" id="aiRegenerateBtn"
                    class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">Try Again</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById("templateType");
        const dynamicFields = document.getElementById("dynamicFields");

        // --- 1. Dynamic Template Logic ---
        templateSelect.addEventListener("change", function() {
            let type = this.options[this.selectedIndex].getAttribute('data-type');
            if (!type) {
                dynamicFields.innerHTML = "";
                return;
            }

            let descLimit = (type === 'text') ? 500 : 200;

            let fields = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Heading</label>
                <input type="text" name="heading" maxlength="200" class="w-full border border-gray-300 rounded-md px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="descriptionField" maxlength="${descLimit}" class="w-full border border-gray-300 rounded-md px-4 py-2" rows="4"></textarea>
                <p id="charCount" class="text-xs text-gray-400 mt-1">0 / ${descLimit}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" maxlength="15" class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hashtag</label>
                    <input type="text" name="hashtag" maxlength="30" class="w-full border border-gray-300 rounded-md px-4 py-2" placeholder="#News">
                </div>
            </div>
        `;

            if (type === 'image') {
                fields +=
                    `<div><label class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label><input type="file" name="image[]" multiple class="w-full border border-gray-300 rounded-md px-4 py-2"></div>`;
            } else if (type === 'video') {
                fields +=
                    `<div><label class="block text-sm font-medium text-gray-700 mb-2">Upload Video</label><input type="file" name="video" class="w-full border border-gray-300 rounded-md px-4 py-2"></div>`;
            }

            dynamicFields.innerHTML = fields;

            const textarea = document.getElementById("descriptionField");
            textarea.addEventListener("input", function() {
                document.getElementById("charCount").innerText = this.value.length + " / " +
                    descLimit;
            });
        });

        // --- 2. AI Logic ---
        const aiModal = document.getElementById('aiModal');
        let aiResponseData = null;

        document.getElementById('aiAssistBtn').addEventListener('click', () => aiModal.classList.remove('hidden'));
        document.getElementById('aiCloseBtn').addEventListener('click', () => aiModal.classList.add('hidden'));

        document.getElementById('aiAnalyzeBtn').addEventListener('click', function() {
            const rawContent = document.getElementById('aiRawInput').value;
            const type = templateSelect.value;

            if (!rawContent.trim()) return alert("Please paste content first.");

            document.getElementById('aiLoading').classList.remove('hidden');
            document.getElementById('aiPreviewSection').classList.add('hidden');

            fetch('/ai/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        raw_content: rawContent,
                        template_type: type || 'text'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('aiLoading').classList.add('hidden');
                    if (data.error) return alert("Error: " + data.error);

                    aiResponseData = data;
                    
                    // Update Preview Sections
                    document.getElementById('aiHeadlinePreview').innerText = data.headline || '';
                    document.getElementById('aiDescriptionPreview').innerText = data.description || '';
                    document.getElementById('aiCaptionPreview').innerText = data.caption || '';
                    document.getElementById('aiCityPreview').innerText = data.city || 'Not detected'; // City Preview

                    const riskBox = document.getElementById('aiRiskPreview');
                    if (data.risk_flag) {
                        riskBox.innerText = data.risk_reason;
                        riskBox.classList.remove('hidden');
                    } else {
                        riskBox.classList.add('hidden');
                    }
                    document.getElementById('aiPreviewSection').classList.remove('hidden');
                })
                .catch(() => {
                    document.getElementById('aiLoading').classList.add('hidden');
                    alert("AI Service unavailable.");
                });
        });

        // --- 3. Apply AI Data to Form ---
        document.getElementById('aiApplyBtn').addEventListener('click', function() {
            if (!aiResponseData) return;

            // Target form inputs
            const hInput = document.querySelector('[name="heading"]');
            const dInput = document.querySelector('[name="description"]');
            const hashInput = document.querySelector('[name="hashtag"]');
            const cityInput = document.querySelector('[name="city"]'); 

            if (hInput) hInput.value = aiResponseData.headline || '';
            if (cityInput) cityInput.value = aiResponseData.city || ''; 
            if (hashInput) hashInput.value = aiResponseData.hashtag || '';
            
            if (dInput) {
                dInput.value = aiResponseData.description || '';
                dInput.dispatchEvent(new Event('input'));
            }

            // Handle Risk Section
            if (aiResponseData.risk_flag) {
                document.getElementById('aiRiskMessage').innerText = aiResponseData.risk_reason;
                document.getElementById('aiRiskSection').classList.remove('hidden');
            }

            // Handle Caption Section
            document.getElementById('aiCaptionText').value = aiResponseData.caption || '';
            document.getElementById('aiCaptionSection').classList.remove('hidden');
            document.getElementById('captionCounter').innerText = (aiResponseData.caption || '').length + " / 280";

            aiModal.classList.add('hidden');
        });

        // --- 4. Utilities ---
        document.getElementById('aiRegenerateBtn').addEventListener('click', () => document.getElementById('aiAnalyzeBtn').click());
        document.getElementById('acknowledgeRiskBtn').addEventListener('click', () => document.getElementById('aiRiskSection').classList.add('hidden'));
        document.getElementById('copyCaptionBtn').addEventListener('click', () => {
            navigator.clipboard.writeText(document.getElementById('aiCaptionText').value);
            alert("Copied!");
        });
    });
</script>