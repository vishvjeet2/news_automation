// public/js/editor.js

/**
 * Konva Canvas Editor - With Template Support
 * All drag, resize, and color issues resolved
 */

class KonvaEditor {
    constructor() {
        // Core Konva objects
        this.stage = null;
        this.layer = null;
        this.backgroundRect = null;
        this.backgroundImage = null; // Store template background image
        this.transformer = null;
        
        // State management
        this.selectedElement = null;
        this.currentTool = 'select';
        this.history = [];
        this.historyIndex = -1;
        this.maxHistory = 50;
        this.zoom = 1;
        this.clipboard = null;
        this.designId = window.designId || null;
        this.isEditing = false; // Track if we're editing text
        this.currentTemplateId = null; // Track current template
        
        // Initialize the editor
        this.init();
    }

    init() {
        this.setupCanvas();
        this.setupTransformer();
        this.setupEventListeners();
        this.loadDesignData();
        this.updateLayersList();
        this.saveHistory();
        
        console.log('Konva Editor Initialized Successfully');
    }

    setupCanvas() {
        const width = window.canvasWidth || 800;
        const height = window.canvasHeight || 600;

        // Create stage
        this.stage = new Konva.Stage({
            container: 'konvaContainer',
            width: width,
            height: height
        });

        // Create layer
        this.layer = new Konva.Layer();
        this.stage.add(this.layer);

        // Create background
        this.backgroundRect = new Konva.Rect({
            x: 0,
            y: 0,
            width: width,
            height: height,
            fill: '#ffffff',
            name: 'background',
            listening: true
        });
        this.layer.add(this.backgroundRect);

        // Update canvas size inputs
        const widthInput = document.getElementById('canvasWidth');
        const heightInput = document.getElementById('canvasHeight');
        if (widthInput) widthInput.value = width;
        if (heightInput) heightInput.value = height;

        this.layer.draw();
    }

    setupTransformer() {
        // Create transformer with all anchors enabled
        this.transformer = new Konva.Transformer({
            // Enable all anchors
            enabledAnchors: [
                'top-left', 
                'top-center', 
                'top-right', 
                'middle-left', 
                'middle-right', 
                'bottom-left', 
                'bottom-center', 
                'bottom-right'
            ],
            // Styling
            borderStroke: '#6366f1',
            borderStrokeWidth: 2,
            anchorStroke: '#6366f1',
            anchorFill: '#ffffff',
            anchorSize: 10,
            anchorCornerRadius: 2,
            rotateEnabled: true,
            rotateAnchorOffset: 30,
            // Keep minimum size
            boundBoxFunc: (oldBox, newBox) => {
                // Minimum size of 10 pixels
                if (newBox.width < 10) {
                    newBox.width = 10;
                }
                if (newBox.height < 10) {
                    newBox.height = 10;
                }
                return newBox;
            }
        });

        this.layer.add(this.transformer);
        this.transformer.moveToTop();
    }

    setupEventListeners() {
        // =====================
        // STAGE EVENTS
        // =====================

        // Click on stage - select or deselect elements
        this.stage.on('click tap', (e) => {
            // If clicking on empty area, background, or template image - deselect
            if (e.target === this.stage || 
                e.target === this.backgroundRect || 
                e.target === this.backgroundImage ||
                e.target.name() === 'templateBackground' ||
                e.target.id() === 'template_bg') {
                this.deselectAll();
                return;
            }

            // Don't select if we're in editing mode
            if (this.isEditing) {
                return;
            }

            // Select the clicked element
            const clickedElement = e.target;
            
            // Check if it's a valid element
            if (clickedElement.getClassName() !== 'Transformer' && 
                clickedElement.name() !== 'background' &&
                clickedElement.name() !== 'templateBackground') {
                this.selectElement(clickedElement);
            }
        });

        // Double click for text editing
        this.stage.on('dblclick dbltap', (e) => {
            const target = e.target;
            
            // Only edit text elements
            if (target.getClassName() === 'Text') {
                this.editText(target);
            }
        });

        // Mouse down - track for drag
        this.stage.on('mousedown touchstart', (e) => {
            // If editing text, don't interfere
            if (this.isEditing) {
                return;
            }
        });

        // =====================
        // TRANSFORMER EVENTS
        // =====================

        this.transformer.on('transformstart', () => {
            console.log('Transform started');
        });

        this.transformer.on('transform', () => {
            // Update properties panel during transform
            this.updatePropertiesPanel();
        });

        this.transformer.on('transformend', () => {
            console.log('Transform ended');
            this.updatePropertiesPanel();
            this.saveHistory();
            this.updateLayersList();
        });

        // =====================
        // DRAG EVENTS
        // =====================

        this.stage.on('dragstart', (e) => {
            if (e.target === this.stage || e.target === this.backgroundRect || e.target === this.backgroundImage) {
                return;
            }
            console.log('Drag started:', e.target.getClassName());
        });

        this.stage.on('dragmove', (e) => {
            if (e.target === this.stage || e.target === this.backgroundRect || e.target === this.backgroundImage) {
                return;
            }
            this.updatePropertiesPanel();
        });

        this.stage.on('dragend', (e) => {
            if (e.target === this.stage || e.target === this.backgroundRect || e.target === this.backgroundImage) {
                return;
            }
            console.log('Drag ended');
            this.updatePropertiesPanel();
            this.saveHistory();
        });

        // =====================
        // KEYBOARD EVENTS
        // =====================

        document.addEventListener('keydown', (e) => this.handleKeyboard(e));

        // =====================
        // TOOL BUTTONS
        // =====================

        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Update active state
                document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                this.currentTool = btn.dataset.tool;
                
                // Handle tool action
                if (this.currentTool !== 'select') {
                    this.handleToolAction(this.currentTool);
                }
            });
        });

        // =====================
        // CANVAS SIZE CONTROLS
        // =====================

        const applyCanvasSizeBtn = document.getElementById('applyCanvasSize');
        if (applyCanvasSizeBtn) {
            applyCanvasSizeBtn.addEventListener('click', () => {
                const width = parseInt(document.getElementById('canvasWidth').value);
                const height = parseInt(document.getElementById('canvasHeight').value);
                this.resizeCanvas(width, height);
            });
        }

        // =====================
        // TEMPLATE/PRESET SELECTION - UPDATED
        // =====================

        const presetSizes = document.getElementById('presetSizes');
        if (presetSizes) {
            presetSizes.addEventListener('change', (e) => {
                this.handleTemplateSelection(e);
            });
        }

        // =====================
        // CANVAS BACKGROUND COLOR
        // =====================

        this.setupColorInput('canvasBgColor', 'canvasBgColorText', (color) => {
            this.backgroundRect.fill(color);
            this.layer.batchDraw();
            this.saveHistory();
        });

        // =====================
        // IMAGE UPLOAD
        // =====================

        this.setupImageUpload();

        // =====================
        // ZOOM CONTROLS
        // =====================

        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const zoomFitBtn = document.getElementById('zoomFitBtn');

        if (zoomInBtn) zoomInBtn.addEventListener('click', () => this.setZoom(this.zoom + 0.1));
        if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => this.setZoom(this.zoom - 0.1));
        if (zoomFitBtn) zoomFitBtn.addEventListener('click', () => this.fitToScreen());

        // =====================
        // HISTORY CONTROLS
        // =====================

        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');

        if (undoBtn) undoBtn.addEventListener('click', () => this.undo());
        if (redoBtn) redoBtn.addEventListener('click', () => this.redo());

        // =====================
        // SAVE, EXPORT, PREVIEW
        // =====================

        const saveBtn = document.getElementById('saveBtn');
        const exportBtn = document.getElementById('exportBtn');
        const previewBtn = document.getElementById('previewBtn');
        const confirmSave = document.getElementById('confirmSave');
        const confirmExport = document.getElementById('confirmExport');

        if (saveBtn) saveBtn.addEventListener('click', () => this.openSaveModal());
        if (exportBtn) exportBtn.addEventListener('click', () => this.openExportModal());
        if (previewBtn) previewBtn.addEventListener('click', () => this.showPreview());
        if (confirmSave) confirmSave.addEventListener('click', () => this.save());
        if (confirmExport) confirmExport.addEventListener('click', () => this.export());

        // Export quality display
        const exportQuality = document.getElementById('exportQuality');
        if (exportQuality) {
            exportQuality.addEventListener('input', (e) => {
                const display = document.getElementById('exportQualityValue');
                if (display) display.textContent = Math.round(e.target.value * 100) + '%';
            });
        }

        // =====================
        // TEXT PROPERTIES
        // =====================

        this.setupTextProperties();

        // =====================
        // SHAPE PROPERTIES
        // =====================

        this.setupShapeProperties();

        // =====================
        // IMAGE PROPERTIES
        // =====================

        this.setupImageProperties();

        // =====================
        // COMMON PROPERTIES
        // =====================

        this.setupCommonProperties();

        // =====================
        // LAYERS PANEL
        // =====================

        const layersList = document.getElementById('layersList');
        if (layersList) {
            layersList.addEventListener('click', (e) => {
                const layerItem = e.target.closest('.layer-item');
                if (layerItem && !e.target.closest('.layer-actions')) {
                    const id = layerItem.dataset.id;
                    const element = this.layer.findOne('#' + id);
                    if (element) {
                        this.selectElement(element);
                    }
                }
            });
        }

        // =====================
        // CONTEXT MENU
        // =====================

        this.setupContextMenu();
    }

    // ========================================
    // TEMPLATE SELECTION HANDLER - NEW
    // ========================================

    handleTemplateSelection(e) {
        const selectedOption = e.target.options[e.target.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            return;
        }

        const templateId = selectedOption.dataset.id;
        const templateName = selectedOption.dataset.name;
        const imageUrl = selectedOption.dataset.image;

        console.log('Template selected:', { 
            id: templateId, 
            name: templateName, 
            imageUrl: imageUrl 
        });

        // Store current template ID
        this.currentTemplateId = templateId;

        // Load template image
        if (imageUrl && imageUrl.trim() !== '' && imageUrl !== 'undefined' && imageUrl !== 'null') {
            this.loadTemplateImage(imageUrl);
        } else {
            this.showToast('No image found for this template', 'warning');
        }

        // Reset dropdown (optional - remove if you want to keep selection visible)
        // e.target.value = '';
    }

    // ========================================
    // TEMPLATE IMAGE LOADING - NEW
    // ========================================

    loadTemplateImage(imageUrl) {
        console.log('Loading template image:', imageUrl);
    
        this.showLoading(true);
    
        const imageObj = new Image();
        imageObj.crossOrigin = 'Anonymous';
        
        imageObj.onload = () => {
            console.log('Image loaded, dimensions:', imageObj.width, 'x', imageObj.height);
        
            this.clearTemplateImage();
        
            const imgWidth = imageObj.width;
            const imgHeight = imageObj.height;
        
            this.stage.width(imgWidth);
            this.stage.height(imgHeight);
            this.backgroundRect.width(imgWidth);
            this.backgroundRect.height(imgHeight);
        
            const widthInput = document.getElementById('canvasWidth');
            const heightInput = document.getElementById('canvasHeight');
            if (widthInput) widthInput.value = imgWidth;
            if (heightInput) heightInput.value = imgHeight;
        
            // Create background image - MAKE SURE listening is false
            this.backgroundImage = new Konva.Image({
                x: 0,
                y: 0,
                image: imageObj,
                width: imgWidth,
                height: imgHeight,
                name: 'templateBackground',
                listening: false,  // IMPORTANT: Don't capture mouse events
                draggable: false,  // ADD THIS: Prevent dragging
                id: 'template_bg'
            });
        
            this.layer.add(this.backgroundImage);
            
            // IMPORTANT: Correct layer order
            this.backgroundRect.moveToBottom();      // White rect at very bottom
            this.backgroundImage.zIndex(1);          // Template image just above white rect
            this.transformer.moveToTop();            // Transformer always on top
        
            this.layer.batchDraw();
            this.showLoading(false);
            this.fitToScreen();
            this.saveHistory();
        
            console.log('Template image loaded: ' + imgWidth + 'x' + imgHeight);
            this.showToast('Template loaded (' + imgWidth + 'x' + imgHeight + ')', 'success');
        };
    
        imageObj.onerror = (err) => {
            console.error('Failed to load template image:', err);
            this.showLoading(false);
            this.showToast('Failed to load template image.', 'error');
        };
    
        imageObj.src = imageUrl;
    }

    // ========================================
    // CLEAR TEMPLATE IMAGE - NEW
    // ========================================

    clearTemplateImage() {
        if (this.backgroundImage) {
            this.backgroundImage.destroy();
            this.backgroundImage = null;
            this.layer.batchDraw();
            console.log('Template image cleared');
        }
    }

    // ========================================
    // LOADING INDICATOR - NEW
    // ========================================

    showLoading(show) {
        let loader = document.getElementById('canvasLoader');
        
        if (show) {
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'canvasLoader';
                loader.innerHTML = `
                    <div class="loader-spinner"></div>
                    <span>Loading template...</span>
                `;
                loader.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.9);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                    gap: 10px;
                    z-index: 1000;
                `;
                
                const spinnerStyle = document.createElement('style');
                spinnerStyle.textContent = `
                    .loader-spinner {
                        width: 40px;
                        height: 40px;
                        border: 4px solid #f3f3f3;
                        border-top: 4px solid #6366f1;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(spinnerStyle);
                
                const wrapper = document.getElementById('canvasWrapper');
                if (wrapper) {
                    wrapper.style.position = 'relative';
                    wrapper.appendChild(loader);
                }
            }
            loader.style.display = 'flex';
        } else {
            if (loader) {
                loader.style.display = 'none';
            }
        }
    }

    // ========================================
    // COLOR INPUT HELPER
    // ========================================

    setupColorInput(colorInputId, textInputId, callback) {
        const colorInput = document.getElementById(colorInputId);
        const textInput = document.getElementById(textInputId);

        if (colorInput) {
            colorInput.addEventListener('input', (e) => {
                const color = e.target.value;
                if (textInput) textInput.value = color;
                callback(color);
            });
        }

        if (textInput) {
            textInput.addEventListener('change', (e) => {
                const color = e.target.value;
                if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                    if (colorInput) colorInput.value = color;
                    callback(color);
                }
            });
        }
    }

    // ========================================
    // TEXT PROPERTIES (FIXED)
    // ========================================

    setupTextProperties() {
        // Text Content
        const textContent = document.getElementById('textContent');
        if (textContent) {
            textContent.addEventListener('input', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.text(e.target.value);
                    this.layer.batchDraw();
                }
            });

            textContent.addEventListener('change', () => {
                this.saveHistory();
                this.updateLayersList();
            });
        }

        // Font Family
        const fontFamily = document.getElementById('fontFamily');
        if (fontFamily) {
            fontFamily.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.fontFamily(e.target.value);
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Font Size
        const fontSize = document.getElementById('fontSize');
        if (fontSize) {
            fontSize.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.fontSize(parseInt(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Line Height
        const lineHeight = document.getElementById('lineHeight');
        if (lineHeight) {
            lineHeight.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.lineHeight(parseFloat(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // =====================
        // TEXT COLOR (FIXED!)
        // =====================

        const textColor = document.getElementById('textColor');
        const textColorText = document.getElementById('textColorText');

        if (textColor) {
            textColor.addEventListener('input', (e) => {
                console.log('Text color input:', e.target.value);
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.fill(e.target.value);
                    if (textColorText) textColorText.value = e.target.value;
                    this.layer.batchDraw();
                }
            });

            textColor.addEventListener('change', () => {
                this.saveHistory();
            });
        }

        if (textColorText) {
            textColorText.addEventListener('change', (e) => {
                const color = e.target.value;
                console.log('Text color text change:', color);
                if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                    if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                        this.selectedElement.fill(color);
                        if (textColor) textColor.value = color;
                        this.layer.batchDraw();
                        this.saveHistory();
                    }
                }
            });
        }

        // Text Opacity
        const textOpacity = document.getElementById('textOpacity');
        if (textOpacity) {
            textOpacity.addEventListener('input', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.opacity(parseFloat(e.target.value));
                    const display = document.getElementById('textOpacityValue');
                    if (display) display.textContent = Math.round(e.target.value * 100) + '%';
                    this.layer.batchDraw();
                }
            });

            textOpacity.addEventListener('change', () => {
                this.saveHistory();
            });
        }

        // =====================
        // FONT STYLE BUTTONS
        // =====================

        const boldBtn = document.getElementById('boldBtn');
        if (boldBtn) {
            boldBtn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    const current = this.selectedElement.fontStyle() || '';
                    const hasBold = current.includes('bold');
                    let newStyle = hasBold 
                        ? current.replace('bold', '').trim() 
                        : (current + ' bold').trim();
                    this.selectedElement.fontStyle(newStyle);
                    boldBtn.classList.toggle('active', !hasBold);
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        const italicBtn = document.getElementById('italicBtn');
        if (italicBtn) {
            italicBtn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    const current = this.selectedElement.fontStyle() || '';
                    const hasItalic = current.includes('italic');
                    let newStyle = hasItalic 
                        ? current.replace('italic', '').trim() 
                        : (current + ' italic').trim();
                    this.selectedElement.fontStyle(newStyle);
                    italicBtn.classList.toggle('active', !hasItalic);
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        const underlineBtn = document.getElementById('underlineBtn');
        if (underlineBtn) {
            underlineBtn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    const current = this.selectedElement.textDecoration() || '';
                    const hasUnderline = current.includes('underline');
                    let newDeco = hasUnderline 
                        ? current.replace('underline', '').trim() 
                        : (current + ' underline').trim();
                    this.selectedElement.textDecoration(newDeco);
                    underlineBtn.classList.toggle('active', !hasUnderline);
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        const strikeBtn = document.getElementById('strikeBtn');
        if (strikeBtn) {
            strikeBtn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    const current = this.selectedElement.textDecoration() || '';
                    const hasStrike = current.includes('line-through');
                    let newDeco = hasStrike 
                        ? current.replace('line-through', '').trim() 
                        : (current + ' line-through').trim();
                    this.selectedElement.textDecoration(newDeco);
                    strikeBtn.classList.toggle('active', !hasStrike);
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // =====================
        // TEXT ALIGN BUTTONS
        // =====================

        document.querySelectorAll('.align-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Text') {
                    this.selectedElement.align(btn.dataset.align);
                    document.querySelectorAll('.align-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        });
    }

    // ========================================
    // SHAPE PROPERTIES
    // ========================================

    setupShapeProperties() {
        // Fill Color
        const shapeFillColor = document.getElementById('shapeFillColor');
        const shapeFillColorText = document.getElementById('shapeFillColorText');

        if (shapeFillColor) {
            shapeFillColor.addEventListener('input', (e) => {
                if (this.selectedElement && this.selectedElement.name().includes('shape')) {
                    this.selectedElement.fill(e.target.value);
                    if (shapeFillColorText) shapeFillColorText.value = e.target.value;
                    this.layer.batchDraw();
                }
            });

            shapeFillColor.addEventListener('change', () => this.saveHistory());
        }

        if (shapeFillColorText) {
            shapeFillColorText.addEventListener('change', (e) => {
                const color = e.target.value;
                if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                    if (this.selectedElement && this.selectedElement.name().includes('shape')) {
                        this.selectedElement.fill(color);
                        if (shapeFillColor) shapeFillColor.value = color;
                        this.layer.batchDraw();
                        this.saveHistory();
                    }
                }
            });
        }

        // Clear Fill
        const clearShapeFill = document.getElementById('clearShapeFill');
        if (clearShapeFill) {
            clearShapeFill.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.name().includes('shape')) {
                    this.selectedElement.fill('transparent');
                    if (shapeFillColorText) shapeFillColorText.value = 'transparent';
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Stroke Color
        const shapeStrokeColor = document.getElementById('shapeStrokeColor');
        const shapeStrokeColorText = document.getElementById('shapeStrokeColorText');

        if (shapeStrokeColor) {
            shapeStrokeColor.addEventListener('input', (e) => {
                if (this.selectedElement && this.selectedElement.name().includes('shape')) {
                    this.selectedElement.stroke(e.target.value);
                    if (shapeStrokeColorText) shapeStrokeColorText.value = e.target.value;
                    this.layer.batchDraw();
                }
            });

            shapeStrokeColor.addEventListener('change', () => this.saveHistory());
        }

        // Stroke Width
        const shapeStrokeWidth = document.getElementById('shapeStrokeWidth');
        if (shapeStrokeWidth) {
            shapeStrokeWidth.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.name().includes('shape')) {
                    this.selectedElement.strokeWidth(parseInt(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Corner Radius
        const shapeCornerRadius = document.getElementById('shapeCornerRadius');
        if (shapeCornerRadius) {
            shapeCornerRadius.addEventListener('change', (e) => {
                if (this.selectedElement && typeof this.selectedElement.cornerRadius === 'function') {
                    this.selectedElement.cornerRadius(parseInt(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Shape Opacity
        const shapeOpacity = document.getElementById('shapeOpacity');
        if (shapeOpacity) {
            shapeOpacity.addEventListener('input', (e) => {
                if (this.selectedElement && this.selectedElement.name().includes('shape')) {
                    this.selectedElement.opacity(parseFloat(e.target.value));
                    const display = document.getElementById('shapeOpacityValue');
                    if (display) display.textContent = Math.round(e.target.value * 100) + '%';
                    this.layer.batchDraw();
                }
            });

            shapeOpacity.addEventListener('change', () => this.saveHistory());
        }

        // Star Points
        const starPoints = document.getElementById('starPoints');
        if (starPoints) {
            starPoints.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Star') {
                    this.selectedElement.numPoints(parseInt(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Polygon Sides
        const polygonSides = document.getElementById('polygonSides');
        if (polygonSides) {
            polygonSides.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'RegularPolygon') {
                    this.selectedElement.sides(parseInt(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }
    }

    // ========================================
    // IMAGE PROPERTIES
    // ========================================

    setupImageProperties() {
        const imageWidth = document.getElementById('imageWidth');
        const imageHeight = document.getElementById('imageHeight');
        const imageLockRatio = document.getElementById('imageLockRatio');

        if (imageWidth) {
            imageWidth.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Image') {
                    const newWidth = parseInt(e.target.value);
                    const currentWidth = this.selectedElement.width() * this.selectedElement.scaleX();
                    const scale = newWidth / this.selectedElement.width();
                    
                    this.selectedElement.scaleX(scale);
                    
                    if (imageLockRatio && imageLockRatio.checked) {
                        this.selectedElement.scaleY(scale);
                        if (imageHeight) {
                            imageHeight.value = Math.round(this.selectedElement.height() * scale);
                        }
                    }
                    
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        if (imageHeight) {
            imageHeight.addEventListener('change', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Image') {
                    const newHeight = parseInt(e.target.value);
                    const scale = newHeight / this.selectedElement.height();
                    
                    this.selectedElement.scaleY(scale);
                    
                    if (imageLockRatio && imageLockRatio.checked) {
                        this.selectedElement.scaleX(scale);
                        if (imageWidth) {
                            imageWidth.value = Math.round(this.selectedElement.width() * scale);
                        }
                    }
                    
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Image Opacity
        const imageOpacity = document.getElementById('imageOpacity');
        if (imageOpacity) {
            imageOpacity.addEventListener('input', (e) => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Image') {
                    this.selectedElement.opacity(parseFloat(e.target.value));
                    const display = document.getElementById('imageOpacityValue');
                    if (display) display.textContent = Math.round(e.target.value * 100) + '%';
                    this.layer.batchDraw();
                }
            });

            imageOpacity.addEventListener('change', () => this.saveHistory());
        }

        // Replace Image
        const replaceImageBtn = document.getElementById('replaceImageBtn');
        if (replaceImageBtn) {
            replaceImageBtn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Image') {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.onchange = (e) => {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                const imageObj = new Image();
                                imageObj.onload = () => {
                                    this.selectedElement.image(imageObj);
                                    this.layer.batchDraw();
                                    this.saveHistory();
                                };
                                imageObj.src = event.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    };
                    input.click();
                }
            });
        }

        // Image Filters
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (this.selectedElement && this.selectedElement.getClassName() === 'Image') {
                    this.applyImageFilter(btn.dataset.filter);
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                }
            });
        });
    }

    applyImageFilter(filter) {
        if (!this.selectedElement || this.selectedElement.getClassName() !== 'Image') return;

        try {
            this.selectedElement.cache();
            this.selectedElement.filters([]);

            switch (filter) {
                case 'grayscale':
                    this.selectedElement.filters([Konva.Filters.Grayscale]);
                    break;
                case 'blur':
                    this.selectedElement.filters([Konva.Filters.Blur]);
                    this.selectedElement.blurRadius(10);
                    break;
                case 'brighten':
                    this.selectedElement.filters([Konva.Filters.Brighten]);
                    this.selectedElement.brightness(0.3);
                    break;
                case 'contrast':
                    this.selectedElement.filters([Konva.Filters.Contrast]);
                    this.selectedElement.contrast(50);
                    break;
                case 'sepia':
                    this.selectedElement.filters([Konva.Filters.Sepia]);
                    break;
                case 'none':
                default:
                    this.selectedElement.clearCache();
                    break;
            }

            this.layer.batchDraw();
            this.saveHistory();
        } catch (e) {
            console.error('Filter error:', e);
        }
    }

    // ========================================
    // COMMON PROPERTIES
    // ========================================

    setupCommonProperties() {
        // Position X
        const elementX = document.getElementById('elementX');
        if (elementX) {
            elementX.addEventListener('change', (e) => {
                if (this.selectedElement) {
                    this.selectedElement.x(parseFloat(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Position Y
        const elementY = document.getElementById('elementY');
        if (elementY) {
            elementY.addEventListener('change', (e) => {
                if (this.selectedElement) {
                    this.selectedElement.y(parseFloat(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Width
        const elementWidth = document.getElementById('elementWidth');
        if (elementWidth) {
            elementWidth.addEventListener('change', (e) => {
                if (this.selectedElement) {
                    const newWidth = parseFloat(e.target.value);
                    const currentWidth = this.selectedElement.width();
                    if (currentWidth > 0) {
                        this.selectedElement.scaleX(newWidth / currentWidth);
                        this.layer.batchDraw();
                        this.saveHistory();
                    }
                }
            });
        }

        // Height
        const elementHeight = document.getElementById('elementHeight');
        if (elementHeight) {
            elementHeight.addEventListener('change', (e) => {
                if (this.selectedElement) {
                    const newHeight = parseFloat(e.target.value);
                    const currentHeight = this.selectedElement.height();
                    if (currentHeight > 0) {
                        this.selectedElement.scaleY(newHeight / currentHeight);
                        this.layer.batchDraw();
                        this.saveHistory();
                    }
                }
            });
        }

        // Rotation
        const elementRotation = document.getElementById('elementRotation');
        if (elementRotation) {
            elementRotation.addEventListener('change', (e) => {
                if (this.selectedElement) {
                    this.selectedElement.rotation(parseFloat(e.target.value));
                    this.layer.batchDraw();
                    this.saveHistory();
                }
            });
        }

        // Layer Order Buttons
        const bringToFront = document.getElementById('bringToFront');
        if (bringToFront) {
            bringToFront.addEventListener('click', () => {
                if (this.selectedElement) {
                    this.selectedElement.moveToTop();
                    this.transformer.moveToTop();
                    this.layer.batchDraw();
                    this.saveHistory();
                    this.updateLayersList();
                }
            });
        }

        const bringForward = document.getElementById('bringForward');
        if (bringForward) {
            bringForward.addEventListener('click', () => {
                if (this.selectedElement) {
                    this.selectedElement.moveUp();
                    this.transformer.moveToTop();
                    this.layer.batchDraw();
                    this.saveHistory();
                    this.updateLayersList();
                }
            });
        }

        const sendBackward = document.getElementById('sendBackward');
        if (sendBackward) {
            sendBackward.addEventListener('click', () => {
                if (this.selectedElement) {
                    this.selectedElement.moveDown();
                    if (this.backgroundImage) {
                        this.backgroundImage.moveToBottom();
                    }
                    this.backgroundRect.moveToBottom();
                    this.transformer.moveToTop();
                    this.layer.batchDraw();
                    this.saveHistory();
                    this.updateLayersList();
                }
            });
        }

        const sendToBack = document.getElementById('sendToBack');
        if (sendToBack) {
            sendToBack.addEventListener('click', () => {
                if (this.selectedElement) {
                    this.selectedElement.moveToBottom();
                    if (this.backgroundImage) {
                        this.backgroundImage.moveToBottom();
                    }
                    this.backgroundRect.moveToBottom();
                    this.transformer.moveToTop();
                    this.layer.batchDraw();
                    this.saveHistory();
                    this.updateLayersList();
                }
            });
        }

        // Duplicate
        const duplicateElement = document.getElementById('duplicateElement');
        if (duplicateElement) {
            duplicateElement.addEventListener('click', () => this.duplicateSelected());
        }

        // Delete
        const deleteElement = document.getElementById('deleteElement');
        if (deleteElement) {
            deleteElement.addEventListener('click', () => this.deleteSelected());
        }
    }

    // ========================================
    // IMAGE UPLOAD
    // ========================================

    setupImageUpload() {
        const uploadArea = document.getElementById('uploadArea');
        const imageUpload = document.getElementById('imageUpload');

        if (uploadArea && imageUpload) {
            uploadArea.addEventListener('click', () => imageUpload.click());

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = 'var(--primary-color)';
                uploadArea.style.background = 'rgba(99, 102, 241, 0.1)';
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.style.borderColor = '';
                uploadArea.style.background = '';
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = '';
                uploadArea.style.background = '';
                this.uploadImages(e.dataTransfer.files);
            });

            imageUpload.addEventListener('change', (e) => {
                this.uploadImages(e.target.files);
                e.target.value = ''; // Reset for same file selection
            });
        }

        // Click on uploaded images
        const imagesGrid = document.getElementById('imagesGrid');
        if (imagesGrid) {
            imagesGrid.addEventListener('click', (e) => {
                const imageItem = e.target.closest('.image-item');
                const deleteBtn = e.target.closest('.delete-image-btn');

                if (deleteBtn && imageItem) {
                    e.stopPropagation();
                    this.deleteUploadedImage(imageItem.dataset.id);
                    return;
                }

                if (imageItem) {
                    this.addImageToCanvas(imageItem.dataset.url);
                }
            });
        }
    }

    async uploadImages(files) {
        for (const file of files) {
            if (!file.type.startsWith('image/')) continue;

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('/editor/upload-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    this.addImageToGrid(data.image);
                    this.addImageToCanvas(data.image.url);
                    this.showToast('Image uploaded successfully!', 'success');
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.showToast('Failed to upload image', 'error');
            }
        }
    }

    addImageToGrid(image) {
        const grid = document.getElementById('imagesGrid');
        if (!grid) return;

        const div = document.createElement('div');
        div.className = 'image-item';
        div.dataset.id = image.id;
        div.dataset.url = image.url;
        div.innerHTML = `
            <img src="${image.url}" alt="${image.filename}">
            <button class="delete-image-btn" title="Delete">
                <i class="fas fa-times"></i>
            </button>
        `;
        grid.insertBefore(div, grid.firstChild);
    }

    async deleteUploadedImage(id) {
        if (!confirm('Delete this image?')) return;

        try {
            const response = await fetch(`/editor/image/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                const item = document.querySelector(`.image-item[data-id="${id}"]`);
                if (item) item.remove();
                this.showToast('Image deleted', 'success');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showToast('Failed to delete image', 'error');
        }
    }

    addImageToCanvas(url) {
        const imageObj = new Image();
        imageObj.crossOrigin = 'Anonymous';
        
        imageObj.onload = () => {
            // Scale image to fit canvas
            let width = imageObj.width;
            let height = imageObj.height;
            const maxSize = Math.min(this.stage.width(), this.stage.height()) * 0.6;

            if (width > maxSize || height > maxSize) {
                const ratio = Math.min(maxSize / width, maxSize / height);
                width *= ratio;
                height *= ratio;
            }

            const konvaImage = new Konva.Image({
                x: (this.stage.width() - width) / 2,
                y: (this.stage.height() - height) / 2,
                image: imageObj,
                width: width,
                height: height,
                draggable: true,
                id: this.generateId(),
                name: 'image'
            });

            this.layer.add(konvaImage);
            konvaImage.moveToTop();
            this.transformer.moveToTop();
            this.layer.batchDraw();

            this.selectElement(konvaImage);
            this.saveHistory();
            this.updateLayersList();
        };

        imageObj.onerror = () => {
            this.showToast('Failed to load image', 'error');
        };

        imageObj.src = url;
    }

    // ========================================
    // TOOL ACTIONS
    // ========================================

    handleToolAction(tool) {
        this.deselectAll();

        switch (tool) {
            case 'text':
                this.addText();
                break;
            case 'rectangle':
                this.addRectangle();
                break;
            case 'circle':
                this.addCircle();
                break;
            case 'line':
                this.addLine();
                break;
            case 'arrow':
                this.addArrow();
                break;
            case 'triangle':
                this.addTriangle();
                break;
            case 'star':
                this.addStar();
                break;
            case 'polygon':
                this.addPolygon();
                break;
            case 'image':
                const imageUpload = document.getElementById('imageUpload');
                if (imageUpload) imageUpload.click();
                break;
        }

        // Reset to select tool
        setTimeout(() => {
            const selectBtn = document.querySelector('.tool-btn[data-tool="select"]');
            if (selectBtn) selectBtn.click();
        }, 100);
    }

    // ========================================
    // ADD TEXT (FIXED - ALWAYS DRAGGABLE)
    // ========================================

    addText() {
        const text = new Konva.Text({
            x: this.stage.width() / 2 - 100,
            y: this.stage.height() / 2 - 20,
            text: 'Double click to edit',
            fontSize: 32,
            fontFamily: 'Arial',
            fill: '#000000',
            draggable: true,
            id: this.generateId(),
            name: 'text',
            align: 'left',
            lineHeight: 1.2,
            padding: 5
        });
    
        this.layer.add(text);
        
        // IMPORTANT: Move text above template, transformer on top
        text.moveToTop();
        this.transformer.moveToTop();
        
        this.layer.batchDraw();
    
        this.selectElement(text);
        this.saveHistory();
        this.updateLayersList();
    
        console.log('Text added:', text.id());
    }

    // Add this method to the class
    fixLayerOrder() {
        // Ensure correct z-index order
        this.backgroundRect.moveToBottom();
        if (this.backgroundImage) {
            this.backgroundImage.zIndex(1);
        }
        this.transformer.moveToTop();
        this.layer.batchDraw();
    }

    // ========================================
    // EDIT TEXT (FIXED - MAINTAINS DRAGGABLE)
    // ========================================

    editText(textNode) {
        // Set editing flag
        this.isEditing = true;

        // Get position
        const textPosition = textNode.absolutePosition();
        const stageBox = this.stage.container().getBoundingClientRect();
        const rotation = textNode.rotation();

        // Create textarea
        const textarea = document.createElement('textarea');
        document.body.appendChild(textarea);

        // Position textarea
        const areaPosition = {
            x: stageBox.left + textPosition.x * this.zoom,
            y: stageBox.top + textPosition.y * this.zoom
        };

        // Style textarea
        textarea.value = textNode.text();
        textarea.style.position = 'fixed';
        textarea.style.top = areaPosition.y + 'px';
        textarea.style.left = areaPosition.x + 'px';
        textarea.style.width = (textNode.width() * textNode.scaleX() * this.zoom) + 'px';
        textarea.style.height = 'auto';
        textarea.style.minHeight = (textNode.height() * textNode.scaleY() * this.zoom) + 'px';
        textarea.style.fontSize = (textNode.fontSize() * this.zoom) + 'px';
        textarea.style.lineHeight = textNode.lineHeight();
        textarea.style.fontFamily = textNode.fontFamily();
        textarea.style.textAlign = textNode.align();
        textarea.style.color = textNode.fill();
        textarea.style.fontWeight = textNode.fontStyle().includes('bold') ? 'bold' : 'normal';
        textarea.style.fontStyle = textNode.fontStyle().includes('italic') ? 'italic' : 'normal';
        textarea.style.border = '2px solid #6366f1';
        textarea.style.borderRadius = '4px';
        textarea.style.padding = '5px';
        textarea.style.margin = '0';
        textarea.style.overflow = 'hidden';
        textarea.style.background = 'rgba(255, 255, 255, 0.95)';
        textarea.style.outline = 'none';
        textarea.style.resize = 'none';
        textarea.style.transformOrigin = 'left top';
        textarea.style.transform = `rotate(${rotation}deg)`;
        textarea.style.zIndex = '10000';
        textarea.style.boxShadow = '0 4px 6px rgba(0,0,0,0.3)';

        // Hide transformer during editing
        this.transformer.hide();
        this.layer.batchDraw();

        // Focus and select text
        textarea.focus();
        textarea.select();

        // Auto-resize textarea
        const resizeTextarea = () => {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        };

        textarea.addEventListener('input', resizeTextarea);
        resizeTextarea();

        // Handle blur (finish editing)
        const finishEditing = () => {
            // Update text
            textNode.text(textarea.value);
            
            // IMPORTANT: Ensure draggable is still true
            textNode.draggable(true);
            
            // Remove textarea
            if (textarea.parentNode) {
                document.body.removeChild(textarea);
            }

            // Show transformer
            this.transformer.show();
            this.transformer.forceUpdate();
            this.layer.batchDraw();

            // Reset editing flag
            this.isEditing = false;

            // Update UI
            this.updatePropertiesPanel();
            this.saveHistory();
            this.updateLayersList();

            console.log('Text editing finished, draggable:', textNode.draggable());
        };

        textarea.addEventListener('blur', finishEditing);

        // Handle escape key
        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                textarea.blur();
            }
        });
    }

    // ========================================
    // ADD SHAPES
    // ========================================

    addRectangle() {
        const rect = new Konva.Rect({
            x: this.stage.width() / 2 - 75,
            y: this.stage.height() / 2 - 50,
            width: 150,
            height: 100,
            fill: '#3498db',
            stroke: '#2c3e50',
            strokeWidth: 2,
            cornerRadius: 0,
            draggable: true,
            id: this.generateId(),
            name: 'shape rectangle'
        });

        this.layer.add(rect);
        rect.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(rect);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    addCircle() {
        const circle = new Konva.Circle({
            x: this.stage.width() / 2,
            y: this.stage.height() / 2,
            radius: 60,
            fill: '#e74c3c',
            stroke: '#c0392b',
            strokeWidth: 2,
            draggable: true,
            id: this.generateId(),
            name: 'shape circle'
        });

        this.layer.add(circle);
        circle.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(circle);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    addLine() {
        const line = new Konva.Line({
            points: [
                this.stage.width() / 2 - 100, this.stage.height() / 2,
                this.stage.width() / 2 + 100, this.stage.height() / 2
            ],
            stroke: '#2c3e50',
            strokeWidth: 4,
            lineCap: 'round',
            lineJoin: 'round',
            draggable: true,
            id: this.generateId(),
            name: 'shape line'
        });

        this.layer.add(line);
        line.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(line);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    addArrow() {
        const arrow = new Konva.Arrow({
            points: [
                this.stage.width() / 2 - 100, this.stage.height() / 2,
                this.stage.width() / 2 + 100, this.stage.height() / 2
            ],
            pointerLength: 20,
            pointerWidth: 20,
            fill: '#2c3e50',
            stroke: '#2c3e50',
            strokeWidth: 4,
            draggable: true,
            id: this.generateId(),
            name: 'shape arrow'
        });

        this.layer.add(arrow);
        arrow.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(arrow);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    addTriangle() {
        const triangle = new Konva.RegularPolygon({
            x: this.stage.width() / 2,
            y: this.stage.height() / 2,
            sides: 3,
            radius: 60,
            fill: '#9b59b6',
            stroke: '#8e44ad',
            strokeWidth: 2,
            draggable: true,
            id: this.generateId(),
            name: 'shape triangle'
        });

        this.layer.add(triangle);
        triangle.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(triangle);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    addStar() {
        const star = new Konva.Star({
            x: this.stage.width() / 2,
            y: this.stage.height() / 2,
            numPoints: 5,
            innerRadius: 30,
            outerRadius: 60,
            fill: '#f1c40f',
            stroke: '#f39c12',
            strokeWidth: 2,
            draggable: true,
            id: this.generateId(),
            name: 'shape star'
        });

        this.layer.add(star);
        star.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(star);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    addPolygon() {
        const polygon = new Konva.RegularPolygon({
            x: this.stage.width() / 2,
            y: this.stage.height() / 2,
            sides: 6,
            radius: 60,
            fill: '#1abc9c',
            stroke: '#16a085',
            strokeWidth: 2,
            draggable: true,
            id: this.generateId(),
            name: 'shape polygon'
        });

        this.layer.add(polygon);
        polygon.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(polygon);
        this.saveHistory();
        this.updateLayersList();

        // ADD THIS LINE to all add methods
        this.fixLayerOrder();
    }

    // ========================================
    // SELECTION (FIXED)
    // ========================================

    selectElement(element) {
        // Don't select background, template image, or transformer
        if (!element || 
            element === this.backgroundRect || 
            element === this.backgroundImage ||
            element === this.transformer ||
            element.name() === 'templateBackground' ||
            element.name() === 'background' ||
            element.id() === 'template_bg') {
            return;
        }
    
        console.log('Selecting element:', element.getClassName(), element.id());
    
        this.selectedElement = element;
    
        // IMPORTANT: Ensure element is draggable
        element.draggable(true);
    
        // Attach transformer
        this.transformer.nodes([element]);
        
        // IMPORTANT: Force transformer update and move to top
        this.transformer.forceUpdate();
        this.transformer.moveToTop();
        
        this.layer.batchDraw();
    
        this.updatePropertiesPanel();
        this.updateLayersList();
    }

    deselectAll() {
        console.log('Deselecting all');
        
        this.selectedElement = null;
        this.transformer.nodes([]);
        this.layer.batchDraw();

        // Show canvas properties
        this.hideAllPropertyPanels();
        const canvasProps = document.getElementById('canvasProperties');
        if (canvasProps) canvasProps.style.display = 'block';

        this.updateLayersList();
    }

    // ========================================
    // PROPERTIES PANEL
    // ========================================

    hideAllPropertyPanels() {
        const panels = ['canvasProperties', 'textProperties', 'shapeProperties', 'imageProperties', 'commonProperties'];
        panels.forEach(id => {
            const panel = document.getElementById(id);
            if (panel) panel.style.display = 'none';
        });
    }

    updatePropertiesPanel() {
        this.hideAllPropertyPanels();

        if (!this.selectedElement) {
            const canvasProps = document.getElementById('canvasProperties');
            if (canvasProps) canvasProps.style.display = 'block';
            return;
        }

        // Show common properties
        const commonProps = document.getElementById('commonProperties');
        if (commonProps) commonProps.style.display = 'block';

        // Update common properties
        this.updateCommonProperties();

        const className = this.selectedElement.getClassName();
        const name = this.selectedElement.name() || '';

        if (className === 'Text') {
            const textProps = document.getElementById('textProperties');
            if (textProps) textProps.style.display = 'block';
            this.updateTextProperties();
        } else if (className === 'Image') {
            const imageProps = document.getElementById('imageProperties');
            if (imageProps) imageProps.style.display = 'block';
            this.updateImageProperties();
        } else if (name.includes('shape')) {
            const shapeProps = document.getElementById('shapeProperties');
            if (shapeProps) shapeProps.style.display = 'block';
            this.updateShapeProperties();
        }
    }

    updateCommonProperties() {
        const el = this.selectedElement;
        if (!el) return;

        const setVal = (id, value) => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        };

        setVal('elementX', Math.round(el.x()));
        setVal('elementY', Math.round(el.y()));
        setVal('elementWidth', Math.round(el.width() * el.scaleX()));
        setVal('elementHeight', Math.round(el.height() * el.scaleY()));
        setVal('elementRotation', Math.round(el.rotation()));
    }

    updateTextProperties() {
        const el = this.selectedElement;
        if (!el || el.getClassName() !== 'Text') return;

        const setVal = (id, value) => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        };

        setVal('textContent', el.text());
        setVal('fontFamily', el.fontFamily());
        setVal('fontSize', el.fontSize());
        setVal('lineHeight', el.lineHeight() || 1.2);
        setVal('textColor', el.fill());
        setVal('textColorText', el.fill());

        const opacityInput = document.getElementById('textOpacity');
        const opacityDisplay = document.getElementById('textOpacityValue');
        if (opacityInput) opacityInput.value = el.opacity();
        if (opacityDisplay) opacityDisplay.textContent = Math.round(el.opacity() * 100) + '%';

        // Font style buttons
        const fontStyle = el.fontStyle() || '';
        const textDecoration = el.textDecoration() || '';

        const toggleClass = (id, condition) => {
            const btn = document.getElementById(id);
            if (btn) btn.classList.toggle('active', condition);
        };

        toggleClass('boldBtn', fontStyle.includes('bold'));
        toggleClass('italicBtn', fontStyle.includes('italic'));
        toggleClass('underlineBtn', textDecoration.includes('underline'));
        toggleClass('strikeBtn', textDecoration.includes('line-through'));

        // Align buttons
        document.querySelectorAll('.align-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.align === el.align());
        });
    }

    updateShapeProperties() {
        const el = this.selectedElement;
        if (!el) return;

        const setVal = (id, value) => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        };

        const fill = el.fill() || '#ffffff';
        const stroke = el.stroke() || '#000000';

        setVal('shapeFillColor', fill === 'transparent' ? '#ffffff' : fill);
        setVal('shapeFillColorText', fill);
        setVal('shapeStrokeColor', stroke);
        setVal('shapeStrokeColorText', stroke);
        setVal('shapeStrokeWidth', el.strokeWidth() || 0);

        const opacityInput = document.getElementById('shapeOpacity');
        const opacityDisplay = document.getElementById('shapeOpacityValue');
        if (opacityInput) opacityInput.value = el.opacity();
        if (opacityDisplay) opacityDisplay.textContent = Math.round(el.opacity() * 100) + '%';

        // Corner radius
        if (typeof el.cornerRadius === 'function') {
            setVal('shapeCornerRadius', el.cornerRadius() || 0);
        }

        // Show/hide specific options
        const starPointsGroup = document.getElementById('starPointsGroup');
        const polygonSidesGroup = document.getElementById('polygonSidesGroup');
        const className = el.getClassName();
        const name = el.name() || '';

        if (starPointsGroup) starPointsGroup.style.display = className === 'Star' ? 'block' : 'none';
        if (polygonSidesGroup) polygonSidesGroup.style.display = 
            (name.includes('polygon') || name.includes('triangle')) ? 'block' : 'none';

        if (className === 'Star') {
            setVal('starPoints', el.numPoints());
        }
        if (className === 'RegularPolygon') {
            setVal('polygonSides', el.sides());
        }
    }

    updateImageProperties() {
        const el = this.selectedElement;
        if (!el || el.getClassName() !== 'Image') return;

        const setVal = (id, value) => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        };

        setVal('imageWidth', Math.round(el.width() * el.scaleX()));
        setVal('imageHeight', Math.round(el.height() * el.scaleY()));

        const opacityInput = document.getElementById('imageOpacity');
        const opacityDisplay = document.getElementById('imageOpacityValue');
        if (opacityInput) opacityInput.value = el.opacity();
        if (opacityDisplay) opacityDisplay.textContent = Math.round(el.opacity() * 100) + '%';
    }

    // ========================================
    // LAYERS
    // ========================================

    updateLayersList() {
        const layersList = document.getElementById('layersList');
        if (!layersList) return;

        layersList.innerHTML = '';

        const elements = this.layer.getChildren().filter(node => {
            return node !== this.backgroundRect && 
                   node !== this.backgroundImage &&
                   node !== this.transformer && 
                   node.name() !== 'background' &&
                   node.name() !== 'templateBackground' &&
                   node.id() !== 'template_bg';
        });

        // Reverse to show top layers first
        elements.slice().reverse().forEach(el => {
            const div = document.createElement('div');
            div.className = 'layer-item' + (el === this.selectedElement ? ' active' : '');
            div.dataset.id = el.id();

            let icon = 'fa-shapes';
            let name = 'Element';
            const className = el.getClassName();

            switch (className) {
                case 'Text':
                    icon = 'fa-font';
                    name = el.text().substring(0, 20) || 'Text';
                    break;
                case 'Image':
                    icon = 'fa-image';
                    name = 'Image';
                    break;
                case 'Rect':
                    icon = 'fa-square';
                    name = 'Rectangle';
                    break;
                case 'Circle':
                    icon = 'fa-circle';
                    name = 'Circle';
                    break;
                case 'Line':
                    icon = 'fa-minus';
                    name = 'Line';
                    break;
                case 'Arrow':
                    icon = 'fa-long-arrow-alt-right';
                    name = 'Arrow';
                    break;
                case 'Star':
                    icon = 'fa-star';
                    name = 'Star';
                    break;
                case 'RegularPolygon':
                    icon = 'fa-draw-polygon';
                    name = el.sides() === 3 ? 'Triangle' : 'Polygon';
                    break;
            }

            div.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${name}</span>
                <div class="layer-actions">
                    <button title="Visibility" class="toggle-visibility">
                        <i class="fas fa-eye${el.visible() ? '' : '-slash'}"></i>
                    </button>
                    <button title="Lock" class="toggle-lock ${el.draggable() ? '' : 'active'}">
                        <i class="fas fa-${el.draggable() ? 'unlock' : 'lock'}"></i>
                    </button>
                </div>
            `;

            // Visibility toggle
            const visBtn = div.querySelector('.toggle-visibility');
            if (visBtn) {
                visBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    el.visible(!el.visible());
                    this.layer.batchDraw();
                    this.updateLayersList();
                    this.saveHistory();
                });
            }

            // Lock toggle
            const lockBtn = div.querySelector('.toggle-lock');
            if (lockBtn) {
                lockBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    el.draggable(!el.draggable());
                    this.layer.batchDraw();
                    this.updateLayersList();
                });
            }

            layersList.appendChild(div);
        });
    }

    // ========================================
    // CONTEXT MENU
    // ========================================

    setupContextMenu() {
        // Remove existing context menu
        const existing = document.querySelector('.context-menu');
        if (existing) existing.remove();

        const contextMenu = document.createElement('div');
        contextMenu.className = 'context-menu';
        contextMenu.innerHTML = `
            <div class="context-menu-item" data-action="duplicate">
                <i class="fas fa-copy"></i><span>Duplicate</span>
            </div>
            <div class="context-menu-item" data-action="copy">
                <i class="fas fa-clipboard"></i><span>Copy</span>
            </div>
            <div class="context-menu-item" data-action="paste">
                <i class="fas fa-paste"></i><span>Paste</span>
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" data-action="bringToFront">
                <i class="fas fa-angle-double-up"></i><span>Bring to Front</span>
            </div>
            <div class="context-menu-item" data-action="sendToBack">
                <i class="fas fa-angle-double-down"></i><span>Send to Back</span>
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" data-action="delete">
                <i class="fas fa-trash"></i><span>Delete</span>
            </div>
        `;
        contextMenu.style.display = 'none';
        document.body.appendChild(contextMenu);

        // Right click on stage
        this.stage.on('contextmenu', (e) => {
            e.evt.preventDefault();

            if (e.target === this.stage || e.target === this.backgroundRect || e.target === this.backgroundImage) {
                contextMenu.style.display = 'none';
                return;
            }

            this.selectElement(e.target);

            contextMenu.style.display = 'block';
            contextMenu.style.left = e.evt.pageX + 'px';
            contextMenu.style.top = e.evt.pageY + 'px';
        });

        // Hide on click outside
        document.addEventListener('click', () => {
            contextMenu.style.display = 'none';
        });

        // Handle context menu actions
        contextMenu.addEventListener('click', (e) => {
            const item = e.target.closest('.context-menu-item');
            if (!item) return;

            const action = item.dataset.action;
            switch (action) {
                case 'duplicate':
                    this.duplicateSelected();
                    break;
                case 'copy':
                    this.copySelected();
                    break;
                case 'paste':
                    this.paste();
                    break;
                case 'bringToFront':
                    if (this.selectedElement) {
                        this.selectedElement.moveToTop();
                        this.transformer.moveToTop();
                        this.layer.batchDraw();
                        this.saveHistory();
                        this.updateLayersList();
                    }
                    break;
                case 'sendToBack':
                    if (this.selectedElement) {
                        this.selectedElement.moveToBottom();
                        if (this.backgroundImage) {
                            this.backgroundImage.moveToBottom();
                        }
                        this.backgroundRect.moveToBottom();
                        this.transformer.moveToTop();
                        this.layer.batchDraw();
                        this.saveHistory();
                        this.updateLayersList();
                    }
                    break;
                case 'delete':
                    this.deleteSelected();
                    break;
            }

            contextMenu.style.display = 'none';
        });
    }

    // ========================================
    // KEYBOARD SHORTCUTS
    // ========================================

    handleKeyboard(e) {
        // Don't handle if typing in input/textarea
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            return;
        }

        // Don't handle if editing text
        if (this.isEditing) {
            return;
        }

        // Ctrl/Cmd shortcuts
        if (e.ctrlKey || e.metaKey) {
            switch (e.key.toLowerCase()) {
                case 'z':
                    e.preventDefault();
                    if (e.shiftKey) {
                        this.redo();
                    } else {
                        this.undo();
                    }
                    break;
                case 'y':
                    e.preventDefault();
                    this.redo();
                    break;
                case 's':
                    e.preventDefault();
                    this.openSaveModal();
                    break;
                case 'c':
                    e.preventDefault();
                    this.copySelected();
                    break;
                case 'v':
                    e.preventDefault();
                    this.paste();
                    break;
                case 'd':
                    e.preventDefault();
                    this.duplicateSelected();
                    break;
                case 'a':
                    e.preventDefault();
                    this.selectAll();
                    break;
            }
            return;
        }

        // Single key shortcuts
        switch (e.key) {
            case 'Delete':
            case 'Backspace':
                if (this.selectedElement) {
                    e.preventDefault();
                    this.deleteSelected();
                }
                break;
            case 'Escape':
                this.deselectAll();
                break;
            case 'ArrowUp':
                if (this.selectedElement) {
                    e.preventDefault();
                    this.selectedElement.y(this.selectedElement.y() - (e.shiftKey ? 10 : 1));
                    this.layer.batchDraw();
                    this.updatePropertiesPanel();
                }
                break;
            case 'ArrowDown':
                if (this.selectedElement) {
                    e.preventDefault();
                    this.selectedElement.y(this.selectedElement.y() + (e.shiftKey ? 10 : 1));
                    this.layer.batchDraw();
                    this.updatePropertiesPanel();
                }
                break;
            case 'ArrowLeft':
                if (this.selectedElement) {
                    e.preventDefault();
                    this.selectedElement.x(this.selectedElement.x() - (e.shiftKey ? 10 : 1));
                    this.layer.batchDraw();
                    this.updatePropertiesPanel();
                }
                break;
            case 'ArrowRight':
                if (this.selectedElement) {
                    e.preventDefault();
                    this.selectedElement.x(this.selectedElement.x() + (e.shiftKey ? 10 : 1));
                    this.layer.batchDraw();
                    this.updatePropertiesPanel();
                }
                break;
        }
    }

    // ========================================
    // CLIPBOARD OPERATIONS
    // ========================================

    copySelected() {
        if (!this.selectedElement) return;
        this.clipboard = this.selectedElement.clone();
        this.showToast('Copied!', 'success');
    }

    paste() {
        if (!this.clipboard) {
            this.showToast('Nothing to paste', 'warning');
            return;
        }

        const clone = this.clipboard.clone({
            x: this.clipboard.x() + 20,
            y: this.clipboard.y() + 20,
            id: this.generateId()
        });

        clone.draggable(true);
        this.layer.add(clone);
        clone.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(clone);
        this.clipboard = clone;
        this.saveHistory();
        this.updateLayersList();
        this.showToast('Pasted!', 'success');
    }

    duplicateSelected() {
        if (!this.selectedElement) return;

        const clone = this.selectedElement.clone({
            x: this.selectedElement.x() + 20,
            y: this.selectedElement.y() + 20,
            id: this.generateId()
        });

        clone.draggable(true);
        this.layer.add(clone);
        clone.moveToTop();
        this.transformer.moveToTop();
        this.layer.batchDraw();

        this.selectElement(clone);
        this.saveHistory();
        this.updateLayersList();
        this.showToast('Duplicated!', 'success');
    }

    deleteSelected() {
        if (!this.selectedElement) return;

        this.selectedElement.destroy();
        this.transformer.nodes([]);
        this.selectedElement = null;
        this.layer.batchDraw();

        this.hideAllPropertyPanels();
        const canvasProps = document.getElementById('canvasProperties');
        if (canvasProps) canvasProps.style.display = 'block';

        this.saveHistory();
        this.updateLayersList();
        this.showToast('Deleted!', 'success');
    }

    selectAll() {
        const elements = this.layer.getChildren().filter(node => {
            return node !== this.backgroundRect && 
                   node !== this.backgroundImage &&
                   node !== this.transformer && 
                   node.name() !== 'background' &&
                   node.name() !== 'templateBackground';
        });

        if (elements.length > 0) {
            this.transformer.nodes(elements);
            this.layer.batchDraw();
        }
    }

    // ========================================
    // CANVAS OPERATIONS
    // ========================================

    resizeCanvas(width, height) {
        if (width < 100 || height < 100) {
            this.showToast('Minimum size is 100x100', 'warning');
            return;
        }

        this.stage.width(width);
        this.stage.height(height);
        this.backgroundRect.width(width);
        this.backgroundRect.height(height);

        // Also resize template image if exists
        if (this.backgroundImage) {
            this.backgroundImage.width(width);
            this.backgroundImage.height(height);
        }

        this.layer.batchDraw();
        this.saveHistory();
        this.fitToScreen();
        this.showToast(`Canvas resized to ${width}x${height}`, 'success');
    }

    setZoom(zoom) {
        zoom = Math.max(0.1, Math.min(3, zoom));
        this.zoom = zoom;

        const container = document.getElementById('konvaContainer');
        if (container) {
            container.style.transform = `scale(${zoom})`;
            container.style.transformOrigin = 'center center';
        }

        const zoomLevel = document.getElementById('zoomLevel');
        if (zoomLevel) zoomLevel.textContent = Math.round(zoom * 100) + '%';
    }

    fitToScreen() {
        const wrapper = document.querySelector('.canvas-area');
        if (!wrapper) return;
    
        const padding = 100; // Increased padding for large images
        const scaleX = (wrapper.clientWidth - padding) / this.stage.width();
        const scaleY = (wrapper.clientHeight - padding) / this.stage.height();
        const scale = Math.min(scaleX, scaleY, 1); // Don't zoom beyond 100%
    
        this.setZoom(scale);
    }

    // ========================================
    // HISTORY
    // ========================================

    saveHistory() {
        const json = this.stage.toJSON();

        if (this.historyIndex < this.history.length - 1) {
            this.history = this.history.slice(0, this.historyIndex + 1);
        }

        this.history.push(json);

        if (this.history.length > this.maxHistory) {
            this.history.shift();
        }

        this.historyIndex = this.history.length - 1;
        this.updateHistoryButtons();
    }

    undo() {
        if (this.historyIndex <= 0) {
            this.showToast('Nothing to undo', 'warning');
            return;
        }

        this.historyIndex--;
        this.loadFromHistory(this.history[this.historyIndex]);
        this.updateHistoryButtons();
        this.showToast('Undone', 'success');
    }

    redo() {
        if (this.historyIndex >= this.history.length - 1) {
            this.showToast('Nothing to redo', 'warning');
            return;
        }

        this.historyIndex++;
        this.loadFromHistory(this.history[this.historyIndex]);
        this.updateHistoryButtons();
        this.showToast('Redone', 'success');
    }

    loadFromHistory(json) {
        try {
            this.deselectAll();
        } catch (e) {
            console.error('Error loading from history:', e);
        }
    }

    updateHistoryButtons() {
        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');

        if (undoBtn) undoBtn.disabled = this.historyIndex <= 0;
        if (redoBtn) redoBtn.disabled = this.historyIndex >= this.history.length - 1;
    }

    // ========================================
    // SAVE & EXPORT
    // ========================================

    openSaveModal() {
        const nameInput = document.getElementById('saveDesignName');
        const designName = document.getElementById('designName');
        if (nameInput && designName) {
            nameInput.value = designName.value;
        }
        
        const modal = document.getElementById('saveModal');
        if (modal) modal.classList.add('active');
    }

    async save() {
        const nameInput = document.getElementById('saveDesignName');
        const designNameInput = document.getElementById('designName');
        const name = nameInput ? nameInput.value : 'Untitled Design';
        
        if (designNameInput) designNameInput.value = name;

        // Hide transformer for thumbnail
        this.transformer.nodes([]);
        this.layer.batchDraw();

        const thumbnail = this.stage.toDataURL({ pixelRatio: 0.3 });
        const designData = this.getDesignData();

        try {
            const response = await fetch('/editor/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    design_id: this.designId,
                    name: name,
                    design_data: designData,
                    width: this.stage.width(),
                    height: this.stage.height(),
                    thumbnail: thumbnail,
                    template_id: this.currentTemplateId
                })
            });

            const data = await response.json();
            if (data.success) {
                this.designId = data.design_id;
                window.designId = data.design_id;
                closeModal('saveModal');
                this.showToast('Design saved successfully!', 'success');
                window.history.replaceState({}, '', `/editor/${data.design_id}`);
            } else {
                this.showToast('Failed to save design', 'error');
            }
        } catch (error) {
            console.error('Save error:', error);
            this.showToast('Failed to save design', 'error');
        }

        // Restore selection
        if (this.selectedElement) {
            this.transformer.nodes([this.selectedElement]);
            this.layer.batchDraw();
        }
    }

    getDesignData() {
        const elements = [];

        this.layer.getChildren().forEach(node => {
            if (node === this.transformer) return;

            const data = node.toObject();

            // Handle images
            if (node.getClassName() === 'Image' && node.image()) {
                data.attrs.imageUrl = node.image().src;
            }

            // Mark template background
            if (node === this.backgroundImage || node.id() === 'template_bg') {
                data.attrs.isTemplateBackground = true;
            }

            elements.push(data);
        });

        return {
            elements: elements,
            backgroundColor: this.backgroundRect.fill(),
            width: this.stage.width(),
            height: this.stage.height(),
            templateId: this.currentTemplateId
        };
    }

    loadDesignData() {
        if (!window.designData) return;

        const data = window.designData;

        if (data.width && data.height) {
            this.stage.width(data.width);
            this.stage.height(data.height);
            this.backgroundRect.width(data.width);
            this.backgroundRect.height(data.height);
        }

        if (data.backgroundColor) {
            this.backgroundRect.fill(data.backgroundColor);
            const bgColor = document.getElementById('canvasBgColor');
            const bgColorText = document.getElementById('canvasBgColorText');
            if (bgColor) bgColor.value = data.backgroundColor;
            if (bgColorText) bgColorText.value = data.backgroundColor;
        }

        // Store template ID
        if (data.templateId) {
            this.currentTemplateId = data.templateId;
        }

        if (data.elements) {
            data.elements.forEach(elementData => {
                if (elementData.attrs.name === 'background') return;

                // Handle template background
                if (elementData.attrs.isTemplateBackground && elementData.attrs.imageUrl) {
                    const imageObj = new Image();
                    imageObj.crossOrigin = 'Anonymous';
                    imageObj.onload = () => {
                        this.backgroundImage = new Konva.Image({
                            ...elementData.attrs,
                            image: imageObj,
                            listening: false,
                            id: 'template_bg',
                            name: 'templateBackground'
                        });
                        this.layer.add(this.backgroundImage);
                        this.backgroundImage.moveToBottom();
                        this.backgroundRect.moveToBottom();
                        this.transformer.moveToTop();
                        this.layer.batchDraw();
                    };
                    imageObj.src = elementData.attrs.imageUrl;
                    return;
                }

                // Handle regular images
                if (elementData.className === 'Image' && elementData.attrs.imageUrl) {
                    const imageObj = new Image();
                    imageObj.crossOrigin = 'Anonymous';
                    imageObj.onload = () => {
                        const konvaImage = new Konva.Image({
                            ...elementData.attrs,
                            image: imageObj,
                            draggable: true
                        });
                        this.layer.add(konvaImage);
                        this.transformer.moveToTop();
                        this.layer.batchDraw();
                        this.updateLayersList();
                    };
                    imageObj.src = elementData.attrs.imageUrl;
                } else {
                    const node = Konva.Node.create(elementData);
                    node.draggable(true);
                    this.layer.add(node);
                }
            });
        }

        this.transformer.moveToTop();
        this.layer.batchDraw();
        this.updateLayersList();
    }

    openExportModal() {
        const filenameInput = document.getElementById('exportFilename');
        const designName = document.getElementById('designName');
        if (filenameInput && designName) {
            filenameInput.value = designName.value || 'my-design';
        }
        
        const modal = document.getElementById('exportModal');
        if (modal) modal.classList.add('active');
    }

    export() {
        const filenameInput = document.getElementById('exportFilename');
        const formatSelect = document.getElementById('exportFormat');
        const qualityInput = document.getElementById('exportQuality');
        const scaleSelect = document.getElementById('exportScale');

        const filename = filenameInput ? filenameInput.value : 'design';
        const format = formatSelect ? formatSelect.value : 'png';
        const quality = qualityInput ? parseFloat(qualityInput.value) : 0.9;
        const scale = scaleSelect ? parseFloat(scaleSelect.value) : 1;

        // Hide transformer
        this.transformer.nodes([]);
        this.layer.batchDraw();

        const dataURL = this.stage.toDataURL({
            pixelRatio: scale,
            mimeType: format === 'jpg' ? 'image/jpeg' : 'image/png',
            quality: format === 'jpg' ? quality : 1
        });

        const link = document.createElement('a');
        link.download = `${filename}.${format}`;
        link.href = dataURL;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        closeModal('exportModal');
        this.showToast('Design exported successfully!', 'success');

        if (this.selectedElement) {
            this.transformer.nodes([this.selectedElement]);
            this.layer.batchDraw();
        }
    }

    showPreview() {
        this.transformer.nodes([]);
        this.layer.batchDraw();

        const dataURL = this.stage.toDataURL({ pixelRatio: 1 });

        const previewContainer = document.getElementById('previewContainer');
        if (previewContainer) {
            previewContainer.innerHTML = `<img src="${dataURL}" alt="Preview" style="max-width: 100%; max-height: 70vh; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">`;
        }

        const modal = document.getElementById('previewModal');
        if (modal) modal.classList.add('active');

        if (this.selectedElement) {
            this.transformer.nodes([this.selectedElement]);
            this.layer.batchDraw();
        }
    }

    // ========================================
    // UTILITIES
    // ========================================

    generateId() {
        return 'el_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        let icon = 'fa-check-circle';
        if (type === 'error') icon = 'fa-times-circle';
        if (type === 'warning') icon = 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// ========================================
// MODAL FUNCTIONS
// ========================================

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('active');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('active');
}

// Close modals on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});

// Close modals on backdrop click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});

// ========================================
// INITIALIZE EDITOR
// ========================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Loaded - Initializing Konva Editor');
    window.editor = new KonvaEditor();
});