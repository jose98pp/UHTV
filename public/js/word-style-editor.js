/**
 * Microsoft Word Style Rich Text Editor
 * Implements a ribbon interface with tabs and tool groups
 */

const WordStyleEditor = ({ initialContent = '', onChange, onAutoSave, onBusyChange, editorId = 'word-editor' }) => {
    const [content, setContent] = React.useState(initialContent);
    const [activeTab, setActiveTab] = React.useState('home');
    const [showColorPicker, setShowColorPicker] = React.useState(false);
    const [showFontColorPicker, setShowFontColorPicker] = React.useState(false);
    const [undoStack, setUndoStack] = React.useState([initialContent]);
    const [undoIndex, setUndoIndex] = React.useState(0);
    const [isUploading, setIsUploading] = React.useState(false);
    const [isDraggingImage, setIsDraggingImage] = React.useState(false);
    const [uploadError, setUploadError] = React.useState('');
    const [validationError, setValidationError] = React.useState('');
    const editorRef = React.useRef(null);
    const imageInputRef = React.useRef(null);
    const savedRangeRef = React.useRef(null);
    const selectedImageFrameRef = React.useRef(null);
    const imageInteractionCleanupRef = React.useRef(null);
    const dragDepthRef = React.useRef(0);
    const imageSequenceRef = React.useRef(0);
    const autoSaveTimerRef = React.useRef(null);
    const imageInputId = `${editorId}-image-input`;
    const editorElementId = `${editorId}-content`;

    // Colores predefinidos para el picker
    const colors = [
        '#000000', '#333333', '#666666', '#999999', '#cccccc', '#ffffff',
        '#ff0000', '#ff6600', '#ffcc00', '#00ff00', '#0066ff', '#6600ff',
        '#800000', '#ff3300', '#ff9900', '#99cc00', '#3366ff', '#800080',
        '#660000', '#cc3300', '#ff6600', '#66cc00', '#0033cc', '#663399'
    ];

    // Fuentes disponibles
    const fonts = [
        'Arial', 'Times New Roman', 'Helvetica', 'Georgia', 'Verdana', 
        'Tahoma', 'Trebuchet MS', 'Impact', 'Comic Sans MS', 'Courier New'
    ];

    // Tamaños de fuente
    const fontSizes = ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '32', '36', '48', '72'];

    // Inicialización del editor
    React.useEffect(() => {
        if (editorRef.current) {
            editorRef.current.innerHTML = initialContent;
            requestAnimationFrame(() => {
                decorateEditorImages();
                if (editorRef.current && onChange) onChange(getCleanEditorHtml(editorRef.current.innerHTML));
            });
        }
    }, [initialContent]);

    React.useEffect(() => {
        if (editorRef.current) {
            editorRef.current.dataset.uploading = isUploading ? 'true' : 'false';
            editorRef.current.setAttribute('aria-busy', isUploading ? 'true' : 'false');
        }
        if (onBusyChange) onBusyChange(isUploading);
    }, [isUploading, onBusyChange]);

    React.useEffect(() => () => {
        if (imageInteractionCleanupRef.current) {
            imageInteractionCleanupRef.current();
        }
    }, []);

    // Función para el guardado automático
    React.useEffect(() => {
        if (autoSaveTimerRef.current) {
            clearTimeout(autoSaveTimerRef.current);
        }

        autoSaveTimerRef.current = setTimeout(() => {
            if (onAutoSave) {
                onAutoSave(content);
            }
        }, 3000);

        return () => {
            if (autoSaveTimerRef.current) {
                clearTimeout(autoSaveTimerRef.current);
            }
        };
    }, [content, onAutoSave]);

    // Funciones de deshacer/rehacer
    const addToUndoStack = (newContent) => {
        if (undoStack[undoIndex] === newContent) return;
        const newStack = undoStack.slice(0, undoIndex + 1);
        newStack.push(newContent);
        setUndoStack(newStack);
        setUndoIndex(newStack.length - 1);
    };

    const getCleanEditorHtml = (fallback = '') => {
        if (!editorRef.current) return fallback;
        const cleanEditor = editorRef.current.cloneNode(true);
        cleanEditor.querySelectorAll('.editor-image-move-handle, .editor-image-resize-handle, .editor-image-delete-handle')
            .forEach(handle => handle.remove());
        cleanEditor.querySelectorAll('.editor-image-frame').forEach(frame => {
            frame.removeAttribute('id');
            frame.removeAttribute('contenteditable');
            frame.removeAttribute('role');
            frame.removeAttribute('tabindex');
            frame.removeAttribute('aria-label');
            frame.removeAttribute('aria-selected');
            frame.removeAttribute('style');
            frame.removeAttribute('data-editor-image-bound');
        });
        cleanEditor.querySelectorAll('img').forEach(image => {
            image.removeAttribute('loading');
            image.removeAttribute('draggable');
            image.removeAttribute('title');
            image.removeAttribute('style');
        });
        return cleanEditor.innerHTML;
    };

    const commitContent = (newContent, recordUndo = true) => {
        const cleanContent = getCleanEditorHtml(newContent);
        setContent(cleanContent);
        if (recordUndo) addToUndoStack(cleanContent);
        if (onChange) onChange(cleanContent);
    };

    const undo = () => {
        if (undoIndex > 0 && editorRef.current) {
            const previousContent = undoStack[undoIndex - 1];
            setUndoIndex(undoIndex - 1);
            editorRef.current.innerHTML = previousContent;
            setContent(previousContent);
            if (onChange) onChange(previousContent);
            requestAnimationFrame(decorateEditorImages);
        }
    };

    const redo = () => {
        if (undoIndex < undoStack.length - 1 && editorRef.current) {
            const nextContent = undoStack[undoIndex + 1];
            setUndoIndex(undoIndex + 1);
            editorRef.current.innerHTML = nextContent;
            setContent(nextContent);
            if (onChange) onChange(nextContent);
            requestAnimationFrame(decorateEditorImages);
        }
    };

    // Remember the caret while the user interacts with the ribbon or file picker.
    const rememberSelection = () => {
        const editor = editorRef.current;
        const selection = window.getSelection();
        if (!editor || !selection || selection.rangeCount === 0) return;

        const range = selection.getRangeAt(0);
        if (editor.contains(range.commonAncestorContainer)) {
            savedRangeRef.current = range.cloneRange();
        }
    };

    const restoreSelection = () => {
        const editor = editorRef.current;
        const selection = window.getSelection();
        if (!editor || !selection) return false;

        if (savedRangeRef.current && editor.contains(savedRangeRef.current.commonAncestorContainer)) {
            selection.removeAllRanges();
            selection.addRange(savedRangeRef.current);
            editor.focus();
            return true;
        }

        editor.focus();
        return false;
    };

    const getRangeFromPoint = (x, y) => {
        const editor = editorRef.current;
        if (!editor) return null;

        let range = null;
        if (document.caretRangeFromPoint) {
            range = document.caretRangeFromPoint(x, y);
        } else if (document.caretPositionFromPoint) {
            const position = document.caretPositionFromPoint(x, y);
            if (position) {
                range = document.createRange();
                range.setStart(position.offsetNode, position.offset);
                range.collapse(true);
            }
        }

        return range && editor.contains(range.commonAncestorContainer) ? range : null;
    };

    // Función para ejecutar comandos de formato
    const handleCommand = (command, value = null) => {
        restoreSelection();
        document.execCommand(command, false, value);
        rememberSelection();
        if (editorRef.current) commitContent(editorRef.current.innerHTML);
    };

    // Imágenes: selección, arrastrar/soltar y pegado desde el portapapeles.
    const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    const getImageFiles = (fileList) => Array.from(fileList || []).filter(file => {
        if (allowedImageTypes.includes(file.type)) return true;
        return /\.(jpe?g|png|gif|webp)$/i.test(file.name || '');
    });

    const getClipboardImageFiles = (clipboardData) => {
        const directFiles = getImageFiles(clipboardData?.files);
        if (directFiles.length > 0) return directFiles;

        return Array.from(clipboardData?.items || [])
            .filter(item => item.kind === 'file' && item.type.startsWith('image/'))
            .map(item => item.getAsFile())
            .filter(Boolean);
    };

    const validateImageFile = (file) => {
        if (file.size > 5 * 1024 * 1024) {
            throw new Error(`“${file.name}” supera el máximo de 5 MB.`);
        }
        if (!allowedImageTypes.includes(file.type) && !/\.(jpe?g|png|gif|webp)$/i.test(file.name || '')) {
            throw new Error(`“${file.name}” no es un formato de imagen permitido.`);
        }
    };

    const getSafeImageUrl = (value) => {
        try {
            const parsedUrl = new URL(value, window.location.origin);
            if (!['http:', 'https:'].includes(parsedUrl.protocol)) return '';
            return parsedUrl.href;
        } catch (error) {
            return '';
        }
    };

    const clearImageSelection = () => {
        if (selectedImageFrameRef.current) {
            selectedImageFrameRef.current.classList.remove('is-selected');
            selectedImageFrameRef.current.removeAttribute('aria-selected');
        }
        selectedImageFrameRef.current = null;
    };

    const selectImageFrame = (frame) => {
        if (!frame) return;
        if (selectedImageFrameRef.current && selectedImageFrameRef.current !== frame) {
            selectedImageFrameRef.current.classList.remove('is-selected');
            selectedImageFrameRef.current.removeAttribute('aria-selected');
        }
        frame.classList.add('is-selected');
        frame.setAttribute('aria-selected', 'true');
        selectedImageFrameRef.current = frame;
    };

    const getImageAspectRatio = (image) => {
        if (image.naturalWidth > 0 && image.naturalHeight > 0) {
            return image.naturalWidth / image.naturalHeight;
        }
        const rect = image.getBoundingClientRect();
        return rect.height > 0 ? rect.width / rect.height : 16 / 9;
    };

    const applyImageDimensions = (frame, image, width) => {
        const editor = editorRef.current;
        const ratio = getImageAspectRatio(image);
        const maxWidth = Math.max(240, (editor?.clientWidth || 800) - 32);
        const nextWidth = Math.round(Math.min(Math.max(width, 96), maxWidth));
        const nextHeight = Math.round(nextWidth / ratio);

        image.style.width = `${nextWidth}px`;
        image.style.height = `${nextHeight}px`;
        image.setAttribute('width', String(nextWidth));
        image.setAttribute('height', String(nextHeight));
        frame.style.width = `${nextWidth}px`;
        frame.style.maxWidth = '100%';
    };

    const startImageResize = (event, frame, image) => {
        event.preventDefault();
        event.stopPropagation();
        selectImageFrame(frame);
        if (imageInteractionCleanupRef.current) imageInteractionCleanupRef.current();

        const startX = event.clientX;
        const startWidth = image.getBoundingClientRect().width;
        let finished = false;
        document.body.classList.add('is-resizing-editor-image');

        const finish = (commit = true) => {
            if (finished) return;
            finished = true;
            window.removeEventListener('pointermove', handleMove);
            window.removeEventListener('pointerup', finish);
            window.removeEventListener('pointercancel', cancel);
            document.body.classList.remove('is-resizing-editor-image');
            if (imageInteractionCleanupRef.current === cleanup) imageInteractionCleanupRef.current = null;
            if (commit && editorRef.current) commitContent(editorRef.current.innerHTML);
        };

        const cancel = () => finish(false);
        const cleanup = () => finish(false);

        const handleMove = (moveEvent) => {
            applyImageDimensions(frame, image, startWidth + moveEvent.clientX - startX);
        };

        window.addEventListener('pointermove', handleMove);
        window.addEventListener('pointerup', finish, { once: true });
        window.addEventListener('pointercancel', cancel, { once: true });
        imageInteractionCleanupRef.current = cleanup;
    };

    const getMoveTargetRange = (frame, x, y) => {
        let range = getRangeFromPoint(x, y);
        if (!range) return null;

        if (frame.contains(range.commonAncestorContainer)) {
            const frameRect = frame.getBoundingClientRect();
            range = document.createRange();
            range.selectNode(frame);
            range.collapse(y > frameRect.top + frameRect.height / 2);
        }

        return range;
    };

    const startImageMove = (event, frame) => {
        event.preventDefault();
        event.stopPropagation();
        selectImageFrame(frame);
        if (imageInteractionCleanupRef.current) imageInteractionCleanupRef.current();

        const frameRect = frame.getBoundingClientRect();
        const ghost = frame.cloneNode(true);
        const marker = document.createElement('div');
        let targetRange = null;
        let finished = false;

        ghost.classList.add('editor-image-move-ghost');
        ghost.removeAttribute('id');
        ghost.setAttribute('aria-hidden', 'true');
        ghost.querySelectorAll('button').forEach(button => button.remove());
        ghost.style.width = `${frameRect.width}px`;
        ghost.style.height = `${frameRect.height}px`;
        ghost.style.left = `${frameRect.left}px`;
        ghost.style.top = `${frameRect.top}px`;

        marker.className = 'editor-image-drop-marker';
        marker.setAttribute('aria-hidden', 'true');
        document.body.appendChild(ghost);
        document.body.appendChild(marker);
        frame.classList.add('is-moving');
        document.body.classList.add('is-moving-editor-image');

        const positionGhost = (pointerEvent) => {
            ghost.style.left = `${pointerEvent.clientX - frameRect.width / 2}px`;
            ghost.style.top = `${pointerEvent.clientY - 24}px`;
        };

        const updateTarget = (pointerEvent) => {
            positionGhost(pointerEvent);
            const range = getMoveTargetRange(frame, pointerEvent.clientX, pointerEvent.clientY);
            targetRange = range;
            if (!range) {
                marker.style.display = 'none';
                return;
            }

            const rangeRect = range.getBoundingClientRect();
            marker.style.display = 'block';
            marker.style.left = `${Math.max(4, rangeRect.left - 2)}px`;
            marker.style.top = `${rangeRect.top}px`;
            marker.style.height = `${Math.max(20, rangeRect.height)}px`;
        };

        const finish = (commit = true) => {
            if (finished) return;
            finished = true;
            window.removeEventListener('pointermove', updateTarget);
            window.removeEventListener('pointerup', handlePointerUp);
            window.removeEventListener('pointercancel', cancel);
            ghost.remove();
            marker.remove();
            frame.classList.remove('is-moving');
            document.body.classList.remove('is-moving-editor-image');
            if (imageInteractionCleanupRef.current === cleanup) imageInteractionCleanupRef.current = null;

            if (commit && targetRange && editorRef.current) {
                targetRange.insertNode(frame);
                if (!frame.nextSibling && frame.parentElement === editorRef.current) {
                    frame.after(document.createElement('br'));
                }
                commitContent(editorRef.current.innerHTML);
                selectImageFrame(frame);
            }
        };

        const handlePointerUp = () => finish(true);
        const cancel = () => finish(false);
        const cleanup = () => finish(false);

        window.addEventListener('pointermove', updateTarget);
        window.addEventListener('pointerup', handlePointerUp, { once: true });
        window.addEventListener('pointercancel', cancel, { once: true });
        imageInteractionCleanupRef.current = cleanup;
        updateTarget(event);
    };

    const createEditableImageFrame = (image) => {
        let frame = image.closest('.editor-image-frame');
        if (!frame) {
            frame = document.createElement('span');
            image.before(frame);
            frame.appendChild(image);
        }

        if (frame.dataset.editorImageBound === 'true'
            && frame.querySelector('.editor-image-move-handle')
            && frame.querySelector('.editor-image-resize-handle')) {
            image.classList.add('editor-image');
            image.draggable = false;
            return frame;
        }

        imageSequenceRef.current += 1;
        frame.id = frame.id || `${editorId}-image-${imageSequenceRef.current}`;
        frame.className = 'editor-image-frame';
        frame.contentEditable = 'false';
        frame.setAttribute('role', 'group');
        frame.tabIndex = 0;
        frame.setAttribute('aria-label', `Imagen editable: ${image.alt || 'imagen'}`);
        frame.querySelectorAll('.editor-image-move-handle, .editor-image-resize-handle, .editor-image-delete-handle')
            .forEach(handle => handle.remove());

        image.classList.add('editor-image');
        image.draggable = false;
        image.title = 'Seleccionar para mover o redimensionar';

        const moveHandle = document.createElement('button');
        moveHandle.type = 'button';
        moveHandle.className = 'editor-image-move-handle';
        moveHandle.title = 'Arrastrar para mover la imagen';
        moveHandle.setAttribute('aria-label', 'Mover imagen');
        moveHandle.textContent = '↕ Mover';

        const resizeHandle = document.createElement('button');
        resizeHandle.type = 'button';
        resizeHandle.className = 'editor-image-resize-handle';
        resizeHandle.title = 'Arrastrar para redimensionar';
        resizeHandle.setAttribute('aria-label', 'Redimensionar imagen');
        resizeHandle.textContent = '↘';

        const deleteHandle = document.createElement('button');
        deleteHandle.type = 'button';
        deleteHandle.className = 'editor-image-delete-handle';
        deleteHandle.title = 'Eliminar imagen';
        deleteHandle.setAttribute('aria-label', 'Eliminar imagen');
        deleteHandle.textContent = '×';

        moveHandle.addEventListener('pointerdown', (moveEvent) => startImageMove(moveEvent, frame));
        resizeHandle.addEventListener('pointerdown', (resizeEvent) => startImageResize(resizeEvent, frame, image));
        resizeHandle.addEventListener('keydown', (keyEvent) => {
            if (!['ArrowLeft', 'ArrowRight'].includes(keyEvent.key)) return;
            keyEvent.preventDefault();
            const direction = keyEvent.key === 'ArrowRight' ? 10 : -10;
            applyImageDimensions(frame, image, image.getBoundingClientRect().width + direction);
            commitContent(editorRef.current.innerHTML);
        });
        deleteHandle.addEventListener('click', () => {
            if (imageInteractionCleanupRef.current) imageInteractionCleanupRef.current();
            frame.remove();
            clearImageSelection();
            if (editorRef.current) commitContent(editorRef.current.innerHTML);
        });

        frame.addEventListener('click', (clickEvent) => {
            if (!clickEvent.target.closest('button')) selectImageFrame(frame);
        });
        frame.addEventListener('focus', () => selectImageFrame(frame));
        frame.addEventListener('keydown', (keyEvent) => {
            if (keyEvent.key === 'Enter' || keyEvent.key === ' ') {
                keyEvent.preventDefault();
                selectImageFrame(frame);
            }
            if (keyEvent.key === 'Delete' || keyEvent.key === 'Backspace') {
                keyEvent.preventDefault();
                frame.remove();
                clearImageSelection();
                if (editorRef.current) commitContent(editorRef.current.innerHTML);
            }
        });
        frame.appendChild(moveHandle);
        frame.appendChild(resizeHandle);
        frame.appendChild(deleteHandle);
        frame.dataset.editorImageBound = 'true';
        return frame;
    };

    const decorateEditorImages = () => {
        const editor = editorRef.current;
        if (!editor) return;
        editor.querySelectorAll('img').forEach(image => createEditableImageFrame(image));
    };

    const insertUploadedImage = (value, altText = 'Imagen insertada') => {
        const imageUrl = getSafeImageUrl(value);
        const editor = editorRef.current;
        const selection = window.getSelection();
        if (!imageUrl || !editor || !selection) {
            throw new Error('No se pudo validar la URL de la imagen.');
        }

        restoreSelection();
        let insertionRange = savedRangeRef.current
            ? savedRangeRef.current.cloneRange()
            : document.createRange();

        if (!savedRangeRef.current || !editor.contains(insertionRange.commonAncestorContainer)) {
            insertionRange.selectNodeContents(editor);
            insertionRange.collapse(false);
        }

        insertionRange.deleteContents();
        const image = document.createElement('img');
        image.src = imageUrl;
        image.alt = altText;
        image.className = 'editor-image';
        image.loading = 'lazy';
        const frame = createEditableImageFrame(image);
        insertionRange.insertNode(frame);

        const lineBreak = document.createElement('br');
        frame.after(lineBreak);
        insertionRange.setStartAfter(lineBreak);
        insertionRange.collapse(true);
        selection.removeAllRanges();
        selection.addRange(insertionRange);
        savedRangeRef.current = insertionRange.cloneRange();

        commitContent(editor.innerHTML);
        selectImageFrame(frame);
        frame.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const uploadImages = async (fileList, insertionRange = null) => {
        const files = Array.from(fileList || []);
        if (files.length === 0) return;

        if (insertionRange) savedRangeRef.current = insertionRange.cloneRange();
        rememberSelection();
        setUploadError('');
        setIsUploading(true);
        const errors = [];

        try {
            for (const file of files) {
                try {
                    validateImageFile(file);
                    const formData = new FormData();
                    formData.append('image', file);

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    const uploadUrl = document.querySelector('meta[name="image-upload-url"]')?.content
                        || '/admin/noticias/images';

                    if (!csrfToken) {
                        throw new Error('No se encontró el token de seguridad. Recarga la página e inicia sesión nuevamente.');
                    }

                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const contentType = response.headers.get('content-type') || '';
                    if (response.redirected || !contentType.includes('application/json')) {
                        throw new Error('El servidor devolvió una respuesta inesperada. Vuelve a iniciar sesión e inténtalo nuevamente.');
                    }

                    const data = await response.json();
                    if (!response.ok || !data.success || typeof data.url !== 'string') {
                        throw new Error(data.message || `Error HTTP ${response.status} al subir la imagen.`);
                    }

                    const altText = file.name.replace(/\.[^.]+$/, '') || 'Imagen insertada';
                    insertUploadedImage(data.url, altText);
                } catch (error) {
                    console.error('Error al subir la imagen:', error);
                    errors.push(error.message || `No se pudo subir ${file.name}.`);
                }
            }
        } finally {
            setIsUploading(false);
            setUploadError(errors.join(' '));
            if (imageInputRef.current) imageInputRef.current.value = '';
        }
    };

    const handleImageUpload = async (event) => {
        await uploadImages(event.target.files);
    };

    const openImagePicker = () => {
        rememberSelection();
        imageInputRef.current?.click();
    };

    const hasExternalImageFiles = (dataTransfer) => getImageFiles(dataTransfer?.files).length > 0;

    const handleEditorDragEnter = (event) => {
        if (!hasExternalImageFiles(event.dataTransfer)) return;
        event.preventDefault();
        dragDepthRef.current += 1;
        setIsDraggingImage(true);
    };

    const handleEditorDragOver = (event) => {
        if (!hasExternalImageFiles(event.dataTransfer)) return;
        event.preventDefault();
        event.dataTransfer.dropEffect = 'copy';
    };

    const handleEditorDragLeave = (event) => {
        if (!isDraggingImage) return;
        event.preventDefault();
        dragDepthRef.current = Math.max(0, dragDepthRef.current - 1);
        if (dragDepthRef.current === 0) setIsDraggingImage(false);
    };

    const handleEditorDrop = (event) => {
        const files = getImageFiles(event.dataTransfer?.files);
        dragDepthRef.current = 0;
        setIsDraggingImage(false);
        if (files.length === 0) return;

        event.preventDefault();
        event.stopPropagation();
        const dropRange = getRangeFromPoint(event.clientX, event.clientY);
        uploadImages(files, dropRange);
    };

    const handleEditorPaste = (event) => {
        const files = getClipboardImageFiles(event.clipboardData);
        if (files.length === 0) return;
        event.preventDefault();
        event.stopPropagation();
        rememberSelection();
        uploadImages(files);
    };

    // Función para insertar enlaces
    // Función para insertar enlaces preservando la selección y soportando texto libre
    const insertLink = () => {
        const selection = window.getSelection();
        let savedRange = null;
        let selectedText = '';
        
        if (selection.rangeCount > 0) {
            savedRange = selection.getRangeAt(0).cloneRange();
            selectedText = selection.toString();
        }

        let linkText = selectedText;
        if (!linkText || linkText.trim() === '') {
            const promptText = prompt('Ingrese el texto que mostrará el enlace (o déjelo en blanco para usar la URL):', '');
            if (promptText === null) return; // Cancelado
            linkText = promptText.trim();
        }

        const rawUrl = prompt('Ingrese la dirección web (URL) del enlace:', 'https://');
        if (!rawUrl || rawUrl.trim() === '' || rawUrl.trim() === 'https://') {
            return;
        }

        let cleanUrl = rawUrl.trim();
        if (!/^https?:\/\//i.test(cleanUrl) && !cleanUrl.startsWith('/') && !cleanUrl.startsWith('mailto:')) {
            cleanUrl = 'https://' + cleanUrl;
        }

        if (!linkText || linkText === '') {
            linkText = cleanUrl;
        }

        // Restaurar foco al editor
        if (editorRef.current) {
            editorRef.current.focus();
        }

        if (savedRange) {
            selection.removeAllRanges();
            selection.addRange(savedRange);
        }

        if (!selectedText || selectedText.trim() === '') {
            // Insertar nodo <a> directo en la posición del cursor
            const a = document.createElement('a');
            a.href = cleanUrl;
            a.textContent = linkText;
            a.target = '_blank';
            a.rel = 'noopener noreferrer';
            a.style.color = '#007bff';
            a.style.textDecoration = 'underline';

            if (savedRange) {
                savedRange.deleteContents();
                savedRange.insertNode(a);
                const newRange = document.createRange();
                newRange.setStartAfter(a);
                newRange.collapse(true);
                selection.removeAllRanges();
                selection.addRange(newRange);
            } else if (editorRef.current) {
                editorRef.current.appendChild(a);
            }
            if (editorRef.current) {
                commitContent(editorRef.current.innerHTML);
            }
        } else {
            // Aplicar comando createLink sobre texto seleccionado
            handleCommand('createLink', cleanUrl);
            setTimeout(() => {
                if (editorRef.current) {
                    const links = editorRef.current.querySelectorAll('a[href="' + cleanUrl + '"]');
                    links.forEach(link => {
                        link.style.color = '#007bff';
                        link.style.textDecoration = 'underline';
                        link.setAttribute('target', '_blank');
                        link.setAttribute('rel', 'noopener noreferrer');
                    });
                    commitContent(editorRef.current.innerHTML);
                }
            }, 50);
        }
    };

    // Función para quitar enlaces
    const removeLink = () => {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const parentElement = range.commonAncestorContainer.nodeType === Node.TEXT_NODE 
                ? range.commonAncestorContainer.parentElement 
                : range.commonAncestorContainer;
            
            const linkElement = parentElement.closest('a');
            if (linkElement) {
                const textNode = document.createTextNode(linkElement.textContent);
                linkElement.parentNode.replaceChild(textNode, linkElement);
                
                commitContent(editorRef.current.innerHTML);
            } else {
                alert('No hay ningún enlace seleccionado para quitar.');
            }
        } else {
            alert('Por favor, seleccione el enlace que desea quitar.');
        }
    };

    // Manejar cambios en el contenido
    const handleChange = (e) => {
        const newContent = e.currentTarget.innerHTML;
        setContent(newContent);

        requestAnimationFrame(() => {
            decorateEditorImages();
            if (editorRef.current && onChange) onChange(getCleanEditorHtml(editorRef.current.innerHTML));
        });
    };

    // Manejar atajos de teclado
    const handleKeyDown = (e) => {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key.toLowerCase()) {
                case 'z':
                    e.preventDefault();
                    if (e.shiftKey) {
                        redo();
                    } else {
                        undo();
                    }
                    break;
                case 'y':
                    e.preventDefault();
                    redo();
                    break;
                case 's':
                    e.preventDefault();
                    if (onAutoSave) {
                        onAutoSave(content);
                    }
                    break;
            }
        }

        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleCommand('insertParagraph');
        }
    };

    // Crear botón de herramienta
    const createToolButton = (command, icon, title, value = null) => {
        return React.createElement(
            'button',
            {
                className: 'word-button',
                onMouseDown: (event) => {
                    event.preventDefault();
                    rememberSelection();
                },
                onClick: () => value ? handleCommand(command, value) : handleCommand(command),
                title: title,
                'aria-label': title,
                type: 'button',
                key: command + (value || '')
            },
            icon
        );
    };

    // Crear separador
    const createSeparator = (key) => {
        return React.createElement('div', {
            className: 'separator',
            key: key
        });
    };

    return React.createElement(
        'div',
        { 
            className: `word-editor-container ${isDraggingImage ? 'is-dragging' : ''}`,
            style: { direction: 'ltr' }
        },
        [
            // Mensajes de error
            (uploadError || validationError) && React.createElement(
                'div',
                {
                    key: 'error-messages',
                    className: 'alert alert-danger',
                    role: 'alert'
                },
                uploadError || validationError
            ),

            isDraggingImage && React.createElement(
                'div',
                {
                    className: 'word-editor-drop-overlay',
                    role: 'status',
                    'aria-live': 'polite'
                },
                [
                    React.createElement('i', { key: 'icon', 'aria-hidden': 'true' }, '🖼'),
                    React.createElement('strong', { key: 'title' }, 'Suelta la imagen aquí'),
                    React.createElement('span', { key: 'hint' }, 'JPG, PNG, GIF o WebP de hasta 5 MB')
                ]
            ),
            
            // Barra de herramientas estilo Microsoft Word
            React.createElement(
                'div',
                { 
                    key: 'word-toolbar',
                    className: 'word-toolbar',
                    role: 'toolbar'
                },
                [
                    // Pestañas del ribbon
                    React.createElement(
                        'div',
                        { className: 'ribbon-tabs', key: 'ribbon-tabs' },
                        [
                            React.createElement(
                                'button',
                                {
                                    className: `ribbon-tab ${activeTab === 'home' ? 'active' : ''}`,
                                    type: 'button',
                                    role: 'tab',
                                    'aria-selected': activeTab === 'home',
                                    'aria-controls': 'word-style-home-panel',
                                    onClick: () => setActiveTab('home'),
                                    key: 'home-tab'
                                },
                                'Inicio'
                            ),
                            React.createElement(
                                'button',
                                {
                                    className: `ribbon-tab ${activeTab === 'insert' ? 'active' : ''}`,
                                    type: 'button',
                                    role: 'tab',
                                    'aria-selected': activeTab === 'insert',
                                    'aria-controls': 'word-style-insert-panel',
                                    onClick: () => setActiveTab('insert'),
                                    key: 'insert-tab'
                                },
                                'Insertar'
                            )
                        ]
                    ),

                    // Contenido del ribbon
                    React.createElement(
                        'div',
                        {
                            id: activeTab === 'home' ? 'word-style-home-panel' : 'word-style-insert-panel',
                            className: 'ribbon-content',
                            role: 'tabpanel',
                            key: 'ribbon-content'
                        },
                        activeTab === 'home' ? [
                            // Grupo Portapapeles
                            React.createElement('div', { className: 'toolbar-group', key: 'clipboard' }, [
                                React.createElement('div', { className: 'group-label', key: 'clipboard-label' }, 'Portapapeles'),
                                React.createElement('div', { className: 'group-buttons', key: 'clipboard-buttons' }, [
                                    createToolButton('undo', '↶', 'Deshacer'),
                                    createToolButton('redo', '↷', 'Rehacer'),
                                    createToolButton('copy', '📋', 'Copiar'),
                                    createToolButton('cut', '✂️', 'Cortar'),
                                    createToolButton('paste', '📄', 'Pegar')
                                ])
                            ]),

                            createSeparator('sep1'),

                            // Grupo Fuente
                            React.createElement('div', { className: 'toolbar-group', key: 'font' }, [
                                React.createElement('div', { className: 'group-label', key: 'font-label' }, 'Fuente'),
                                React.createElement('div', { className: 'group-buttons', key: 'font-buttons' }, [
                                    React.createElement('select', {
                                        id: `${editorId}-font-family`,
                                        className: 'font-family-select',
                                        onMouseDown: rememberSelection,
                                        onChange: (e) => handleCommand('fontName', e.target.value),
                                        title: 'Fuente',
                                        'aria-label': 'Fuente de texto',
                                        key: 'font-family',
                                        defaultValue: 'Arial'
                                    }, fonts.map(font => 
                                        React.createElement('option', { value: font, key: font }, font)
                                    )),
                                    React.createElement('select', {
                                        id: `${editorId}-font-size`,
                                        className: 'font-size-select',
                                        onMouseDown: rememberSelection,
                                        onChange: (e) => handleCommand('fontSize', e.target.value),
                                        title: 'Tamaño',
                                        'aria-label': 'Tamaño de texto',
                                        key: 'font-size',
                                        defaultValue: '12'
                                    }, fontSizes.map(size => 
                                        React.createElement('option', { value: size, key: size }, size)
                                    )),
                                    createToolButton('bold', 'B', 'Negrita'),
                                    createToolButton('italic', 'I', 'Cursiva'),
                                    createToolButton('underline', 'U', 'Subrayado'),
                                    createToolButton('strikethrough', 'S', 'Tachado'),
                                    createToolButton('subscript', 'X₂', 'Subíndice'),
                                    createToolButton('superscript', 'X²', 'Superíndice')
                                ])
                            ]),

                            createSeparator('sep2'),

                            // Grupo Color
                            React.createElement('div', { className: 'toolbar-group color-picker-container', key: 'colors' }, [
                                React.createElement('div', { className: 'group-label', key: 'color-label' }, 'Color'),
                                React.createElement('div', { className: 'group-buttons', key: 'color-buttons' }, [
                                    React.createElement('button', {
                                        className: 'word-button color-button',
                                        type: 'button',
                                        onMouseDown: rememberSelection,
                                        onClick: () => setShowColorPicker(!showColorPicker),
                                        title: 'Color de texto',
                                        'aria-label': 'Color de texto',
                                        'aria-expanded': showColorPicker,
                                        key: 'text-color'
                                    }, [
                                        'A',
                                        React.createElement('div', {
                                            className: 'color-bar',
                                            key: 'color-bar'
                                        })
                                    ]),
                                    showColorPicker && React.createElement('div', {
                                        className: 'color-grid',
                                        key: 'color-grid'
                                    }, colors.map(color => 
                                        React.createElement('button', {
                                            className: 'color-swatch',
                                            type: 'button',
                                            style: { backgroundColor: color },
                                            title: `Color ${color}`,
                                            'aria-label': `Aplicar color ${color}`,
                                            onClick: () => {
                                                handleCommand('foreColor', color);
                                                setShowColorPicker(false);
                                            },
                                            key: color
                                        })
                                    ))
                                ])
                            ]),

                            createSeparator('sep3'),

                            // Grupo Párrafo
                            React.createElement('div', { className: 'toolbar-group', key: 'paragraph' }, [
                                React.createElement('div', { className: 'group-label', key: 'paragraph-label' }, 'Párrafo'),
                                React.createElement('div', { className: 'group-buttons', key: 'paragraph-buttons' }, [
                                    createToolButton('insertUnorderedList', '•', 'Lista con viñetas'),
                                    createToolButton('insertOrderedList', '1.', 'Lista numerada'),
                                    createToolButton('outdent', '⬅', 'Disminuir sangría'),
                                    createToolButton('indent', '➡', 'Aumentar sangría'),
                                    createToolButton('justifyLeft', '⬅', 'Alinear izquierda'),
                                    createToolButton('justifyCenter', '⬌', 'Centrar'),
                                    createToolButton('justifyRight', '➡', 'Alinear derecha'),
                                    createToolButton('justifyFull', '⬍', 'Justificar')
                                ])
                            ]),

                            createSeparator('sep4'),

                            // Grupo Enlaces
                            React.createElement('div', { className: 'toolbar-group', key: 'links' }, [
                                React.createElement('div', { className: 'group-label', key: 'links-label' }, 'Enlaces'),
                                React.createElement('div', { className: 'group-buttons', key: 'links-buttons' }, [
                                    React.createElement('button', {
                                        className: 'word-button',
                                        onMouseDown: (event) => {
                                            event.preventDefault();
                                            rememberSelection();
                                        },
                                        onClick: insertLink,
                                        title: 'Insertar enlace',
                                        'aria-label': 'Insertar enlace',
                                        key: 'insert-link',
                                        type: 'button'
                                    }, '🔗'),
                                    React.createElement('button', {
                                        className: 'word-button',
                                        onMouseDown: (event) => {
                                            event.preventDefault();
                                            rememberSelection();
                                        },
                                        onClick: removeLink,
                                        title: 'Quitar enlace',
                                        'aria-label': 'Quitar enlace',
                                        key: 'remove-link',
                                        type: 'button'
                                    }, '🚫')
                                ])
                            ])
                        ] : [
                            // Pestaña Insertar
                            React.createElement('div', { className: 'toolbar-group', key: 'insert-images' }, [
                                React.createElement('div', { className: 'group-label', key: 'images-label' }, 'Imágenes'),
                                React.createElement('div', { className: 'group-buttons', key: 'images-buttons' }, [
                                    React.createElement('button', {
                                        className: `word-button ${isUploading ? 'uploading' : ''}`,
                                        type: 'button',
                                        title: isUploading ? 'Subiendo imagen...' : 'Insertar imagen',
                                        'aria-label': isUploading ? 'Subiendo imagen' : 'Insertar imagen',
                                        'aria-controls': imageInputId,
                                        disabled: isUploading,
                                        onMouseDown: (event) => {
                                            event.preventDefault();
                                            rememberSelection();
                                        },
                                        onClick: openImagePicker,
                                        key: 'image-upload'
                                    }, isUploading ? '⏳' : '🖼'),
                                    React.createElement('input', {
                                        ref: imageInputRef,
                                        id: imageInputId,
                                        type: 'file',
                                        hidden: true,
                                        tabIndex: -1,
                                        accept: 'image/jpeg,image/jpg,image/png,image/gif,image/webp',
                                        'aria-label': 'Seleccionar imagen para insertar',
                                        onChange: handleImageUpload,
                                        disabled: isUploading,
                                        key: 'image-input'
                                    })
                                ])
                            ])
                        ]
                    )
                ]
            ),
            
            // Área editable
            React.createElement(
                'div',
                {
                    key: 'editor',
                    ref: editorRef,
                    id: editorElementId,
                    className: 'word-editor-content',
                    contentEditable: true,
                    spellCheck: true,
                    onInput: handleChange,
                    onClick: (event) => {
                        if (!event.target.closest('.editor-image-frame')) clearImageSelection();
                        rememberSelection();
                    },
                    onKeyDown: handleKeyDown,
                    onKeyUp: rememberSelection,
                    onMouseUp: rememberSelection,
                    onBlur: rememberSelection,
                    onPaste: handleEditorPaste,
                    onDragEnter: handleEditorDragEnter,
                    onDragOver: handleEditorDragOver,
                    onDragLeave: handleEditorDragLeave,
                    onDrop: handleEditorDrop,
                    role: 'textbox',
                    'aria-label': 'Editor de texto enriquecido estilo Microsoft Word',
                    'aria-multiline': 'true',
                    'aria-required': 'true',
                    'aria-busy': isUploading ? 'true' : 'false'
                }
            )
        ]
    );
};

// Exportar el componente
if (typeof module !== 'undefined' && module.exports) {
    module.exports = WordStyleEditor;
} else if (typeof window !== 'undefined') {
    window.WordStyleEditor = WordStyleEditor;
}