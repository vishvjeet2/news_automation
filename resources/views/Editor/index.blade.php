<!-- resources/views/editor/index.blade.php -->
@extends('layouts.editor')

@section('title', $design ? 'Edit: ' . $design->name : 'New Design')

@section('content')
<div class="editor-container">
    <!-- Top Toolbar -->
    <header class="editor-header">
        <div class="header-left">
            <a href="{{ route('designs.list') }}" class="btn btn-icon" title="Back to designs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="design-name-container">
                <input type="text" id="designName" value="{{ $design ? $design->name : 'Untitled Design' }}" 
                       class="design-name-input" placeholder="Design name">
            </div>
        </div>
        
        <div class="header-center">
            <div class="history-controls">
                <button class="btn btn-icon" id="undoBtn" title="Undo (Ctrl+Z)" disabled>
                    <i class="fas fa-undo"></i>
                </button>
                <button class="btn btn-icon" id="redoBtn" title="Redo (Ctrl+Y)" disabled>
                    <i class="fas fa-redo"></i>
                </button>
            </div>
            
            <div class="zoom-controls">
                <button class="btn btn-icon" id="zoomOutBtn" title="Zoom Out">
                    <i class="fas fa-search-minus"></i>
                </button>
                <span id="zoomLevel">100%</span>
                <button class="btn btn-icon" id="zoomInBtn" title="Zoom In">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button class="btn btn-icon" id="zoomFitBtn" title="Fit to Screen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        
        <div class="header-right">
            <button class="btn btn-secondary" id="previewBtn">
                <i class="fas fa-eye"></i> Preview
            </button>
            <button class="btn btn-secondary" id="exportBtn">
                <i class="fas fa-download"></i> Export
            </button>
            <button class="btn btn-primary" id="saveBtn">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </header>

    <div class="editor-body">
        <!-- Left Sidebar - Tools -->
        <aside class="sidebar sidebar-left">
            <div class="sidebar-section">
                <h3>Canvas Size</h3>
                <div class="canvas-size-controls">
                    <div class="size-input-group">
                        <label>Width</label>
                        <input type="number" id="canvasWidth" value="{{ $design ? $design->width : 800 }}" min="100" max="4000">
                    </div>
                    <div class="size-input-group">
                        <label>Height</label>
                        <input type="number" id="canvasHeight" value="{{ $design ? $design->height : 600 }}" min="100" max="4000">
                    </div>
                    <button class="btn btn-sm btn-block" id="applyCanvasSize">Apply Size</button>
                </div>
                
                <div class="preset-sizes">
                    <label>Presets:</label>
                    <select id="presetSizes">
                        <option value="">Select template...</option>
                        @forelse($templates as $template)
                            <option value="{{ $template->id }}" 
                                    data-id="{{ $template->id }}"
                                    data-name="{{ $template->name }}"
                                    data-image="{{ asset('storage/' . $template->template_path) }}">
                                {{ $template->name }}
                            </option>
                        @empty
                            <option value="" disabled>No templates available</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- ==================== ADD AI ASSIST SECTION HERE ==================== -->
            <div class="sidebar-section">
                <h3>AI Assistant</h3>
                <button type="button" id="aiAssistBtn" class="btn btn-block" style="background: #3b82f6; color: white;">
                    <i class="fas fa-magic"></i> ✨ AI Assist
                </button>
            </div>
            <!-- ==================== END AI ASSIST SECTION ==================== -->

            <div class="sidebar-section">
                <h3>Tools</h3>
                <div class="tools-grid">
                    <button class="tool-btn active" data-tool="select" title="Select (V)">
                        <i class="fas fa-mouse-pointer"></i>
                        <span>Select</span>
                    </button>
                    <button class="tool-btn" data-tool="text" title="Add Text (T)">
                        <i class="fas fa-font"></i>
                        <span>Text</span>
                    </button>
                    <button class="tool-btn" data-tool="image" title="Add Image (I)">
                        <i class="fas fa-image"></i>
                        <span>Image</span>
                    </button>
                    <button class="tool-btn" data-tool="rectangle" title="Rectangle (R)">
                        <i class="fas fa-square"></i>
                        <span>Rectangle</span>
                    </button>
                    <button class="tool-btn" data-tool="circle" title="Circle (C)">
                        <i class="fas fa-circle"></i>
                        <span>Circle</span>
                    </button>
                    <button class="tool-btn" data-tool="line" title="Line (L)">
                        <i class="fas fa-minus"></i>
                        <span>Line</span>
                    </button>
                    <button class="tool-btn" data-tool="arrow" title="Arrow (A)">
                        <i class="fas fa-long-arrow-alt-right"></i>
                        <span>Arrow</span>
                    </button>
                    <button class="tool-btn" data-tool="triangle" title="Triangle">
                        <i class="fas fa-play fa-rotate-270"></i>
                        <span>Triangle</span>
                    </button>
                    <button class="tool-btn" data-tool="star" title="Star">
                        <i class="fas fa-star"></i>
                        <span>Star</span>
                    </button>
                    <button class="tool-btn" data-tool="polygon" title="Polygon">
                        <i class="fas fa-draw-polygon"></i>
                        <span>Polygon</span>
                    </button>
                </div>
            </div>

            <div class="sidebar-section">
                <h3>Upload Image</h3>
                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Drag & drop or click</p>
                    <input type="file" id="imageUpload" accept="image/*" multiple hidden>
                </div>
            </div>

            <div class="sidebar-section">
                <h3>My Images</h3>
                <div class="images-grid" id="imagesGrid">
                    @foreach($uploadedImages as $image)
                        <div class="image-item" data-id="{{ $image->id }}" data-url="{{ Storage::url($image->path) }}">
                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->filename }}">
                            <button class="delete-image-btn" title="Delete">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Main Canvas Area -->
        <main class="canvas-area">
            <div class="canvas-wrapper" id="canvasWrapper">
                <div id="konvaContainer"></div>
            </div>
        </main>

        <!-- Right Sidebar - Properties -->
        <aside class="sidebar sidebar-right">
            <!-- Canvas Properties (default) -->
            <div class="properties-panel" id="canvasProperties">
                <h3>Canvas Properties</h3>
                <div class="property-group">
                    <label>Background Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="canvasBgColor" value="#ffffff">
                        <input type="text" id="canvasBgColorText" value="#ffffff" class="color-text-input">
                    </div>
                </div>
            </div>

            <!-- Text Properties -->
            <div class="properties-panel" id="textProperties" style="display: none;">
                <h3>Text Properties</h3>
                
                <div class="property-group">
                    <label>Text Content</label>
                    <textarea id="textContent" rows="3" placeholder="Enter text..."></textarea>
                </div>
                
                <div class="property-group">
                    <label>Font Family</label>
                    <select id="fontFamily">
                        @foreach($fonts as $font)
                            <option value="{{ $font }}" style="font-family: '{{ $font }}'">{{ $font }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="property-row">
                    <div class="property-group">
                        <label>Size</label>
                        <input type="number" id="fontSize" value="24" min="8" max="200">
                    </div>
                    <div class="property-group">
                        <label>Line Height</label>
                        <input type="number" id="lineHeight" value="1.2" min="0.5" max="3" step="0.1">
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Font Style</label>
                    <div class="style-buttons">
                        <button class="style-btn" id="boldBtn" title="Bold">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button class="style-btn" id="italicBtn" title="Italic">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button class="style-btn" id="underlineBtn" title="Underline">
                            <i class="fas fa-underline"></i>
                        </button>
                        <button class="style-btn" id="strikeBtn" title="Strikethrough">
                            <i class="fas fa-strikethrough"></i>
                        </button>
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Text Align</label>
                    <div class="align-buttons">
                        <button class="align-btn active" data-align="left" title="Align Left">
                            <i class="fas fa-align-left"></i>
                        </button>
                        <button class="align-btn" data-align="center" title="Align Center">
                            <i class="fas fa-align-center"></i>
                        </button>
                        <button class="align-btn" data-align="right" title="Align Right">
                            <i class="fas fa-align-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Text Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="textColor" value="#000000">
                        <input type="text" id="textColorText" value="#000000" class="color-text-input">
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Background Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="textBgColor" value="#ffffff">
                        <input type="text" id="textBgColorText" value="#ffffff" class="color-text-input">
                        <button class="btn btn-sm" id="clearTextBg">Clear</button>
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Opacity</label>
                    <input type="range" id="textOpacity" min="0" max="1" step="0.1" value="1">
                    <span id="textOpacityValue">100%</span>
                </div>
            </div>

            <!-- Shape Properties -->
            <div class="properties-panel" id="shapeProperties" style="display: none;">
                <h3>Shape Properties</h3>
                
                <div class="property-group">
                    <label>Fill Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="shapeFillColor" value="#3498db">
                        <input type="text" id="shapeFillColorText" value="#3498db" class="color-text-input">
                        <button class="btn btn-sm" id="clearShapeFill">No Fill</button>
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Stroke Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="shapeStrokeColor" value="#2c3e50">
                        <input type="text" id="shapeStrokeColorText" value="#2c3e50" class="color-text-input">
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Stroke Width</label>
                    <input type="number" id="shapeStrokeWidth" value="2" min="0" max="50">
                </div>
                
                <div class="property-group">
                    <label>Corner Radius (Rectangle)</label>
                    <input type="number" id="shapeCornerRadius" value="0" min="0" max="100">
                </div>
                
                <div class="property-group">
                    <label>Opacity</label>
                    <input type="range" id="shapeOpacity" min="0" max="1" step="0.1" value="1">
                    <span id="shapeOpacityValue">100%</span>
                </div>
                
                <div class="property-group" id="starPointsGroup" style="display: none;">
                    <label>Number of Points</label>
                    <input type="number" id="starPoints" value="5" min="3" max="20">
                </div>
                
                <div class="property-group" id="polygonSidesGroup" style="display: none;">
                    <label>Number of Sides</label>
                    <input type="number" id="polygonSides" value="6" min="3" max="20">
                </div>
            </div>

            <!-- Image Properties -->
            <div class="properties-panel" id="imageProperties" style="display: none;">
                <h3>Image Properties</h3>
                
                <div class="property-row">
                    <div class="property-group">
                        <label>Width</label>
                        <input type="number" id="imageWidth" min="10">
                    </div>
                    <div class="property-group">
                        <label>Height</label>
                        <input type="number" id="imageHeight" min="10">
                    </div>
                </div>
                
                <div class="property-group">
                    <label>
                        <input type="checkbox" id="imageLockRatio" checked> Lock Aspect Ratio
                    </label>
                </div>
                
                <div class="property-group">
                    <label>Opacity</label>
                    <input type="range" id="imageOpacity" min="0" max="1" step="0.1" value="1">
                    <span id="imageOpacityValue">100%</span>
                </div>
                
                <div class="property-group">
                    <label>Corner Radius</label>
                    <input type="number" id="imageCornerRadius" value="0" min="0" max="100">
                </div>
                
                <div class="property-group">
                    <label>Border Width</label>
                    <input type="number" id="imageBorderWidth" value="0" min="0" max="50">
                </div>
                
                <div class="property-group">
                    <label>Border Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="imageBorderColor" value="#000000">
                        <input type="text" id="imageBorderColorText" value="#000000" class="color-text-input">
                    </div>
                </div>
                
                <div class="property-group">
                    <button class="btn btn-block" id="replaceImageBtn">
                        <i class="fas fa-exchange-alt"></i> Replace Image
                    </button>
                </div>

                <div class="property-group">
                    <h4>Filters</h4>
                    <div class="filters-grid">
                        <button class="filter-btn" data-filter="none">None</button>
                        <button class="filter-btn" data-filter="grayscale">Grayscale</button>
                        <button class="filter-btn" data-filter="sepia">Sepia</button>
                        <button class="filter-btn" data-filter="blur">Blur</button>
                        <button class="filter-btn" data-filter="brighten">Brighten</button>
                        <button class="filter-btn" data-filter="contrast">Contrast</button>
                    </div>
                </div>
            </div>

            <!-- Common Properties (shown for all selected elements) -->
            <div class="properties-panel" id="commonProperties" style="display: none;">
                <h3>Position & Size</h3>
                
                <div class="property-row">
                    <div class="property-group">
                        <label>X</label>
                        <input type="number" id="elementX">
                    </div>
                    <div class="property-group">
                        <label>Y</label>
                        <input type="number" id="elementY">
                    </div>
                </div>
                
                <div class="property-row">
                    <div class="property-group">
                        <label>Width</label>
                        <input type="number" id="elementWidth">
                    </div>
                    <div class="property-group">
                        <label>Height</label>
                        <input type="number" id="elementHeight">
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Rotation</label>
                    <input type="number" id="elementRotation" value="0" min="-360" max="360">
                </div>
                
                <div class="property-group">
                    <label>Layer Order</label>
                    <div class="layer-buttons">
                        <button class="btn btn-sm" id="bringToFront" title="Bring to Front">
                            <i class="fas fa-angle-double-up"></i>
                        </button>
                        <button class="btn btn-sm" id="bringForward" title="Bring Forward">
                            <i class="fas fa-angle-up"></i>
                        </button>
                        <button class="btn btn-sm" id="sendBackward" title="Send Backward">
                            <i class="fas fa-angle-down"></i>
                        </button>
                        <button class="btn btn-sm" id="sendToBack" title="Send to Back">
                            <i class="fas fa-angle-double-down"></i>
                        </button>
                    </div>
                </div>
                
                <div class="property-group">
                    <label>Actions</label>
                    <div class="action-buttons">
                        <button class="btn btn-sm" id="duplicateElement" title="Duplicate">
                            <i class="fas fa-copy"></i> Duplicate
                        </button>
                        <button class="btn btn-sm btn-danger" id="deleteElement" title="Delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Layers Panel -->
            <div class="properties-panel" id="layersPanel">
                <h3>Layers</h3>
                <div class="layers-list" id="layersList">
                    <!-- Layers will be dynamically added here -->
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Modals -->
<div class="modal" id="previewModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Preview</h2>
            <button class="modal-close" onclick="closeModal('previewModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="previewContainer"></div>
        </div>
    </div>
</div>

<div class="modal" id="exportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Export Design</h2>
            <button class="modal-close" onclick="closeModal('exportModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="export-options">
                <div class="property-group">
                    <label>Filename</label>
                    <input type="text" id="exportFilename" value="my-design">
                </div>
                <div class="property-group">
                    <label>Format</label>
                    <select id="exportFormat">
                        <option value="png">PNG (Transparent)</option>
                        <option value="jpg">JPG</option>
                    </select>
                </div>
                <div class="property-group">
                    <label>Quality (for JPG)</label>
                    <input type="range" id="exportQuality" min="0.1" max="1" step="0.1" value="0.9">
                    <span id="exportQualityValue">90%</span>
                </div>
                <div class="property-group">
                    <label>Scale</label>
                    <select id="exportScale">
                        <option value="1">1x (Original)</option>
                        <option value="2">2x (Double)</option>
                        <option value="0.5">0.5x (Half)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('exportModal')">Cancel</button>
            <button class="btn btn-primary" id="confirmExport">
                <i class="fas fa-download"></i> Download
            </button>
        </div>
    </div>
</div>

<div class="modal" id="saveModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Save Design</h2>
            <button class="modal-close" onclick="closeModal('saveModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="property-group">
                <label>Design Name</label>
                <input type="text" id="saveDesignName" value="{{ $design ? $design->name : 'Untitled Design' }}">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('saveModal')">Cancel</button>
            <button class="btn btn-primary" id="confirmSave">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-container" id="toastContainer"></div>

<!-- Store design data if editing -->
<script>
    window.designId = {{ $design ? $design->id : 'null' }};
    window.designData = {!! $design ? json_encode($design->design_data) : 'null' !!};
    window.canvasWidth = {{ $design ? $design->width : 800 }};
    window.canvasHeight = {{ $design ? $design->height : 600 }};
</script>

<!-- ... existing modals ... -->

<!-- ==================== AI MODAL ==================== -->
<div id="aiModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>AI News Assistant</h2>
            <button class="modal-close" id="aiCloseBtn">&times;</button>
        </div>
        <div class="modal-body">
            <textarea id="aiRawInput" class="w-full border border-gray-300 rounded-md px-4 py-2 mb-4" 
                      rows="6" placeholder="Paste raw news content or points here..." 
                      style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 16px;"></textarea>

            <div class="modal-footer" style="margin-bottom: 24px;">
                <button type="button" id="aiCloseBtn2" class="btn btn-secondary">Cancel</button>
                <button type="button" id="aiAnalyzeBtn" class="btn btn-primary">
                    <i class="fas fa-brain"></i> Analyze Content
                </button>
            </div>

            <div id="aiLoading" class="hidden" style="text-align: center; padding: 20px;">
                <div style="display: inline-block; width: 24px; height: 24px; border: 4px solid #000; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 8px; color: #6b7280; font-size: 14px;">Processing News...</p>
            </div>

            <div id="aiPreviewSection" class="hidden" style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                <h3 style="font-weight: bold; color: #1f2937; margin-bottom: 16px;">AI Suggestion Preview</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Headline</label>
                        <div id="aiHeadlinePreview" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; background: #f9fafb; font-size: 14px; font-style: italic;"></div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Detected City</label>
                        <div id="aiCityPreview" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; background: #f9fafb; font-size: 14px; font-weight: 600;"></div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Detected Hashtag</label>
                        <div id="aihashtagPreview" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; background: #f9fafb; font-size: 14px; font-weight: 600;"></div>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Description</label>
                    <div id="aiDescriptionPreview" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; background: #f9fafb; font-size: 14px;"></div>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Caption</label>
                    <div id="aiCaptionPreview" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; background: #f9fafb; font-size: 14px;"></div>
                </div>

                <div id="aiRiskPreview" class="hidden" style="padding: 12px; border: 1px solid #f87171; background: #fef2f2; border-radius: 6px; font-size: 14px; color: #b91c1c; margin-bottom: 16px;"></div>

                <div class="modal-footer">
                    <button type="button" id="aiRegenerateBtn" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                    <button type="button" id="aiApplyBtn" class="btn" style="background: #10b981; color: white;">
                        <i class="fas fa-check"></i> Apply to Canvas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Caption Section (appears after applying) -->
<div id="aiCaptionSection" class="hidden" style="position: fixed; bottom: 20px; right: 20px; width: 400px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); z-index: 1000;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h3 style="font-size: 14px; font-weight: 600; color: #374151;">AI Generated Caption</h3>
        <div>
            <button type="button" id="copyCaptionBtn" class="btn btn-sm" style="margin-right: 4px;">
                <i class="fas fa-copy"></i> Copy
            </button>
            <button type="button" id="clearCaptionBtn" class="btn btn-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <textarea id="aiCaptionText" maxlength="280" rows="3" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 13px;"></textarea>
    <div style="display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; margin-top: 4px;">
        <span>Max 280 characters</span>
        <span id="captionCounter">0 / 280</span>
    </div>
</div>

<!-- AI Risk Warning Section -->
<div id="aiRiskSection" class="hidden" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); width: 500px; background: white; border: 2px solid #f87171; border-radius: 8px; padding: 16px; box-shadow: 0 10px 15px rgba(0,0,0,0.2); z-index: 1001;">
    <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #b91c1c;">⚠️ AI Safety Warning</h3>
    <p id="aiRiskMessage" style="font-size: 13px; color: #991b1b; margin-bottom: 12px;"></p>
    <button type="button" id="acknowledgeRiskBtn" class="btn btn-sm">
        I Understand
    </button>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
.hidden {
    display: none !important;
}
.modal-large {
    max-width: 800px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let aiResponseData = null;

    // Open Modal
    document.getElementById('aiAssistBtn').addEventListener('click', () => {
        document.getElementById('aiModal').classList.add('active');
    });

    // Close Modal
    document.getElementById('aiCloseBtn').addEventListener('click', () => {
        document.getElementById('aiModal').classList.remove('active');
    });
    document.getElementById('aiCloseBtn2').addEventListener('click', () => {
        document.getElementById('aiModal').classList.remove('active');
    });

    // Analyze Content
    document.getElementById('aiAnalyzeBtn').addEventListener('click', function() {
        const rawContent = document.getElementById('aiRawInput').value;

        if (!rawContent.trim()) {
            alert("Please paste content first.");
            return;
        }

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
                template_type: 'text'
            })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('aiLoading').classList.add('hidden');
            
            if (data.error) {
                alert("Error: " + data.error);
                return;
            }

            aiResponseData = data;
            
            // Update Preview
            document.getElementById('aiHeadlinePreview').innerText = data.headline || '';
            document.getElementById('aiDescriptionPreview').innerText = data.description || '';
            document.getElementById('aiCaptionPreview').innerText = data.caption || '';
            document.getElementById('aiCityPreview').innerText = data.city || 'Not detected';
            document.getElementById('aihashtagPreview').innerText = data.hashtag || 'Not detected';

            // Risk Warning
            const riskBox = document.getElementById('aiRiskPreview');
            if (data.risk_flag) {
                riskBox.innerText = data.risk_reason;
                riskBox.classList.remove('hidden');
            } else {
                riskBox.classList.add('hidden');
            }

            document.getElementById('aiPreviewSection').classList.remove('hidden');
        })
        .catch(err => {
            document.getElementById('aiLoading').classList.add('hidden');
            console.error(err);
            alert("AI Service unavailable.");
        });
    });

    // Apply to Canvas
    // Apply to Canvas
    document.getElementById('aiApplyBtn').addEventListener('click', function() {
        if (!aiResponseData) return;

        // Add text elements to canvas
        if (window.editor && aiResponseData.headline) {
            window.editor.deselectAll();
            
            // Add headline - BLACK TEXT
            const headline = new Konva.Text({
                x: 50,
                y: 50,
                text: aiResponseData.headline,
                fontSize: 48,
                fontFamily: 'Arial',
                fill: '#000000',           // BLACK
                stroke: null,
                fontStyle: 'bold',
                draggable: true,
                id: window.editor.generateId(),
                name: 'text',
                width: window.editor.stage.width() - 100
            });
            
            window.editor.layer.add(headline);
            headline.moveToTop();
            window.editor.transformer.moveToTop();
            window.editor.layer.batchDraw();
            window.editor.saveHistory();
            window.editor.updateLayersList();

            // Add description if exists - DARK GRAY TEXT
            if (aiResponseData.description) {
                const description = new Konva.Text({
                    x: 50,
                    y: 150,
                    text: aiResponseData.description,
                    fontSize: 24,
                    fontFamily: 'Arial',
                    fill: '#1a1a1a',        // DARK GRAY (almost black)
                    stroke: null,
                    draggable: true,
                    id: window.editor.generateId(),
                    name: 'text',
                    width: window.editor.stage.width() - 100
                });
                
                window.editor.layer.add(description);
                description.moveToTop();
                window.editor.transformer.moveToTop();
                window.editor.layer.batchDraw();
                window.editor.saveHistory();
                window.editor.updateLayersList();
            }

            // Add city if detected - BLACK TEXT
            if (aiResponseData.city) {
                const city = new Konva.Text({
                    x: 50,
                    y: window.editor.stage.height() - 80,
                    text: aiResponseData.city,
                    fontSize: 32,
                    fontFamily: 'Arial',
                    fill: '#000000',        // BLACK
                    stroke: null,
                    fontStyle: 'bold',
                    draggable: true,
                    id: window.editor.generateId(),
                    name: 'text'
                });
                
                window.editor.layer.add(city);
                city.moveToTop();
                window.editor.transformer.moveToTop();
                window.editor.layer.batchDraw();
                window.editor.saveHistory();
                window.editor.updateLayersList();
            }

            // Add hashtag if exists - BLUE TEXT
            if (aiResponseData.hashtag) {
                const hashtag = new Konva.Text({
                    x: 50,
                    y: window.editor.stage.height() - 40,
                    text: aiResponseData.hashtag,
                    fontSize: 20,
                    fontFamily: 'Arial',
                    fill: '#2563eb',        // BLUE
                    stroke: null,
                    draggable: true,
                    id: window.editor.generateId(),
                    name: 'text'
                });
                
                window.editor.layer.add(hashtag);
                hashtag.moveToTop();
                window.editor.transformer.moveToTop();
                window.editor.layer.batchDraw();
                window.editor.saveHistory();
                window.editor.updateLayersList();
            }
        }

        // Show caption section
        if (aiResponseData.caption) {
            document.getElementById('aiCaptionText').value = aiResponseData.caption;
            document.getElementById('aiCaptionSection').classList.remove('hidden');
            document.getElementById('captionCounter').innerText = aiResponseData.caption.length + " / 280";
        }

        // Show risk warning
        if (aiResponseData.risk_flag) {
            document.getElementById('aiRiskMessage').innerText = aiResponseData.risk_reason;
            document.getElementById('aiRiskSection').classList.remove('hidden');
        }

        // Close modal
        document.getElementById('aiModal').classList.remove('active');
        
        // Show success toast
        if (window.editor) {
            window.editor.showToast('AI content added to canvas!', 'success');
        }
    });

    // Regenerate
    document.getElementById('aiRegenerateBtn').addEventListener('click', () => {
        document.getElementById('aiAnalyzeBtn').click();
    });

    // Copy Caption
    document.getElementById('copyCaptionBtn').addEventListener('click', () => {
        const caption = document.getElementById('aiCaptionText').value;
        navigator.clipboard.writeText(caption).then(() => {
            alert("Caption copied to clipboard!");
        });
    });

    // Clear Caption
    document.getElementById('clearCaptionBtn').addEventListener('click', () => {
        document.getElementById('aiCaptionSection').classList.add('hidden');
    });

    // Acknowledge Risk
    document.getElementById('acknowledgeRiskBtn').addEventListener('click', () => {
        document.getElementById('aiRiskSection').classList.add('hidden');
    });

    // Caption Counter
    document.getElementById('aiCaptionText').addEventListener('input', function() {
        document.getElementById('captionCounter').innerText = this.value.length + " / 280";
    });
});
</script>

<!-- Toast Notification -->
<div class="toast-container" id="toastContainer"></div>


@endsection

@push('scripts')
<script src="{{ asset('js/editor.js') }}"></script>
@endpush