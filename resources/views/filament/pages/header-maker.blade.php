<div>
  <style>
    :root{--toolbar-h:64px;--bg:#f3f4f6}
    .canvas-builder{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;background:var(--bg);display:flex;flex-direction:column;height:100%}
    .toolbar{height:var(--toolbar-h);display:flex;gap:10px;align-items:center;padding:8px 12px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.06)}
    .toolbar > *{height:40px}
    .btn{padding:8px 12px;border-radius:8px;border:1px solid #ddd;background:#fff;cursor:pointer}
    .btn.primary{background:#111827;color:#fff;border:0}
    .select, .input, .color{padding:6px;border-radius:6px;border:1px solid #ddd;background:#fff}
    .container{flex:1;display:flex;gap:12px;padding:12px}
    .panel{width:260px;background:#fff;padding:12px;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:auto}
    .stage-wrap{flex:1;display:flex;align-items:center;justify-content:center}
    #canvas-container{width:794px;height:224px;background:white;box-shadow:0 6px 30px rgba(2,6,23,.12);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
    canvas{border:1px solid #e5e7eb}
    label{display:block;font-size:12px;color:#374151;margin-bottom:6px}
    .field{margin-bottom:10px}
    .muted{font-size:12px;color:#6b7280}
  </style>

  <div class="canvas-builder">
    
    <div class="toolbar">
      <select id="templateType" class="select">
          <option value="prescription">Prescription</option>
          <option value="certificate">Certificate</option>
      </select>
      <button id="loadTemplate" class="btn">Load</button>
      <button id="saveTemplate" class="btn primary">Save Template</button>
    </div>

    <div class="toolbar">
      <input id="file" type="file" accept="image/*" class="input" />
      <button id="addText" class="btn">Add Text</button>
      <button id="delete" class="btn">Delete</button>
      <button id="clear" class="btn">Clear</button>
      <div style="flex:1"></div>
      <label style="font-size:14px">Zoom:
        <input id="zoomRange" type="range" min="0.5" max="2" step="0.1" value="1" />
      </label>
    </div>


    <div class="container">
      <div class="panel">
        <div class="field">
          <label>Selected object controls</label>
          <div class="muted">Select an object on the canvas to edit its properties.</div>
        </div>

        <div class="field">
          <label for="fontFamily">Font family</label>
          <select id="fontFamily" class="select">
            <option value="Arial">Arial</option>
            <option value="Helvetica">Helvetica</option>
            <option value="Times New Roman">Times New Roman</option>
            <option value="Courier New">Courier New</option>
          </select>
        </div>

        <div class="field">
          <label for="fontSize">Font size</label>
          <input id="fontSize" class="input" type="number" value="40" min="8" max="240" />
        </div>

        <div class="field">
          <label>Style</label>
          <div style="display:flex;gap:8px">
            <button id="bold" class="btn">B</button>
            <button id="italic" class="btn">I</button>
            <button id="underline" class="btn">U</button>
          </div>
        </div>

        <div class="field">
          <label for="color">Color</label>
          <input id="color" class="color" type="color" value="#111111" />
        </div>

        <div class="field">
          <label for="opacity">Opacity</label>
          <input id="opacity" type="range" min="0" max="1" step="0.01" value="1" />
        </div>

        <div class="field">
          <label for="angle">Rotation (degrees)</label>
          <input id="angle" type="range" min="0" max="360" step="1" value="0" />
        </div>
      </div>

      <div class="stage-wrap">
        <div id="canvas-container">
          <canvas id="c" width="794" height="224"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const canvas = new fabric.Canvas('c', { backgroundColor: '#ffffff', preserveObjectStacking: true });
      const fileInput = document.getElementById('file');
      const zoomRange = document.getElementById('zoomRange');
      const saveServer = document.getElementById('saveServer');

      const fontFamily = document.getElementById('fontFamily');
      const fontSize = document.getElementById('fontSize');
      const color = document.getElementById('color');
      const opacity = document.getElementById('opacity');
      const angle = document.getElementById('angle');
      const boldBtn = document.getElementById('bold');
      const italicBtn = document.getElementById('italic');
      const underlineBtn = document.getElementById('underline');

      // Upload image
      fileInput.addEventListener('change', (e) => {
        const f = e.target.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
          fabric.Image.fromURL(ev.target.result, (img) => {
            img.set({ left: 50, top: 50, scaleX: Math.min(1, 500 / img.width), scaleY: Math.min(1, 500 / img.height) });
            canvas.add(img).setActiveObject(img);
          });
        };
        reader.readAsDataURL(f);
      });

      // Add Text
      document.getElementById('addText').addEventListener('click', () => {
        const txt = new fabric.IText('Double-click to edit', {
          left: 100, top: 80, fontFamily: 'Arial', fontSize: 40, fill: '#111'
        });
        canvas.add(txt).setActiveObject(txt);
      });

      document.getElementById('delete').addEventListener('click', () => {
        const obj = canvas.getActiveObject(); if (obj) canvas.remove(obj);
      });

      document.getElementById('clear').addEventListener('click', () => {
        if (!confirm('Clear entire canvas?')) return;
        canvas.clear(); canvas.setBackgroundColor('#ffffff');
      });

      // Zoom
      const container = document.getElementById('canvas-container');

        zoomRange.addEventListener('input', (e) => {
        const zoom = parseFloat(e.target.value);
        container.style.transformOrigin = 'center center';
        container.style.transform = `scale(${zoom})`;
        });

      // Formatting controls
      function getActiveText() {
        const obj = canvas.getActiveObject();
        return obj && obj.type === 'i-text' ? obj : null;
      }

      function updateControls(obj) {
        if (obj && obj.type === 'i-text') {
          fontFamily.value = obj.fontFamily || 'Arial';
          fontSize.value = obj.fontSize || 40;
          color.value = obj.fill || '#111111';
          opacity.value = obj.opacity || 1;
          angle.value = obj.angle || 0;
        }
      }

      canvas.on('selection:created', (e) => updateControls(e.selected[0]));
      canvas.on('selection:updated', (e) => updateControls(e.selected[0]));
      canvas.on('selection:cleared', () => updateControls(null));

      fontFamily.addEventListener('change', () => {
        const t = getActiveText(); if (!t) return;
        t.set('fontFamily', fontFamily.value); canvas.requestRenderAll();
      });
      fontSize.addEventListener('input', () => {
        const t = getActiveText(); if (!t) return;
        t.set('fontSize', parseInt(fontSize.value)); canvas.requestRenderAll();
      });
      color.addEventListener('input', () => {
        const t = getActiveText(); if (!t) return;
        t.set('fill', color.value); canvas.requestRenderAll();
      });
      opacity.addEventListener('input', () => {
        const obj = canvas.getActiveObject(); if (!obj) return;
        obj.set('opacity', parseFloat(opacity.value)); canvas.requestRenderAll();
      });
      angle.addEventListener('input', () => {
        const obj = canvas.getActiveObject(); if (!obj) return;
        obj.rotate(parseFloat(angle.value)); canvas.requestRenderAll();
      });
      boldBtn.addEventListener('click', () => {
        const t = getActiveText(); if (!t) return;
        t.set('fontWeight', t.fontWeight === 'bold' ? 'normal' : 'bold'); canvas.requestRenderAll();
      });
      italicBtn.addEventListener('click', () => {
        const t = getActiveText(); if (!t) return;
        t.set('fontStyle', t.fontStyle === 'italic' ? 'normal' : 'italic'); canvas.requestRenderAll();
      });
      underlineBtn.addEventListener('click', () => {
        const t = getActiveText(); if (!t) return;
        t.set('underline', !t.underline); canvas.requestRenderAll();
      });

      const templateType = document.getElementById('templateType');
const loadTemplateBtn = document.getElementById('loadTemplate');
const saveTemplateBtn = document.getElementById('saveTemplate');

// Load template
loadTemplateBtn.addEventListener('click', () => {
  const type = templateType.value;
  fetch(`/get-header-template/${type}`)
    .then(res => res.json())
    .then(data => {
      if (data.template_json) {
        canvas.loadFromJSON(data.template_json, () => canvas.renderAll());
      } else {
        canvas.clear();
        canvas.setBackgroundColor('#ffffff');
      }
    })
    .catch(err => console.error('Failed to load template:', err));
});

// Save template
saveTemplateBtn.addEventListener('click', () => {
  const type = templateType.value;
  const image = canvas.toDataURL({ format: 'png', multiplier: 2 });
  const template = JSON.stringify(canvas.toJSON());

  fetch('/save-header-image', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ image, template, type })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      alert(`✅ ${type.toUpperCase()} template saved successfully!`);
    } else {
      alert('❌ Failed to save template.');
    }
  })
  .catch(err => console.error('Save error:', err));
});

      // Default sample
      canvas.add(new fabric.IText('Welcome! Add images or text.', {
        left: 60, top: 80, fontSize: 28, fill: '#111'
      }));
    });
  </script>
</div>
