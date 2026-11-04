/* =========================================================
   NINA.SUCKS ADVANCED RICH TEXT EDITOR WITH IMAGE UPLOAD
   Provides formatting toolbar, keyboard shortcuts, link tools,
   headers (H1-H4), alignments, lists, paste sanitization,
   direct image file upload, drag-and-drop & clipboard image support,
   live word/character counting, and textarea synchronization.
   ========================================================= */
(function() {
  'use strict';

  // Get dynamic admin image limit (reads window.MAX_IMAGE_MB, meta tag, or defaults to 5MB)
  function getMaxImageMb() {
    if (typeof window.MAX_IMAGE_MB === 'number' && window.MAX_IMAGE_MB > 0) {
      return window.MAX_IMAGE_MB;
    }
    const metaLimit = document.querySelector('meta[name="max-image-mb"]');
    if (metaLimit && !isNaN(parseFloat(metaLimit.content)) && parseFloat(metaLimit.content) > 0) {
      return parseFloat(metaLimit.content);
    }
    return 5;
  }

  // Create image upload modal once if not exists
  let sharedImageModal = null;
  let activeEditorCanvas = null;
  let activeEditorSync = null;

  function ensureImageUploadModal() {
    if (sharedImageModal) {
      const maxMb = getMaxImageMb();
      const limitText = sharedImageModal.querySelector('#modalMaxLimitText');
      if (limitText) limitText.textContent = maxMb;
      return sharedImageModal;
    }

    const maxMb = getMaxImageMb();
    const modal = document.createElement('div');
    modal.id = 'richEditorImageModal';
    modal.style.cssText = `
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.82); backdrop-filter: blur(8px);
      z-index: 99999; display: none; align-items: center; justify-content: center;
      padding: 16px; box-sizing: border-box; font-family: Figtree, system-ui, sans-serif;
    `;

    modal.innerHTML = `
      <div style="background: #0f0716; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8), 0 0 30px rgba(255, 43, 214, 0.15); overflow: hidden; color: #f8fafc;">
        <!-- Modal Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); background: rgba(255, 255, 255, 0.02);">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span style="color: #ff2bd6; font-size: 18px;">🖼️</span>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #fff;">Insert Image into Post</h3>
          </div>
          <button type="button" id="closeImageModalBtn" style="background: transparent; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; padding: 4px; line-height: 1; border-radius: 6px;">&times;</button>
        </div>

        <!-- Tab Selector -->
        <div style="display: flex; border-bottom: 1px solid rgba(255, 255, 255, 0.08); background: rgba(0, 0, 0, 0.3);">
          <button type="button" id="tabUploadBtn" style="flex: 1; padding: 12px; font-size: 13px; font-weight: 700; border: none; background: rgba(255, 43, 214, 0.12); color: #ff2bd6; border-bottom: 2px solid #ff2bd6; cursor: pointer; transition: all 0.15s;">
            📁 Upload from Device
          </button>
          <button type="button" id="tabUrlBtn" style="flex: 1; padding: 12px; font-size: 13px; font-weight: 700; border: none; background: transparent; color: #94a3b8; border-bottom: 2px solid transparent; cursor: pointer; transition: all 0.15s;">
            🔗 Web Image URL
          </button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 22px;">
          <!-- TAB 1: FILE UPLOAD -->
          <div id="tabUploadContent">
            <div id="modalDropZone" style="border: 2px dashed rgba(255, 255, 255, 0.18); border-radius: 12px; padding: 26px 16px; text-align: center; background: rgba(255, 255, 255, 0.02); cursor: pointer; transition: all 0.2s;">
              <input type="file" id="modalImageFileInput" accept="image/*" style="display: none;">
              <div id="modalDropPrompt">
                <div style="font-size: 32px; margin-bottom: 8px;">📷</div>
                <div style="font-weight: 700; font-size: 14px; color: #fff;">Click to browse or drag &amp; drop image here</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 6px;" id="modalLimitHint">
                  Supports JPG, PNG, GIF, WEBP (Max <span id="modalMaxLimitText">${maxMb}</span> MB)
                </div>
              </div>

              <!-- Preview inside modal -->
              <div id="modalImagePreviewBox" style="display: none;">
                <img id="modalPreviewImg" src="" alt="Preview" style="max-height: 180px; max-width: 100%; border-radius: 8px; object-fit: contain; display: block; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.5);">
                <div id="modalFileMeta" style="font-size: 11px; color: #19e3ff; font-weight: 600; margin-top: 8px;"></div>
              </div>
            </div>

            <!-- Error alert -->
            <div id="modalUploadError" style="display: none; margin-top: 12px; padding: 10px 14px; border-radius: 8px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 12px; font-weight: 600;"></div>

            <div style="margin-top: 14px;">
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px;">Caption / Alt Text (Optional)</label>
              <input type="text" id="modalUploadAlt" placeholder="Image description..." style="width: 100%; box-sizing: border-box; background: #07030d; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px; padding: 10px 12px; color: #fff; font-size: 13px; outline: none;">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 18px; justify-content: flex-end;">
              <button type="button" id="modalCancelBtn1" style="padding: 9px 16px; border-radius: 8px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); color: #cbd5e1; font-weight: 600; font-size: 13px; cursor: pointer;">Cancel</button>
              <button type="button" id="modalInsertUploadBtn" disabled style="padding: 9px 20px; border-radius: 8px; background: linear-gradient(135deg, #ff2bd6, #d946ef); border: none; color: #fff; font-weight: 700; font-size: 13px; cursor: pointer; opacity: 0.5; transition: opacity 0.15s;">Insert Image</button>
            </div>
          </div>

          <!-- TAB 2: URL INPUT -->
          <div id="tabUrlContent" style="display: none;">
            <div style="margin-bottom: 14px;">
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px;">Image Web URL</label>
              <input type="url" id="modalUrlInput" placeholder="https://example.com/photo.jpg" style="width: 100%; box-sizing: border-box; background: #07030d; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px; padding: 10px 12px; color: #fff; font-size: 13px; outline: none;">
            </div>

            <div style="margin-bottom: 14px;">
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px;">Caption / Alt Text (Optional)</label>
              <input type="text" id="modalUrlAlt" placeholder="Image description..." style="width: 100%; box-sizing: border-box; background: #07030d; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px; padding: 10px 12px; color: #fff; font-size: 13px; outline: none;">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 18px; justify-content: flex-end;">
              <button type="button" id="modalCancelBtn2" style="padding: 9px 16px; border-radius: 8px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); color: #cbd5e1; font-weight: 600; font-size: 13px; cursor: pointer;">Cancel</button>
              <button type="button" id="modalInsertUrlBtn" style="padding: 9px 20px; border-radius: 8px; background: linear-gradient(135deg, #19e3ff, #0284c7); border: none; color: #07030d; font-weight: 800; font-size: 13px; cursor: pointer;">Insert Image</button>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    sharedImageModal = modal;

    // State
    let selectedDataUrl = null;

    // Tabs switching
    const tabUploadBtn = modal.querySelector('#tabUploadBtn');
    const tabUrlBtn = modal.querySelector('#tabUrlBtn');
    const tabUploadContent = modal.querySelector('#tabUploadContent');
    const tabUrlContent = modal.querySelector('#tabUrlContent');

    tabUploadBtn.addEventListener('click', () => {
      tabUploadBtn.style.background = 'rgba(255, 43, 214, 0.12)';
      tabUploadBtn.style.color = '#ff2bd6';
      tabUploadBtn.style.borderBottomColor = '#ff2bd6';
      tabUrlBtn.style.background = 'transparent';
      tabUrlBtn.style.color = '#94a3b8';
      tabUrlBtn.style.borderBottomColor = 'transparent';
      tabUploadContent.style.display = 'block';
      tabUrlContent.style.display = 'none';
    });

    tabUrlBtn.addEventListener('click', () => {
      tabUrlBtn.style.background = 'rgba(25, 227, 255, 0.12)';
      tabUrlBtn.style.color = '#19e3ff';
      tabUrlBtn.style.borderBottomColor = '#19e3ff';
      tabUploadBtn.style.background = 'transparent';
      tabUploadBtn.style.color = '#94a3b8';
      tabUploadBtn.style.borderBottomColor = 'transparent';
      tabUrlContent.style.display = 'block';
      tabUploadContent.style.display = 'none';
      modal.querySelector('#modalUrlInput').focus();
    });

    // File input & DropZone
    const dropZone = modal.querySelector('#modalDropZone');
    const fileInput = modal.querySelector('#modalImageFileInput');
    const errorAlert = modal.querySelector('#modalUploadError');
    const dropPrompt = modal.querySelector('#modalDropPrompt');
    const previewBox = modal.querySelector('#modalImagePreviewBox');
    const previewImg = modal.querySelector('#modalPreviewImg');
    const fileMeta = modal.querySelector('#modalFileMeta');
    const insertUploadBtn = modal.querySelector('#modalInsertUploadBtn');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', e => {
      e.preventDefault();
      dropZone.style.borderColor = '#ff2bd6';
      dropZone.style.background = 'rgba(255, 43, 214, 0.06)';
    });

    dropZone.addEventListener('dragleave', () => {
      dropZone.style.borderColor = 'rgba(255, 255, 255, 0.18)';
      dropZone.style.background = 'rgba(255, 255, 255, 0.02)';
    });

    dropZone.addEventListener('drop', e => {
      e.preventDefault();
      dropZone.style.borderColor = 'rgba(255, 255, 255, 0.18)';
      dropZone.style.background = 'rgba(255, 255, 255, 0.02)';
      if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        processModalFile(e.dataTransfer.files[0]);
      }
    });

    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files[0]) {
        processModalFile(fileInput.files[0]);
      }
    });

    function processModalFile(file) {
      errorAlert.style.display = 'none';
      const maxMb = getMaxImageMb();
      const fileSizeMb = file.size / (1024 * 1024);

      if (!file.type.startsWith('image/')) {
        errorAlert.textContent = 'Please select a valid image file (JPG, PNG, GIF, WEBP).';
        errorAlert.style.display = 'block';
        return;
      }

      if (fileSizeMb > maxMb) {
        errorAlert.innerHTML = `⚠️ <b>Image upload rejected:</b> File is ${fileSizeMb.toFixed(2)} MB, which exceeds the max image limit of <b>${maxMb} MB</b> set in Admin settings.`;
        errorAlert.style.display = 'block';
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        selectedDataUrl = e.target.result;
        previewImg.src = selectedDataUrl;
        fileMeta.textContent = `${file.name} (${fileSizeMb.toFixed(2)} MB)`;
        dropPrompt.style.display = 'none';
        previewBox.style.display = 'block';
        insertUploadBtn.disabled = false;
        insertUploadBtn.style.opacity = '1';
      };
      reader.readAsDataURL(file);
    }

    function closeModal() {
      modal.style.display = 'none';
      selectedDataUrl = null;
      fileInput.value = '';
      previewImg.src = '';
      dropPrompt.style.display = 'block';
      previewBox.style.display = 'none';
      insertUploadBtn.disabled = true;
      insertUploadBtn.style.opacity = '0.5';
      errorAlert.style.display = 'none';
      modal.querySelector('#modalUploadAlt').value = '';
      modal.querySelector('#modalUrlInput').value = '';
      modal.querySelector('#modalUrlAlt').value = '';
    }

    modal.querySelector('#closeImageModalBtn').addEventListener('click', closeModal);
    modal.querySelector('#modalCancelBtn1').addEventListener('click', closeModal);
    modal.querySelector('#modalCancelBtn2').addEventListener('click', closeModal);
    modal.addEventListener('click', e => {
      if (e.target === modal) closeModal();
    });

    // Insert from Upload
    insertUploadBtn.addEventListener('click', () => {
      if (!selectedDataUrl || !activeEditorCanvas) return;
      const altText = modal.querySelector('#modalUploadAlt').value.trim() || 'Uploaded image';
      insertImageToCanvas(selectedDataUrl, altText);
      closeModal();
    });

    // Insert from URL
    modal.querySelector('#modalInsertUrlBtn').addEventListener('click', () => {
      const urlInput = modal.querySelector('#modalUrlInput');
      const url = (urlInput.value || '').trim();
      if (!url || !activeEditorCanvas) return;
      const altText = modal.querySelector('#modalUrlAlt').value.trim() || 'Image';
      insertImageToCanvas(url, altText);
      closeModal();
    });

    function insertImageToCanvas(imgSrc, altText) {
      if (!activeEditorCanvas) return;
      activeEditorCanvas.focus();
      const safeSrc = imgSrc.startsWith('data:') ? imgSrc : encodeURI(imgSrc);
      const safeAlt = altText.replace(/"/g, '&quot;');
      const html = `<p><img src="${safeSrc}" alt="${safeAlt}" style="max-width:100%;border-radius:8px;margin:12px 0;display:block;box-shadow:0 4px 15px rgba(0,0,0,0.4);" loading="lazy"></p><p><br></p>`;
      document.execCommand('insertHTML', false, html);
      if (typeof activeEditorSync === 'function') {
        activeEditorSync();
      }
    }

    return modal;
  }

  function openImageModalForEditor(canvas, syncCallback) {
    activeEditorCanvas = canvas;
    activeEditorSync = syncCallback;
    const modal = ensureImageUploadModal();
    const maxMb = getMaxImageMb();
    const limitText = modal.querySelector('#modalMaxLimitText');
    if (limitText) limitText.textContent = maxMb;
    modal.style.display = 'flex';
  }

  // Core Editor Initializer
  function initRichEditor(editorContainer) {
    if (!editorContainer || editorContainer._initialized) return;
    editorContainer._initialized = true;

    const toolbar = editorContainer.querySelector('.editor-toolbar');
    const canvas = editorContainer.querySelector('.editor-content');
    const source = editorContainer.querySelector('.editor-source');
    const wordCount = editorContainer.querySelector('#editorWordCount') || editorContainer.querySelector('.editor-word-count');
    const form = editorContainer.closest('form');
    const hiddenTextarea = editorContainer.parentElement?.querySelector('textarea[name="body"]') || 
                           editorContainer.querySelector('textarea[name="body"]') ||
                           document.getElementById('hiddenBodyTextarea');

    if (!canvas) return;

    // Hidden image file input dedicated to this editor for direct file picking
    const directFileInput = document.createElement('input');
    directFileInput.type = 'file';
    directFileInput.accept = 'image/*';
    directFileInput.style.display = 'none';
    editorContainer.appendChild(directFileInput);

    directFileInput.addEventListener('change', () => {
      if (directFileInput.files && directFileInput.files[0]) {
        insertImageFileDirectly(directFileInput.files[0]);
        directFileInput.value = '';
      }
    });

    // Ensure source textarea is strictly hidden initially
    if (source) {
      source.style.display = 'none';
    }

    // Load initial content from hidden textarea if any
    if (hiddenTextarea && hiddenTextarea.value && !canvas.innerHTML.trim()) {
      canvas.innerHTML = hiddenTextarea.value;
    }

    function sync() {
      if (source && source.style.display === 'block') {
        canvas.innerHTML = source.value;
      } else if (source) {
        source.value = canvas.innerHTML;
      }
      if (hiddenTextarea) {
        hiddenTextarea.value = canvas.innerHTML;
      }
      updateWordCount();
    }

    function updateWordCount() {
      if (!wordCount) return;
      const text = canvas.innerText || '';
      const words = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
      const chars = text.length;
      const metaMax = document.querySelector('meta[name="max-text-length"]');
      const maxChars = metaMax && parseInt(metaMax.content, 10) > 0 ? parseInt(metaMax.content, 10) : 10000;
      const readingTime = Math.max(1, Math.ceil(words / 200));
      if (chars > maxChars) {
        wordCount.innerHTML = `<span style="color:#f87171;font-weight:700;">⚠️ ${chars.toLocaleString()} / ${maxChars.toLocaleString()} chars (limit exceeded)</span> &middot; ${words.toLocaleString()} words`;
      } else {
        wordCount.textContent = `${words.toLocaleString()} words · ${chars.toLocaleString()} / ${maxChars.toLocaleString()} chars · ~${readingTime} min read`;
      }
    }

    // Execute rich text commands safely
    function exec(cmd, val = null) {
      canvas.focus();
      try {
        document.execCommand(cmd, false, val);
      } catch (e) {
        console.warn('execCommand failed:', cmd, e);
      }
      sync();
      updateActiveStates();
    }

    function updateActiveStates() {
      if (!toolbar) return;
      const buttons = toolbar.querySelectorAll('.editor-btn[data-cmd]');
      buttons.forEach(btn => {
        const cmd = btn.dataset.cmd;
        try {
          const state = document.queryCommandState(cmd);
          btn.classList.toggle('is-active', Boolean(state));
        } catch(e) {}
      });

      // Update block select value to reflect current block if possible
      const select = toolbar.querySelector('select.editor-select');
      if (select) {
        try {
          const block = document.queryCommandValue('formatBlock');
          if (block) {
            const cleanBlock = block.toLowerCase().replace(/<|>/g, '');
            const matchingOpt = select.querySelector(`option[value="${cleanBlock}"]`);
            if (matchingOpt) {
              select.value = cleanBlock;
            }
          }
        } catch(e) {}
      }
    }

    // Insert Image File directly into canvas (with size check)
    function insertImageFileDirectly(file) {
      const maxMb = getMaxImageMb();
      const fileSizeMb = file.size / (1024 * 1024);

      if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file (JPG, PNG, GIF, WEBP).');
        return;
      }

      if (fileSizeMb > maxMb) {
        alert(`Image exceeds the maximum allowed upload limit of ${maxMb} MB configured in admin settings (file is ${fileSizeMb.toFixed(2)} MB).`);
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        canvas.focus();
        const dataUrl = e.target.result;
        const html = `<p><img src="${dataUrl}" alt="${file.name.replace(/"/g, '&quot;')}" style="max-width:100%;border-radius:8px;margin:12px 0;display:block;box-shadow:0 4px 15px rgba(0,0,0,0.4);" loading="lazy"></p><p><br></p>`;
        document.execCommand('insertHTML', false, html);
        sync();
      };
      reader.readAsDataURL(file);
    }

    // Insert or edit link functionality
    function handleLinkAction() {
      canvas.focus();
      const selection = window.getSelection();
      let selectedText = '';
      let existingLinkNode = null;

      if (selection && selection.rangeCount > 0) {
        selectedText = selection.toString().trim();
        let node = selection.anchorNode;
        while (node && node !== canvas) {
          if (node.nodeName === 'A') {
            existingLinkNode = node;
            break;
          }
          node = node.parentNode;
        }
      }

      const defaultUrl = existingLinkNode ? existingLinkNode.getAttribute('href') : 'https://';
      const defaultText = existingLinkNode ? existingLinkNode.innerText : selectedText;

      const url = prompt('Enter link URL (e.g. https://...):', defaultUrl);
      if (url === null) return; // Cancelled

      const cleanUrl = url.trim();
      if (!cleanUrl || cleanUrl === 'https://' || cleanUrl === 'http://') {
        if (existingLinkNode) {
          exec('unlink');
        }
        return;
      }

      if (existingLinkNode) {
        existingLinkNode.setAttribute('href', cleanUrl);
        existingLinkNode.setAttribute('target', '_blank');
        existingLinkNode.setAttribute('rel', 'noopener noreferrer');
        sync();
        return;
      }

      if (selectedText) {
        exec('createLink', cleanUrl);
        const links = canvas.querySelectorAll('a[href="' + cleanUrl + '"]');
        links.forEach(a => {
          a.setAttribute('target', '_blank');
          a.setAttribute('rel', 'noopener noreferrer');
        });
        sync();
      } else {
        const text = prompt('Enter link text to display:', cleanUrl.replace(/^https?:\/\//, ''));
        const displayText = (text && text.trim()) ? text.trim() : cleanUrl;
        const linkHtml = `<a href="${encodeURI(cleanUrl)}" target="_blank" rel="noopener noreferrer">${escapeHtml(displayText)}</a>`;
        exec('insertHTML', linkHtml);
      }
    }

    function escapeHtml(str) {
      return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Drag and Drop of Images directly onto editor canvas
    canvas.addEventListener('dragover', e => {
      e.preventDefault();
      canvas.style.outline = '2px dashed #ff2bd6';
    });

    canvas.addEventListener('dragleave', () => {
      canvas.style.outline = 'none';
    });

    canvas.addEventListener('drop', e => {
      e.preventDefault();
      canvas.style.outline = 'none';
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];
        if (file.type.startsWith('image/')) {
          insertImageFileDirectly(file);
        }
      }
    });

    // Paste handling: supports text, HTML, and pasting images directly from clipboard!
    canvas.addEventListener('paste', e => {
      const clipboardData = e.clipboardData || window.clipboardData;
      if (!clipboardData) return;

      // Check if image item is pasted from clipboard
      const items = clipboardData.items;
      if (items) {
        for (let i = 0; i < items.length; i++) {
          if (items[i].type.indexOf('image') !== -1) {
            const blob = items[i].getAsFile();
            if (blob) {
              e.preventDefault();
              insertImageFileDirectly(blob);
              return;
            }
          }
        }
      }

      const text = clipboardData.getData('text/plain');
      const html = clipboardData.getData('text/html');

      if (html) {
        e.preventDefault();
        try {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          doc.querySelectorAll('script, style, meta, link, iframe, object, embed, form, input, button').forEach(el => el.remove());
          
          doc.querySelectorAll('a').forEach(a => {
            a.setAttribute('target', '_blank');
            a.setAttribute('rel', 'noopener noreferrer');
          });

          doc.querySelectorAll('*').forEach(el => {
            el.removeAttribute('class');
            el.removeAttribute('id');
            if (el.tagName !== 'SPAN' && el.tagName !== 'FONT' && el.tagName !== 'A' && el.tagName !== 'IMG') {
              el.removeAttribute('style');
            }
          });
          const cleanHtml = doc.body.innerHTML;
          document.execCommand('insertHTML', false, cleanHtml);
        } catch(err) {
          document.execCommand('insertText', false, text);
        }
        sync();
      }
    });

    // Toolbar click handling
    if (toolbar) {
      toolbar.addEventListener('click', e => {
        const btn = e.target.closest('.editor-btn');
        if (!btn) return;
        e.preventDefault();

        // Direct exec command
        if (btn.dataset.cmd) {
          exec(btn.dataset.cmd, btn.dataset.val || null);
          return;
        }

        // Color command
        if (btn.dataset.color) {
          exec('foreColor', btn.dataset.color);
          return;
        }

        // Highlight background color
        if (btn.dataset.hilite) {
          exec('hiliteColor', btn.dataset.hilite);
          return;
        }

        // Custom actions
        const action = btn.dataset.action;
        if (action === 'link') {
          handleLinkAction();
        } else if (action === 'unlink') {
          exec('unlink');
        } else if (action === 'h1') {
          exec('formatBlock', '<h1>');
        } else if (action === 'h2') {
          exec('formatBlock', '<h2>');
        } else if (action === 'h3') {
          exec('formatBlock', '<h3>');
        } else if (action === 'h4') {
          exec('formatBlock', '<h4>');
        } else if (action === 'blockquote') {
          exec('formatBlock', '<blockquote>');
        } else if (action === 'code') {
          exec('formatBlock', '<pre>');
        } else if (action === 'inline-code') {
          const selection = window.getSelection();
          if (selection && selection.toString().trim()) {
            exec('insertHTML', `<code>${escapeHtml(selection.toString())}</code>`);
          } else {
            exec('insertHTML', `<code>code</code>`);
          }
        } else if (action === 'hr') {
          exec('insertHorizontalRule');
        } else if (action === 'image' || action === 'upload-image') {
          // Open the rich image upload & URL modal
          openImageModalForEditor(canvas, sync);
        } else if (action === 'image-browse') {
          // Direct file picker trigger
          directFileInput.click();
        } else if (action === 'clear') {
          exec('removeFormat');
          exec('formatBlock', '<p>');
        } else if (action === 'toggle-source') {
          if (!source) return;
          const isSource = source.style.display === 'block';
          if (isSource) {
            canvas.innerHTML = source.value;
            source.style.display = 'none';
            canvas.style.display = 'block';
            btn.classList.remove('is-active');
            canvas.focus();
          } else {
            source.value = canvas.innerHTML;
            canvas.style.display = 'none';
            source.style.display = 'block';
            btn.classList.add('is-active');
            source.focus();
          }
        }
      });

      // Select dropdowns (e.g. formatBlock for H1, H2, H3, H4, p, blockquote, pre)
      toolbar.querySelectorAll('select.editor-select').forEach(sel => {
        sel.addEventListener('change', () => {
          const tag = sel.value;
          exec('formatBlock', tag);
        });
      });
    }

    // Keyboard shortcuts in canvas
    canvas.addEventListener('keydown', e => {
      if (e.ctrlKey || e.metaKey) {
        if (e.key === 'b' || e.key === 'B') {
          e.preventDefault();
          exec('bold');
        } else if (e.key === 'i' || e.key === 'I') {
          e.preventDefault();
          exec('italic');
        } else if (e.key === 'u' || e.key === 'U') {
          e.preventDefault();
          exec('underline');
        } else if (e.key === 'k' || e.key === 'K') {
          e.preventDefault();
          handleLinkAction();
        } else if (e.key === 'z' || e.key === 'Z') {
          if (e.shiftKey) {
            e.preventDefault();
            exec('redo');
          } else {
            e.preventDefault();
            exec('undo');
          }
        } else if (e.key === 'y' || e.key === 'Y') {
          e.preventDefault();
          exec('redo');
        }
      }
    });

    canvas.addEventListener('input', sync);
    canvas.addEventListener('keyup', () => { sync(); updateActiveStates(); });
    canvas.addEventListener('mouseup', updateActiveStates);

    if (source) {
      source.addEventListener('input', () => {
        if (hiddenTextarea) hiddenTextarea.value = source.value;
        updateWordCount();
      });
    }

    if (form) {
      form.addEventListener('submit', () => {
        sync();
      });
    }

    // Initial word count
    updateWordCount();
  }

  // Auto-initialize any .rich-editor on page load or immediate call
  function runInit() {
    document.querySelectorAll('.rich-editor').forEach(initRichEditor);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runInit);
  } else {
    runInit();
  }

  // Expose globally
  window.initRichEditor = initRichEditor;
  window.openImageModalForEditor = openImageModalForEditor;
})();
