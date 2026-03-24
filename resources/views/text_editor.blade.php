@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@600;700;800&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body { background: #0f0f0f; }

    .editor-wrap {
        min-height: 100vh;
        background: #0f0f0f;
        padding: 28px 32px;
        font-family: 'DM Mono', monospace;
    }

    .editor-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .editor-title {
        font-family: 'Syne', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #f5f5f5;
        letter-spacing: -0.5px;
    }

    .editor-badge {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        background: #2aff8f22;
        color: #2aff8f;
        border: 1px solid #2aff8f44;
        padding: 3px 8px;
        border-radius: 4px;
        letter-spacing: 1px;
    }

    .toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 10px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .toolbar-sep {
        width: 1px;
        height: 28px;
        background: #2f2f2f;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.15s ease;
        letter-spacing: 0.3px;
    }

    .btn-primary {
        background: #2aff8f;
        color: #0f0f0f;
    }

    .btn-primary:hover { background: #22e07a; transform: translateY(-1px); }

    .btn-secondary {
        background: #252525;
        color: #ccc;
        border: 1px solid #333;
    }

    .btn-secondary:hover { background: #2f2f2f; color: #fff; }

    .btn-danger {
        background: #ff4a4a22;
        color: #ff6b6b;
        border: 1px solid #ff4a4a44;
    }

    .btn-danger:hover { background: #ff4a4a33; }

    .btn-save {
        background: #3b82f6;
        color: #fff;
    }

    .btn-save:hover { background: #2563eb; transform: translateY(-1px); }

    .tool-label {
        font-size: 10px;
        color: #555;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .color-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .color-input-wrapper {
        position: relative;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid #333;
        cursor: pointer;
        transition: border-color 0.15s;
    }

    .color-input-wrapper:hover { border-color: #555; }

    .color-input-wrapper input[type="color"] {
        position: absolute;
        inset: -4px;
        width: calc(100% + 8px);
        height: calc(100% + 8px);
        border: none;
        padding: 0;
        cursor: pointer;
        opacity: 1;
    }

    .font-size-input {
        background: #252525;
        border: 1px solid #333;
        border-radius: 6px;
        color: #eee;
        font-family: 'DM Mono', monospace;
        font-size: 13px;
        padding: 6px 10px;
        width: 70px;
        text-align: center;
        transition: border-color 0.15s;
    }

    .font-size-input:focus {
        outline: none;
        border-color: #2aff8f66;
    }

    /* Bold/Italic toggles */
    .style-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #333;
        background: #252525;
        color: #888;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.15s;
    }

    .style-toggle:hover { color: #fff; border-color: #555; }
    .style-toggle.active { background: #2aff8f22; border-color: #2aff8f66; color: #2aff8f; }

    /* Status bar */
    .status-bar {
        display: flex;
        gap: 16px;
        padding: 6px 14px;
        background: #141414;
        border: 1px solid #222;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-size: 10px;
        color: #444;
        letter-spacing: 0.5px;
    }

    .status-bar span { color: #666; }
    .status-bar .highlight { color: #2aff8f; }

    /* Canvas container */
    #container {
        border: 1px solid #222;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
        position: relative; /* CRITICAL for Konva */
        background: #ffffff;
        cursor: default;
        box-shadow: 0 20px 60px #00000088;
    }

    /* Selection info pill */
    #sel-info {
        display: none;
        position: absolute;
        bottom: 14px;
        right: 14px;
        background: #1a1a1acc;
        backdrop-filter: blur(8px);
        border: 1px solid #333;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 11px;
        color: #888;
        pointer-events: none;
        z-index: 10;
    }

    .canvas-outer { position: relative; }

    /* Toast */
    #toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        background: #1a1a1a;
        border: 1px solid #2aff8f44;
        color: #2aff8f;
        padding: 10px 18px;
        border-radius: 8px;
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.25s ease;
        pointer-events: none;
        z-index: 9999;
    }

    #toast.show { opacity: 1; transform: translateY(0); }
</style>

<div class="editor-wrap">

    <div class="editor-header">
        <span class="editor-title">News Dhani</span>
        <span class="editor-badge">2D</span>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">

        <button onclick="addText()" class="btn btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Add Text
        </button>

        <div class="toolbar-sep"></div>

        <div class="color-wrap">
            <span class="tool-label">Fill</span>
            <div class="color-input-wrapper" title="Text color">
                <input type="color" id="colorPicker" value="#000000">
            </div>
        </div>

        <div class="color-wrap">
            <span class="tool-label">Size</span>
            <input type="number" id="fontSize" value="30" min="6" max="200" class="font-size-input" title="Font size">
        </div>

        <button id="boldBtn" class="style-toggle" onclick="toggleBold()" title="Bold"><b>B</b></button>
        <button id="italicBtn" class="style-toggle" onclick="toggleItalic()" title="Italic"><i>I</i></button>

        <button onclick="updateTextStyle()" class="btn btn-secondary">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Apply
        </button>

        <div class="toolbar-sep"></div>

        <button onclick="deleteSelected()" class="btn btn-danger">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
            Delete
        </button>

        <button onclick="clearAll()" class="btn btn-secondary">Clear</button>

        <div class="toolbar-sep"></div>

        <button onclick="saveTemplate()" class="btn btn-save">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save
        </button>

    </div>

    <!-- Canvas -->
    <div class="canvas-outer">
        <div class="status-bar">
            <span>W: 800 &nbsp;H: 500</span>
            <span id="status-selected">No selection</span>
            <span id="status-count">0 objects</span>
        </div>
        <div id="container" style="width:800px; height:500px;"></div>
        <div id="sel-info">Drag to move · Handles to resize</div>
    </div>

</div>

<!-- Toast -->
<div id="toast"></div>

<!-- Konva CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/konva/8.4.2/konva.min.js"></script>

<script>
    let selectedNode = null;
    let isBold = false;
    let isItalic = false;
    let textCount = 0;

    // ─── Stage ───────────────────────────────────────────────────────
    const stage = new Konva.Stage({
        container: 'container',
        width: 800,
        height: 500,
    });

    const layer = new Konva.Layer();
    stage.add(layer);

    // ─── Transformer ─────────────────────────────────────────────────
    const transformer = new Konva.Transformer({

        enabledAnchors: [
            'top-left', 'top-right',
            'bottom-left', 'bottom-right',
            'middle-left', 'middle-right'
        ],
        rotateEnabled: true,
        keepRatio: false,

        boundBoxFunc(oldBox, newBox) {
            // Prevent collapsing too small in either direction
            if (newBox.width < 20 || newBox.height < 20) return oldBox;
            return newBox;
        },
    });
    layer.add(transformer);

    // Make text scale properly from all anchors including corners
    transformer.on('transformend', function () {
        const node = transformer.nodes()[0];
        if (!node || node.className !== 'Text') return;

        const newFontSize = Math.max(8, node.fontSize() * node.scaleY());
        node.fontSize(newFontSize);
        node.scaleY(1);

        node.width(Math.max(20, node.width() * node.scaleX()));
        node.scaleX(1);

        layer.batchDraw();
    });

    // ─── Helpers ─────────────────────────────────────────────────────
    function toast(msg) {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.classList.add('show');
        setTimeout(() => el.classList.remove('show'), 2200);
    }

    function updateStatusBar() {
        const objects = layer.children.filter(n => n.className === 'Text');
        document.getElementById('status-count').textContent = objects.length + ' object' + (objects.length !== 1 ? 's' : '');

        if (selectedNode) {
            document.getElementById('status-selected').innerHTML =
                '<span class="highlight">✦</span> "' + selectedNode.text().substring(0, 18) + (selectedNode.text().length > 18 ? '…' : '') + '"';
            document.getElementById('sel-info').style.display = 'block';
        } else {
            document.getElementById('status-selected').textContent = 'No selection';
            document.getElementById('sel-info').style.display = 'none';
        }
    }

    function selectNode(node) {
        selectedNode = node;
        transformer.nodes([node]);

        // Sync toolbar to selected node's properties
        document.getElementById('colorPicker').value = node.fill() || '#000000';
        document.getElementById('fontSize').value = node.fontSize() || 30;

        isBold = node.fontStyle().includes('bold');
        isItalic = node.fontStyle().includes('italic');
        document.getElementById('boldBtn').classList.toggle('active', isBold);
        document.getElementById('italicBtn').classList.toggle('active', isItalic);

        layer.draw();
        updateStatusBar();
    }

    function deselect() {
        selectedNode = null;
        transformer.nodes([]);
        layer.draw();
        updateStatusBar();
    }

    // ─── Add Text ────────────────────────────────────────────────────
    function addText() {
        textCount++;
        const color = document.getElementById('colorPicker').value;
        const size  = parseInt(document.getElementById('fontSize').value) || 30;

        const text = new Konva.Text({
            x: 60 + (textCount % 6) * 30,
            y: 60 + (textCount % 5) * 30,
            text: 'Text ' + textCount,
            fontSize: size,
            fontFamily: 'Georgia, serif',
            fill: color,
            draggable: true,        // ← must be true on the node
            name: 'text-node',
        });

        layer.add(text);
        layer.draw();
        updateStatusBar();

        // ── Click / tap → select ──────────────────────────────────
        text.on('click tap', function (e) {
            e.cancelBubble = true;  // ← stops the click reaching the stage deselect handler
            selectNode(text);
        });

        // ── Double-click → inline edit ────────────────────────────
        text.on('dblclick dbltap', function () {
            startEditing(text);
        });

        // ── Cursor ───────────────────────────────────────────────
        text.on('mouseenter', function () {
            stage.container().style.cursor = 'move';
        });

        text.on('mouseleave', function () {
            stage.container().style.cursor = 'default';
        });

        // ── Drag events ──────────────────────────────────────────
        text.on('dragstart', function () {
            stage.container().style.cursor = 'grabbing';
        });

        text.on('dragend', function () {
            stage.container().style.cursor = 'move';
            layer.draw();
        });

        // Select the newly added text
        selectNode(text);
        toast('Text added — double-click to edit');
    }

    // ─── Inline text editing ──────────────────────────────────────────
    function startEditing(textNode) {
        textNode.hide();
        transformer.hide();
        layer.draw();

        const pos      = stage.container().getBoundingClientRect();
        const nodePos  = textNode.getAbsolutePosition();

        const textarea = document.createElement('textarea');
        document.body.appendChild(textarea);

        textarea.value = textNode.text();
        textarea.style.cssText = `
            position: absolute;
            top:  ${pos.top  + nodePos.y - 4}px;
            left: ${pos.left + nodePos.x - 4}px;
            width: ${Math.max(textNode.width(), 120)}px;
            min-height: ${textNode.fontSize() + 12}px;
            font-size: ${textNode.fontSize()}px;
            font-family: ${textNode.fontFamily()};
            font-style: ${textNode.fontStyle()};
            color: ${textNode.fill()};
            border: 1.5px dashed #2aff8f;
            border-radius: 4px;
            background: rgba(255,255,255,0.95);
            padding: 2px 6px;
            resize: none;
            outline: none;
            overflow: hidden;
            z-index: 9999;
            line-height: 1.3;
        `;

        textarea.focus();
        textarea.select();

        function finish() {
            textNode.text(textarea.value || 'Text');
            document.body.removeChild(textarea);
            textNode.show();
            transformer.show();
            transformer.nodes([textNode]);
            layer.draw();
            updateStatusBar();
        }

        textarea.addEventListener('blur', finish);
        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') finish();
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); finish(); }
        });
    }

    // ─── Apply Style ─────────────────────────────────────────────────
    function updateTextStyle() {
        if (!selectedNode) { toast('Select a text first'); return; }

        const color = document.getElementById('colorPicker').value;
        const size  = parseInt(document.getElementById('fontSize').value) || 30;

        selectedNode.fill(color);
        selectedNode.fontSize(size);

        const fontStyle = [isBold ? 'bold' : '', isItalic ? 'italic' : ''].filter(Boolean).join(' ') || 'normal';
        selectedNode.fontStyle(fontStyle);

        layer.draw();
        toast('Style applied');
    }

    // ─── Toggle Bold / Italic ─────────────────────────────────────────
    function toggleBold() {
        isBold = !isBold;
        document.getElementById('boldBtn').classList.toggle('active', isBold);
        if (selectedNode) updateTextStyle();
    }

    function toggleItalic() {
        isItalic = !isItalic;
        document.getElementById('italicBtn').classList.toggle('active', isItalic);
        if (selectedNode) updateTextStyle();
    }

    // ─── Delete ──────────────────────────────────────────────────────
    function deleteSelected() {
        if (!selectedNode) { toast('Nothing selected'); return; }
        transformer.nodes([]);
        selectedNode.destroy();
        selectedNode = null;
        layer.draw();
        updateStatusBar();
        toast('Deleted');
    }

    function clearAll() {
        if (!confirm('Clear all objects?')) return;
        layer.children.filter(n => n.className === 'Text').forEach(n => n.destroy());
        transformer.nodes([]);
        selectedNode = null;
        textCount = 0;
        layer.draw();
        updateStatusBar();
        toast('Canvas cleared');
    }

    // ─── Stage click → deselect ───────────────────────────────────────
    stage.on('click tap', function (e) {
        // e.target is exactly what was clicked
        // If it has the name 'text-node' it's one of our texts — don't deselect
        if (e.target.name() === 'text-node') return;
        
        // Clicked on transformer anchor — don't deselect
        if (e.target.getParent() && e.target.getParent().className === 'Transformer') return;
        
        // Truly clicked on empty canvas
        deselect();
    });

    // ─── Save ────────────────────────────────────────────────────────
    function saveTemplate() {
        const elements = [];

        layer.children.forEach(node => {
            if (node.className === 'Text') {
                elements.push({
                    text:      node.text(),
                    x:         node.x(),
                    y:         node.y(),
                    width:      node.width(),
                    fontSize:  node.fontSize(),
                    fontStyle: node.fontStyle(),
                    fontFamily:node.fontFamily(),
                    color:     node.fill(),
                    rotation:  node.rotation(),
                    scaleX:    node.scaleX(),
                    scaleY:    node.scaleY(),
                });
            }
        });

        fetch("{{ route('save.template') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ elements }),
        })
        .then(res => res.json())
        .then(() => toast('✓ Template saved!'))
        .catch(() => toast('Save failed — check console'));
    }

    // Init status
    updateStatusBar();
</script>

@endsection