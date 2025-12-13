<div id="canvasBuilderApp" class="flex flex-col h-screen bg-gray-100 font-sans">
  
  <!-- Top Toolbar -->
  <div class="flex flex-wrap items-center gap-2 p-2 bg-white border-b border-gray-200 shadow-sm">
    <!-- Template Controls -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200">
      <select id="templateType" class="px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="prescription">Prescription</option>
        <option value="certificate">Certificate</option>
      </select>
      <button id="loadTemplate" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
        <span class="hidden sm:inline">Load</span>
      </button>
      <button id="saveTemplate" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span class="hidden sm:inline">Save</span>
      </button>
    </div>

    <!-- Undo/Redo -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200">
      <button id="undo" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100 disabled:opacity-40" title="Undo (Ctrl+Z)" disabled>
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M3 13c0-4.97 4.03-9 9-9s9 4.03 9 9-4.03 9-9 9c-2.12 0-4.07-.74-5.61-1.97"/></svg>
      </button>
      <button id="redo" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100 disabled:opacity-40" title="Redo (Ctrl+Y)" disabled>
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M21 13c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.12 0 4.07-.74 5.61-1.97"/></svg>
      </button>
    </div>

    <!-- Add Objects -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200">
      <button id="uploadBtn" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Add Image">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        <span class="hidden md:inline">Image</span>
      </button>
      <input id="file" type="file" accept="image/*" class="hidden" />
      <button id="addText" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Add Text">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
        <span class="hidden md:inline">Text</span>
      </button>
      <button id="addRect" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Rectangle">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
      </button>
      <button id="addCircle" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Circle">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
      </button>
    </div>

    <!-- Clipboard -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200">
      <button id="copy" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Copy (Ctrl+C)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
      </button>
      <button id="paste" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Paste (Ctrl+V)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg>
      </button>
      <button id="duplicate" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Duplicate (Ctrl+D)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg>
      </button>
    </div>

    <!-- Group -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200">
      <button id="group" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Group (Ctrl+G)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      </button>
      <button id="ungroup" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Ungroup">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
      </button>
    </div>

    <!-- Alignment - Hidden on small screens -->
    <div class="items-center hidden gap-1 pr-2 border-r border-gray-200 xl:flex">
      <button id="alignLeftCanvas" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Align Left">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="4" x2="4" y2="20"/><rect x="8" y="6" width="12" height="4"/><rect x="8" y="14" width="8" height="4"/></svg>
      </button>
      <button id="alignCenterH" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Center H">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="22"/><rect x="5" y="6" width="14" height="4"/><rect x="7" y="14" width="10" height="4"/></svg>
      </button>
      <button id="alignRightCanvas" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Align Right">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="20" y1="4" x2="20" y2="20"/><rect x="4" y="6" width="12" height="4"/><rect x="8" y="14" width="8" height="4"/></svg>
      </button>
      <button id="alignTop" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Align Top">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="4" x2="20" y2="4"/><rect x="6" y="8" width="4" height="12"/><rect x="14" y="8" width="4" height="8"/></svg>
      </button>
      <button id="alignCenterV" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Center V">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="12" x2="22" y2="12"/><rect x="6" y="5" width="4" height="14"/><rect x="14" y="7" width="4" height="10"/></svg>
      </button>
      <button id="alignBottom" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Align Bottom">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="20" x2="20" y2="20"/><rect x="6" y="4" width="4" height="12"/><rect x="14" y="8" width="4" height="8"/></svg>
      </button>
    </div>

    <!-- Image Tools -->
    <div id="imageTools" class="flex items-center gap-1 pr-2 border-r border-gray-200 hidden">
      <button id="cropImage" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Crop Image">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2v4H2M18 22v-4h4M22 18H18V22M2 6h4V2M6 6v12h12M18 18V6H6"/></svg>
        <span class="hidden md:inline">Crop</span>
      </button>
    </div>

    <!-- Canvas Crop Tool -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200 hidden">
      <button id="cropCanvasBtn" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Crop Canvas">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg>
        <span class="hidden md:inline">Crop Canvas</span>
      </button>
    </div>

    <!-- Layer Order & Delete -->
    <div class="flex items-center gap-1 pr-2 border-r border-gray-200">
      <button id="bringForward" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Bring Forward">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="8" y="8" width="12" height="12" rx="1"/><rect x="4" y="4" width="12" height="12" rx="1" fill="white"/></svg>
      </button>
      <button id="sendBackward" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Send Backward">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="12" height="12" rx="1"/><rect x="8" y="8" width="12" height="12" rx="1" fill="white"/></svg>
      </button>
      <button id="delete" class="p-1.5 text-red-500 rounded-md hover:bg-red-50" title="Delete">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
      </button>
    </div>

    <div class="flex-1"></div>

    <!-- Ruler & Zoom -->
    <div class="flex items-center gap-2">
      <button id="resizeCanvas" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Resize Canvas">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 21H3V3"/><path d="M21 12V3h-9"/><path d="M21 3l-9 9"/>
        </svg>
      </button>
      <button id="toggleRulers" class="p-1.5 text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100" title="Toggle Rulers (R)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v6H3zM3 3v18h6V3"/><path d="M7 3v2M11 3v3M15 3v2M19 3v3M3 7h2M3 11h3M3 15h2M3 19h3"/></svg>
      </button>
      <select id="rulerUnit" class="hidden px-1 py-1 text-sm border border-gray-300 rounded-md sm:block">
        <option value="px">px</option>
        <option value="mm">mm</option>
        <option value="cm">cm</option>
      </select>
      <div class="items-center hidden gap-2 sm:flex">
        <input id="zoomRange" type="range" min="0.25" max="3" step="0.05" value="1" class="w-16 lg:w-24" />
        <span id="zoomValue" class="text-sm font-medium text-gray-600 w-10">100%</span>
      </div>
      <button id="toggleLeftPanel" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100 lg:hidden" title="Properties">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
      </button>
      <button id="toggleRightPanel" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100 lg:hidden" title="Layers">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="15" y1="3" x2="15" y2="21"/></svg>
      </button>
      <button id="helpBtn" class="p-1.5 text-gray-600 rounded-md hover:bg-gray-100" title="Shortcuts (?)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </button>
    </div>
  </div>

  <!-- Main Container -->
  <div class="flex flex-1 min-h-0">
    
    <!-- Left Panel -->
    <div id="leftPanel" class="flex-col hidden w-56 bg-white border-r border-gray-200 lg:flex xl:w-64 2xl:w-72">
      <div class="px-4 py-3 font-semibold text-gray-800 border-b border-gray-200">Properties</div>
      <div class="flex-1 p-3 space-y-3 overflow-y-auto">
        <div id="noSelection" class="text-sm text-gray-500">Select an object to edit</div>
        <div id="propertiesPanel" class="hidden space-y-3">
          <div>
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Position</label>
            <div class="grid grid-cols-2 gap-2">
              <input id="posX" type="number" placeholder="X" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
              <input id="posY" type="number" placeholder="Y" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
            </div>
          </div>
          <div>
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Size</label>
            <div class="grid grid-cols-2 gap-2">
              <input id="width" type="number" placeholder="W" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
              <input id="height" type="number" placeholder="H" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
            </div>
          </div>
          <div id="textProperties" class="space-y-3">
            <div>
              <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Font</label>
              <select id="fontFamily" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                <option value="Arial">Arial</option>
                <option value="Helvetica">Helvetica</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="Georgia">Georgia</option>
                <option value="Courier New">Courier New</option>
              </select>
            </div>
            <div>
              <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Size</label>
              <input id="fontSize" type="number" value="40" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
            </div>
            <div>
              <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Style</label>
              <div class="flex gap-1">
                <button id="bold" class="w-8 h-8 font-bold text-gray-700 border border-gray-300 rounded hover:bg-gray-50">B</button>
                <button id="italic" class="w-8 h-8 italic text-gray-700 border border-gray-300 rounded hover:bg-gray-50">I</button>
                <button id="underline" class="w-8 h-8 text-gray-700 underline border border-gray-300 rounded hover:bg-gray-50">U</button>
              </div>
            </div>
          </div>
          <div>
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Fill</label>
            <input id="fillColor" type="color" value="#111111" class="w-full h-8 border border-gray-300 rounded cursor-pointer" />
          </div>
          <div id="strokeField">
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Stroke</label>
            <input id="strokeColor" type="color" value="#000000" class="w-full h-8 border border-gray-300 rounded cursor-pointer" />
          </div>
          <div id="strokeWidthField">
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Stroke Width</label>
            <input id="strokeWidth" type="number" value="1" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
          </div>
          <div>
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Opacity: <span id="opacityValue">100%</span></label>
            <input id="opacity" type="range" min="0" max="1" step="0.01" value="1" class="w-full" />
          </div>
          <div>
            <label class="block mb-1 text-xs font-medium text-gray-500 uppercase">Rotation: <span id="angleValue">0°</span></label>
            <input id="angle" type="range" min="0" max="360" step="1" value="0" class="w-full" />
          </div>
        </div>
      </div>
    </div>

    <!-- Canvas Stage -->
    <div class="relative flex flex-col flex-1 min-w-0">
      <div class="relative flex-1 min-h-0">
        <!-- Ruler Corner -->
        <div id="rulerCorner" class="absolute top-0 left-0 z-10 flex items-center justify-center w-8 h-8 text-gray-400 bg-gray-100 border-b border-r border-gray-300 cursor-pointer hover:bg-gray-200">
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M3 9h6M3 15h4M9 3v6M15 3v4"/></svg>
        </div>
        <!-- Horizontal Ruler -->
        <div id="rulerH" class="absolute top-0 right-0 z-[5] h-8 overflow-hidden bg-gray-50 border-b border-gray-300" style="left: 32px;">
          <canvas id="rulerHCanvas" class="absolute top-0 left-0"></canvas>
        </div>
        <!-- Vertical Ruler -->
        <div id="rulerV" class="absolute bottom-0 left-0 z-[5] w-8 overflow-hidden bg-gray-50 border-r border-gray-300" style="top: 32px;">
          <canvas id="rulerVCanvas" class="absolute top-0 left-0"></canvas>
        </div>
        <!-- Guide Lines -->
        <div id="guideH" class="absolute left-8 right-0 h-px bg-blue-500 pointer-events-none z-[100] hidden"></div>
        <div id="guideV" class="absolute top-8 bottom-0 w-px bg-blue-500 pointer-events-none z-[100] hidden"></div>
        <!-- Canvas Area -->
        <div id="canvasScrollArea" class="absolute right-0 bottom-0 overflow-auto bg-gray-100" style="top: 32px; left: 32px;">
          <div class="flex items-center justify-center min-w-full min-h-full p-4 sm:p-8 lg:p-12">
            <div id="canvas-container" class="bg-white rounded shadow-lg ring-1 ring-black/5" style="transform-origin: center center;">
              <canvas id="c" width="794" height="400"></canvas>
            </div>
          </div>
        </div>
      </div>
      <!-- Status Bar -->
      <div class="flex items-center gap-4 px-3 py-1 text-xs text-gray-500 bg-white border-t border-gray-200">
        <span>Canvas: <span id="canvasSize">794×400</span></span>
        <span>Objects: <span id="objectCount">0</span></span>
        <span id="selectionInfo" class="hidden sm:inline">No selection</span>
        <span class="flex-1"></span>
        <span class="hidden md:inline">Shift+Click multi-select | ? shortcuts</span>
      </div>
    </div>

    <!-- Right Panel -->
    <div id="rightPanel" class="flex-col hidden w-48 bg-white border-l border-gray-200 lg:flex xl:w-56">
      <div class="flex items-center justify-between px-4 py-3 font-semibold text-gray-800 border-b border-gray-200">
        Layers
        <button id="addLayerBtn" class="p-1 text-gray-400 rounded hover:bg-gray-100 hover:text-gray-600">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </button>
      </div>
      <div id="layersList" class="flex-1 p-2 space-y-1 overflow-y-auto"></div>
    </div>
  </div>

  <!-- Mobile Left Panel -->
  <div id="mobileLeftPanel" class="fixed inset-y-0 left-0 z-50 flex-col hidden w-72 max-w-full bg-white shadow-xl lg:hidden">
    <div class="flex items-center justify-between px-4 py-3 font-semibold text-gray-800 border-b border-gray-200">
      Properties
      <button id="closeMobileLeft" class="p-1 text-gray-400 rounded hover:bg-gray-100">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div id="mobilePropertiesContent" class="flex-1 p-3 space-y-3 overflow-y-auto"></div>
  </div>

  <!-- Mobile Right Panel -->
  <div id="mobileRightPanel" class="fixed inset-y-0 right-0 z-50 flex-col hidden w-64 max-w-full bg-white shadow-xl lg:hidden">
    <div class="flex items-center justify-between px-4 py-3 font-semibold text-gray-800 border-b border-gray-200">
      Layers
      <button id="closeMobileRight" class="p-1 text-gray-400 rounded hover:bg-gray-100">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div id="mobileLayersContent" class="flex-1 p-2 space-y-1 overflow-y-auto"></div>
  </div>

  <!-- Backdrop -->
  <div id="panelBackdrop" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

  <!-- Context Menu -->
  <div id="contextMenu" class="fixed z-[1000] hidden min-w-[160px] bg-white border border-gray-200 rounded-lg shadow-lg py-1 text-sm">
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="copy">Copy <span class="float-right text-gray-400">⌘C</span></div>
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="paste">Paste <span class="float-right text-gray-400">⌘V</span></div>
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="duplicate">Duplicate <span class="float-right text-gray-400">⌘D</span></div>
    <div class="h-px my-1 bg-gray-200"></div>
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="group">Group <span class="float-right text-gray-400">⌘G</span></div>
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="ungroup">Ungroup</div>
    <div class="h-px my-1 bg-gray-200"></div>
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="bringForward">Bring Forward</div>
    <div class="px-3 py-1.5 cursor-pointer hover:bg-gray-100" data-action="sendBackward">Send Backward</div>
    <div class="h-px my-1 bg-gray-200"></div>
    <div class="px-3 py-1.5 text-red-500 cursor-pointer hover:bg-red-50" data-action="delete">Delete <span class="float-right text-red-400">⌫</span></div>
  </div>

  <!-- Shortcuts Modal -->
  <div id="shortcutsModal" class="fixed inset-0 z-[1001] flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-sm p-5 bg-white rounded-xl max-h-[80vh] overflow-y-auto">
      <h2 class="mb-4 text-lg font-semibold">Keyboard Shortcuts</h2>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between"><span>Undo</span><span class="text-gray-400">Ctrl+Z</span></div>
        <div class="flex justify-between"><span>Redo</span><span class="text-gray-400">Ctrl+Y</span></div>
        <div class="flex justify-between"><span>Copy</span><span class="text-gray-400">Ctrl+C</span></div>
        <div class="flex justify-between"><span>Paste</span><span class="text-gray-400">Ctrl+V</span></div>
        <div class="flex justify-between"><span>Duplicate</span><span class="text-gray-400">Ctrl+D</span></div>
        <div class="flex justify-between"><span>Group</span><span class="text-gray-400">Ctrl+G</span></div>
        <div class="flex justify-between"><span>Ungroup</span><span class="text-gray-400">Ctrl+Shift+G</span></div>
        <div class="flex justify-between"><span>Select All</span><span class="text-gray-400">Ctrl+A</span></div>
        <div class="flex justify-between"><span>Delete</span><span class="text-gray-400">Delete</span></div>
        <div class="flex justify-between"><span>Move</span><span class="text-gray-400">Arrow Keys</span></div>
        <div class="flex justify-between"><span>Fine Move</span><span class="text-gray-400">Shift+Arrow</span></div>
        <div class="flex justify-between"><span>Toggle Rulers</span><span class="text-gray-400">R</span></div>
      </div>
      <button id="closeModal" class="w-full px-4 py-2 mt-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Close</button>
    </div>
  </div>

  <!-- Canvas Resize Modal -->
  <div id="resizeModal" class="fixed inset-0 z-[1001] flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-md p-5 bg-white rounded-xl">
      <h2 class="mb-4 text-lg font-semibold">Resize Canvas</h2>
      
      <!-- Preset Sizes -->
      <div class="mb-4">
        <label class="block mb-2 text-xs font-medium text-gray-500 uppercase">Preset Sizes</label>
        <div class="grid grid-cols-2 gap-2">
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="794" data-height="1123">
            <span class="font-medium">A4 Portrait</span>
            <span class="text-gray-400 text-xs block">794 × 1123 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="1123" data-height="794">
            <span class="font-medium">A4 Landscape</span>
            <span class="text-gray-400 text-xs block">1123 × 794 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="816" data-height="1056">
            <span class="font-medium">Letter Portrait</span>
            <span class="text-gray-400 text-xs block">816 × 1056 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="1056" data-height="816">
            <span class="font-medium">Letter Landscape</span>
            <span class="text-gray-400 text-xs block">1056 × 816 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="1920" data-height="1080">
            <span class="font-medium">HD 1080p</span>
            <span class="text-gray-400 text-xs block">1920 × 1080 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="1080" data-height="1080">
            <span class="font-medium">Square</span>
            <span class="text-gray-400 text-xs block">1080 × 1080 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="794" data-height="400">
            <span class="font-medium">Header Banner</span>
            <span class="text-gray-400 text-xs block">794 × 400 px</span>
          </button>
          <button class="preset-btn px-3 py-2 text-sm text-left border border-gray-300 rounded-lg hover:bg-gray-50" data-width="794" data-height="224">
            <span class="font-medium">Prescription Header</span>
            <span class="text-gray-400 text-xs block">794 × 224 px</span>
          </button>
        </div>
      </div>

      <!-- Custom Size -->
      <div class="mb-4">
        <label class="block mb-2 text-xs font-medium text-gray-500 uppercase">Custom Size (pixels)</label>
        <div class="flex items-center gap-2">
          <div class="flex-1">
            <label class="block mb-1 text-xs text-gray-500">Width</label>
            <input id="canvasWidth" type="number" value="794" min="100" max="4000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
          </div>
          <button id="swapDimensions" class="p-2 mt-5 text-gray-500 rounded-lg hover:bg-gray-100" title="Swap dimensions">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
          </button>
          <div class="flex-1">
            <label class="block mb-1 text-xs text-gray-500">Height</label>
            <input id="canvasHeight" type="number" value="400" min="100" max="4000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
          </div>
        </div>
      </div>

      <!-- Lock Aspect Ratio -->
      <div class="flex items-center gap-2 mb-4">
        <input id="lockAspectRatio" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
        <label for="lockAspectRatio" class="text-sm text-gray-600">Lock aspect ratio</label>
      </div>

      <!-- Preview -->
      <div class="p-3 mb-4 bg-gray-100 rounded-lg">
        <div class="text-xs text-gray-500 mb-2">Preview</div>
        <div class="flex items-center justify-center h-24">
          <div id="resizePreview" class="bg-white border-2 border-dashed border-gray-300 transition-all duration-200" style="width: 100px; height: 50px;"></div>
        </div>
        <div id="resizePreviewSize" class="text-center text-xs text-gray-500 mt-2">794 × 400 px</div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2">
        <button id="cancelResize" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
        <button id="applyResize" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Apply</button>
      </div>
    </div>
  </div>

  <!-- Crop Modal -->
  <div id="cropModal" class="fixed inset-0 z-[1001] flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-3xl p-5 bg-white rounded-xl max-h-[90vh] overflow-hidden flex flex-col">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Crop Image</h2>
        <button id="closeCropModal" class="p-1 text-gray-400 rounded hover:bg-gray-100">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <!-- Crop Canvas Area -->
      <div class="relative flex-1 min-h-0 mb-4 overflow-auto bg-gray-100 rounded-lg">
        <div class="flex items-center justify-center min-h-[300px] p-4">
          <canvas id="cropCanvas"></canvas>
        </div>
      </div>

      <!-- Crop Controls -->
      <div class="flex flex-wrap items-center gap-4 mb-4">
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Aspect Ratio:</label>
          <select id="cropAspectRatio" class="px-2 py-1 text-sm border border-gray-300 rounded-md">
            <option value="free">Free</option>
            <option value="1:1">1:1 (Square)</option>
            <option value="4:3">4:3</option>
            <option value="16:9">16:9</option>
            <option value="3:2">3:2</option>
            <option value="2:1">2:1</option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-500">Selection: <span id="cropSelectionSize">0 × 0</span></span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2">
        <button id="cancelCrop" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
        <button id="resetCrop" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Reset</button>
        <button id="applyCrop" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Apply Crop</button>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div id="toast" class="fixed left-1/2 -translate-x-1/2 bottom-16 z-[1002] px-4 py-2 text-sm text-white bg-gray-800 rounded-lg opacity-0 transition-all duration-300 -translate-y-4"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
function canvasBuilder() {
  return {
    canvas: null, clipboard: null, historyStack: [], historyIndex: -1, isHistoryAction: false,
    rulersVisible: true, guidesEnabled: false, rulerUnit: 'px', currentZoom: 1, aspectRatio: null,
    unitConversions: { px: 1, mm: 96/25.4, cm: 96/2.54, in: 96 },
    // Crop properties
    cropCanvas: null, cropImage: null, cropRect: null, originalImage: null, cropAspectRatio: null,

    init() {
      this.canvas = new fabric.Canvas('c', {
        backgroundColor: '#ffffff', preserveObjectStacking: true, selection: true,
        selectionBorderColor: '#3b82f6', selectionColor: 'rgba(59,130,246,0.1)', selectionLineWidth: 1,
      });
      this.setupEvents();
      // Add welcome text first, then save initial history
      this.canvas.add(new fabric.IText('Welcome! Add images or text.', { left: 60, top: 80, fontSize: 28, fill: '#111', fontFamily: 'Arial' }));
      this.canvas.renderAll();
      this.saveHistory();
      this.updateLayers();
      setTimeout(() => this.drawRulers(), 100);
      window.addEventListener('resize', () => this.drawRulers());
    },

    $(id) { return document.getElementById(id); },

    setupEvents() {
      // Toolbar
      this.$('uploadBtn').onclick = () => this.$('file').click();
      this.$('file').onchange = (e) => this.handleImage(e);
      this.$('addText').onclick = () => this.addText();
      this.$('addRect').onclick = () => this.addRect();
      this.$('addCircle').onclick = () => this.addCircle();
      this.$('addLayerBtn').onclick = () => this.addText();
      this.$('undo').onclick = () => this.undo();
      this.$('redo').onclick = () => this.redo();
      this.$('copy').onclick = () => this.copy();
      this.$('paste').onclick = () => this.paste();
      this.$('duplicate').onclick = () => this.duplicate();
      this.$('group').onclick = () => this.group();
      this.$('ungroup').onclick = () => this.ungroup();
      this.$('bringForward').onclick = () => { const o = this.canvas.getActiveObject(); if(o) { this.canvas.bringForward(o); this.updateLayers(); this.saveHistory(); }};
      this.$('sendBackward').onclick = () => { const o = this.canvas.getActiveObject(); if(o) { this.canvas.sendBackwards(o); this.updateLayers(); this.saveHistory(); }};
      this.$('delete').onclick = () => this.deleteSelected();
      ['alignLeftCanvas','alignCenterH','alignRightCanvas','alignTop','alignCenterV','alignBottom'].forEach(id => {
        const el = this.$(id); if(el) el.onclick = () => this.align(id.replace('Canvas','').replace('align','').toLowerCase());
      });
      this.$('toggleRulers').onclick = () => this.toggleRulers();
      this.$('rulerCorner').onclick = () => this.toggleRulers();
      this.$('rulerUnit').onchange = (e) => { this.rulerUnit = e.target.value; this.drawRulers(); };
      this.$('zoomRange').oninput = (e) => {
        this.currentZoom = parseFloat(e.target.value);
        this.$('canvas-container').style.transform = `scale(${this.currentZoom})`;
        this.$('zoomValue').textContent = Math.round(this.currentZoom * 100) + '%';
        this.drawRulers();
      };
      // Properties
      this.$('posX').onchange = (e) => this.setProp('left', +e.target.value);
      this.$('posY').onchange = (e) => this.setProp('top', +e.target.value);
      this.$('width').onchange = (e) => { const o = this.canvas.getActiveObject(); if(o) { o.set('scaleX', +e.target.value/o.width); this.canvas.requestRenderAll(); this.saveHistory(); }};
      this.$('height').onchange = (e) => { const o = this.canvas.getActiveObject(); if(o) { o.set('scaleY', +e.target.value/o.height); this.canvas.requestRenderAll(); this.saveHistory(); }};
      this.$('fontFamily').onchange = (e) => this.setTextProp('fontFamily', e.target.value);
      this.$('fontSize').onchange = (e) => this.setTextProp('fontSize', +e.target.value);
      this.$('bold').onclick = () => this.toggleStyle('fontWeight', 'bold', 'normal');
      this.$('italic').onclick = () => this.toggleStyle('fontStyle', 'italic', 'normal');
      this.$('underline').onclick = () => this.toggleStyle('underline', true, false);
      this.$('fillColor').oninput = (e) => this.setProp('fill', e.target.value, false);
      this.$('fillColor').onchange = () => this.saveHistory();
      this.$('strokeColor').oninput = (e) => this.setProp('stroke', e.target.value, false);
      this.$('strokeColor').onchange = () => this.saveHistory();
      this.$('strokeWidth').onchange = (e) => this.setProp('strokeWidth', +e.target.value);
      this.$('opacity').oninput = (e) => { this.setProp('opacity', +e.target.value, false); this.$('opacityValue').textContent = Math.round(e.target.value*100)+'%'; };
      this.$('opacity').onchange = () => this.saveHistory();
      this.$('angle').oninput = (e) => { const o = this.canvas.getActiveObject(); if(o) { o.rotate(+e.target.value); this.canvas.requestRenderAll(); this.$('angleValue').textContent = e.target.value+'°'; }};
      this.$('angle').onchange = () => this.saveHistory();
      // Mobile panels
      this.$('toggleLeftPanel').onclick = () => this.openMobilePanel('left');
      this.$('toggleRightPanel').onclick = () => this.openMobilePanel('right');
      this.$('closeMobileLeft').onclick = () => this.closeMobilePanels();
      this.$('closeMobileRight').onclick = () => this.closeMobilePanels();
      this.$('panelBackdrop').onclick = () => this.closeMobilePanels();
      // Modal
      this.$('helpBtn').onclick = () => this.$('shortcutsModal').classList.remove('hidden');
      this.$('closeModal').onclick = () => this.$('shortcutsModal').classList.add('hidden');
      this.$('shortcutsModal').onclick = (e) => { if(e.target === this.$('shortcutsModal')) this.$('shortcutsModal').classList.add('hidden'); };
      
      // Resize Canvas Modal
      this.$('resizeCanvas').onclick = () => this.openResizeModal();
      this.$('cancelResize').onclick = () => this.$('resizeModal').classList.add('hidden');
      this.$('resizeModal').onclick = (e) => { if(e.target === this.$('resizeModal')) this.$('resizeModal').classList.add('hidden'); };
      this.$('applyResize').onclick = () => this.applyCanvasResize();
      this.$('swapDimensions').onclick = () => this.swapCanvasDimensions();

      // Crop Modal
      this.$('cropImage').onclick = () => this.openCropModal();
      this.$('closeCropModal').onclick = () => this.closeCropModal();
      this.$('cancelCrop').onclick = () => this.closeCropModal();
      this.$('cropModal').onclick = (e) => { if(e.target === this.$('cropModal')) this.closeCropModal(); };
      this.$('applyCrop').onclick = () => this.applyCrop();
      this.$('resetCrop').onclick = () => this.resetCrop();
      this.$('cropAspectRatio').onchange = (e) => this.setCropAspectRatio(e.target.value);
      
      // Preset buttons
      document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.onclick = () => {
          const w = parseInt(btn.dataset.width);
          const h = parseInt(btn.dataset.height);
          this.$('canvasWidth').value = w;
          this.$('canvasHeight').value = h;
          this.updateResizePreview();
          // Highlight selected preset
          document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50'));
          btn.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
        };
      });
      
      // Width/Height inputs
      this.$('canvasWidth').oninput = () => this.handleResizeInput('width');
      this.$('canvasHeight').oninput = () => this.handleResizeInput('height');
      
      // Context menu
      this.$('c').oncontextmenu = (e) => { e.preventDefault(); this.$('contextMenu').style.left = e.clientX+'px'; this.$('contextMenu').style.top = e.clientY+'px'; this.$('contextMenu').classList.remove('hidden'); };
      document.onclick = () => this.$('contextMenu').classList.add('hidden');
      this.$('contextMenu').querySelectorAll('[data-action]').forEach(el => {
        el.onclick = () => { this.handleContextAction(el.dataset.action); this.$('contextMenu').classList.add('hidden'); };
      });
      // Canvas scroll
      this.$('canvasScrollArea').onscroll = () => this.drawRulers();
      // Canvas events
      this.canvas.on('selection:created', (e) => { this.updateProps(e.selected[0]); this.updateLayers(); this.updateImageTools(e.selected[0]); });
      this.canvas.on('selection:updated', (e) => { this.updateProps(e.selected[0]); this.updateLayers(); this.updateImageTools(e.selected[0]); });
      this.canvas.on('selection:cleared', () => { this.updateProps(null); this.updateLayers(); this.updateImageTools(null); });
      this.canvas.on('object:modified', () => { 
        if(!this.isHistoryAction) {
          this.saveHistory(); 
          this.updateProps(this.canvas.getActiveObject()); 
        }
      });
      this.canvas.on('object:moving', () => this.updateProps(this.canvas.getActiveObject()));
      this.canvas.on('object:added', () => { 
        if(!this.isHistoryAction) this.updateLayers(); 
      });
      this.canvas.on('object:removed', () => { 
        if(!this.isHistoryAction) this.updateLayers(); 
      });
      // Keyboard
      document.onkeydown = (e) => this.handleKey(e);
      document.onkeyup = (e) => { if(['ArrowUp','ArrowDown','ArrowLeft','ArrowRight'].includes(e.key)) this.saveHistory(); };
      // Templates
      this.$('loadTemplate').onclick = () => this.loadTemplate();
      this.$('saveTemplate').onclick = () => this.saveTemplate();
    },

    // History
    saveHistory() {
      if(this.isHistoryAction) return;
      // Include canvas dimensions in history
      const historyData = {
        width: this.canvas.getWidth(),
        height: this.canvas.getHeight(),
        json: this.canvas.toJSON()
      };
      const json = JSON.stringify(historyData);
      // Don't save if nothing changed
      if(this.historyStack.length > 0 && this.historyStack[this.historyIndex] === json) return;
      // Remove future history if we're not at the end
      if(this.historyIndex < this.historyStack.length - 1) {
        this.historyStack = this.historyStack.slice(0, this.historyIndex + 1);
      }
      this.historyStack.push(json);
      if(this.historyStack.length > 50) {
        this.historyStack.shift();
      } else {
        this.historyIndex++;
      }
      this.updateHistoryButtons();
    },
    undo() { 
      if(this.historyIndex <= 0) return;
      this.isHistoryAction = true; 
      this.historyIndex--; 
      const state = JSON.parse(this.historyStack[this.historyIndex]);
      
      // Restore canvas dimensions
      if (state.width && state.height) {
        this.canvas.setWidth(state.width);
        this.canvas.setHeight(state.height);
        this.$('canvasSize').textContent = `${state.width}×${state.height}`;
      }
      
      // Restore canvas content
      const jsonData = state.json || state;
      this.canvas.loadFromJSON(jsonData, () => { 
        this.canvas.renderAll(); 
        this.isHistoryAction = false; 
        this.updateHistoryButtons();
        this.updateLayers();
        this.drawRulers();
        this.toast('Undo'); 
      });
    },
    redo() { 
      if(this.historyIndex >= this.historyStack.length - 1) return;
      this.isHistoryAction = true; 
      this.historyIndex++; 
      const state = JSON.parse(this.historyStack[this.historyIndex]);
      
      // Restore canvas dimensions
      if (state.width && state.height) {
        this.canvas.setWidth(state.width);
        this.canvas.setHeight(state.height);
        this.$('canvasSize').textContent = `${state.width}×${state.height}`;
      }
      
      // Restore canvas content
      const jsonData = state.json || state;
      this.canvas.loadFromJSON(jsonData, () => { 
        this.canvas.renderAll(); 
        this.isHistoryAction = false; 
        this.updateHistoryButtons();
        this.updateLayers();
        this.drawRulers();
        this.toast('Redo'); 
      });
    },
    updateHistoryButtons() {
      this.$('undo').disabled = this.historyIndex <= 0;
      this.$('redo').disabled = this.historyIndex >= this.historyStack.length - 1;
    },

    // Clipboard
    copy() { const o = this.canvas.getActiveObject(); if(!o) return this.toast('Nothing to copy'); o.clone((c) => { this.clipboard = c; this.toast('Copied'); }); },
    paste() { if(!this.clipboard) return this.toast('Nothing to paste'); this.clipboard.clone((c) => { this.canvas.discardActiveObject(); c.set({ left: c.left+20, top: c.top+20, evented: true }); if(c.type === 'activeSelection') { c.canvas = this.canvas; c.forEachObject((o) => this.canvas.add(o)); } else { this.canvas.add(c); } this.clipboard.left += 20; this.clipboard.top += 20; this.canvas.setActiveObject(c); this.canvas.requestRenderAll(); this.saveHistory(); this.updateLayers(); this.toast('Pasted'); }); },
    duplicate() { const o = this.canvas.getActiveObject(); if(!o) return this.toast('Nothing to duplicate'); o.clone((c) => { c.set({ left: o.left+20, top: o.top+20, evented: true }); this.canvas.add(c); this.canvas.setActiveObject(c); this.canvas.requestRenderAll(); this.saveHistory(); this.updateLayers(); this.toast('Duplicated'); }); },

    // Group
    group() { const o = this.canvas.getActiveObject(); if(!o || o.type !== 'activeSelection') return this.toast('Select multiple objects'); o.toGroup(); this.canvas.requestRenderAll(); this.saveHistory(); this.updateLayers(); this.toast('Grouped'); },
    ungroup() { const o = this.canvas.getActiveObject(); if(!o || o.type !== 'group') return this.toast('Select a group'); o.toActiveSelection(); this.canvas.requestRenderAll(); this.saveHistory(); this.updateLayers(); this.toast('Ungrouped'); },

    // Align
    align(dir) {
      const o = this.canvas.getActiveObject(); if(!o) return;
      const cw = this.canvas.getWidth(), ch = this.canvas.getHeight();
      if(o.type !== 'activeSelection') {
        switch(dir) {
          case 'left': o.set('left', 0); break;
          case 'centerh': o.set('left', (cw - o.getScaledWidth()) / 2); break;
          case 'right': o.set('left', cw - o.getScaledWidth()); break;
          case 'top': o.set('top', 0); break;
          case 'centerv': o.set('top', (ch - o.getScaledHeight()) / 2); break;
          case 'bottom': o.set('top', ch - o.getScaledHeight()); break;
        }
        o.setCoords(); this.canvas.requestRenderAll(); this.saveHistory(); this.updateProps(o); return;
      }
      const objs = o.getObjects(), b = o.getBoundingRect();
      objs.forEach(obj => {
        const ob = obj.getBoundingRect(true); let l = obj.left, t = obj.top;
        switch(dir) {
          case 'left': l = obj.left - ob.left + b.left; break;
          case 'centerh': l = obj.left - ob.left + b.left + (b.width - ob.width) / 2; break;
          case 'right': l = obj.left - ob.left + b.left + b.width - ob.width; break;
          case 'top': t = obj.top - ob.top + b.top; break;
          case 'centerv': t = obj.top - ob.top + b.top + (b.height - ob.height) / 2; break;
          case 'bottom': t = obj.top - ob.top + b.top + b.height - ob.height; break;
        }
        obj.set({ left: l, top: t }); obj.setCoords();
      });
      this.canvas.requestRenderAll(); this.saveHistory(); this.toast('Aligned');
    },

    // Add objects
    addText() { const t = new fabric.IText('Double-click to edit', { left: 100, top: 100, fontFamily: 'Arial', fontSize: 40, fill: '#111' }); this.canvas.add(t).setActiveObject(t); this.saveHistory(); this.updateLayers(); },
    addRect() { const r = new fabric.Rect({ left: 100, top: 100, width: 150, height: 100, fill: '#3b82f6', stroke: '#1e40af', strokeWidth: 2 }); this.canvas.add(r).setActiveObject(r); this.saveHistory(); this.updateLayers(); },
    addCircle() { const c = new fabric.Circle({ left: 100, top: 100, radius: 60, fill: '#10b981', stroke: '#047857', strokeWidth: 2 }); this.canvas.add(c).setActiveObject(c); this.saveHistory(); this.updateLayers(); },
    handleImage(e) { const f = e.target.files[0]; if(!f) return; const r = new FileReader(); r.onload = (ev) => { fabric.Image.fromURL(ev.target.result, (img) => { const s = Math.min(this.canvas.width*0.8/img.width, this.canvas.height*0.8/img.height, 1); img.set({ left: 50, top: 50, scaleX: s, scaleY: s }); this.canvas.add(img).setActiveObject(img); this.saveHistory(); this.updateLayers(); this.toast('Image added'); }); }; r.readAsDataURL(f); e.target.value = ''; },
    deleteSelected() { const o = this.canvas.getActiveObject(); if(o) { if(o.type === 'activeSelection') { o.forEachObject((ob) => this.canvas.remove(ob)); this.canvas.discardActiveObject(); } else { this.canvas.remove(o); } this.canvas.requestRenderAll(); this.saveHistory(); this.updateLayers(); this.toast('Deleted'); }},

    // Properties
    setProp(p, v, save = true) { 
      const o = this.canvas.getActiveObject(); 
      if (!o) return;
      
      // Handle multiple selection
      if (o.type === 'activeSelection') {
        o.getObjects().forEach(obj => obj.set(p, v));
        this.canvas.requestRenderAll(); 
        if (save) this.saveHistory();
      } else {
        o.set(p, v); 
        this.canvas.requestRenderAll(); 
        if (save) this.saveHistory(); 
      }
    },
    setTextProp(p, v) { 
      const o = this.canvas.getActiveObject(); 
      if (!o) return;
      
      // Handle multiple selection
      if (o.type === 'activeSelection') {
        const textObjects = o.getObjects().filter(obj => obj.type === 'i-text' || obj.type === 'text');
        if (textObjects.length > 0) {
          textObjects.forEach(obj => obj.set(p, v));
          this.canvas.requestRenderAll(); 
          this.saveHistory();
        }
      } else if (o.type === 'i-text' || o.type === 'text') {
        o.set(p, v); 
        this.canvas.requestRenderAll(); 
        this.saveHistory(); 
      }
    },
    toggleStyle(p, on, off) { 
      const o = this.canvas.getActiveObject(); 
      if (!o) return;
      
      // Handle multiple selection
      if (o.type === 'activeSelection') {
        const textObjects = o.getObjects().filter(obj => obj.type === 'i-text' || obj.type === 'text');
        if (textObjects.length > 0) {
          textObjects.forEach(obj => obj.set(p, obj[p] === on ? off : on));
          this.canvas.requestRenderAll(); 
          this.saveHistory();
          this.updateProps(o);
        }
      } else if (o.type === 'i-text' || o.type === 'text') {
        o.set(p, o[p] === on ? off : on); 
        this.canvas.requestRenderAll(); 
        this.saveHistory(); 
        this.updateProps(o); 
      }
    },
    updateProps(o) {
      if(!o) { this.$('propertiesPanel').classList.add('hidden'); this.$('noSelection').classList.remove('hidden'); this.$('selectionInfo').textContent = 'No selection'; return; }
      this.$('propertiesPanel').classList.remove('hidden'); this.$('noSelection').classList.add('hidden');
      this.$('selectionInfo').textContent = o.type === 'activeSelection' ? `${o.getObjects().length} selected` : o.type === 'group' ? `Group` : `Selected: ${o.type}`;
      this.$('posX').value = Math.round(o.left); this.$('posY').value = Math.round(o.top);
      this.$('width').value = Math.round(o.getScaledWidth()); this.$('height').value = Math.round(o.getScaledHeight());
      
      // Check if selection contains text objects
      let isText = o.type === 'i-text' || o.type === 'text';
      let textObjects = [];
      
      if (o.type === 'activeSelection') {
        textObjects = o.getObjects().filter(obj => obj.type === 'i-text' || obj.type === 'text');
        isText = textObjects.length > 0;
      }
      
      this.$('textProperties').style.display = isText ? 'block' : 'none';
      
      if (isText) {
        if (o.type === 'activeSelection' && textObjects.length > 0) {
          // For multi-selection, show the first text object's properties as reference
          const firstText = textObjects[0];
          this.$('fontFamily').value = firstText.fontFamily || 'Arial';
          this.$('fontSize').value = firstText.fontSize || 40;
        } else {
          this.$('fontFamily').value = o.fontFamily || 'Arial'; 
          this.$('fontSize').value = o.fontSize || 40;
        }
      }
      this.$('fillColor').value = this.toHex(o.fill) || '#111';
      const hasStroke = !['i-text','text','image'].includes(o.type);
      this.$('strokeField').style.display = hasStroke ? 'block' : 'none';
      this.$('strokeWidthField').style.display = hasStroke ? 'block' : 'none';
      if(hasStroke) { this.$('strokeColor').value = this.toHex(o.stroke) || '#000'; this.$('strokeWidth').value = o.strokeWidth || 1; }
      this.$('opacity').value = o.opacity || 1; this.$('opacityValue').textContent = Math.round((o.opacity||1)*100)+'%';
      this.$('angle').value = o.angle || 0; this.$('angleValue').textContent = Math.round(o.angle||0)+'°';
    },
    toHex(c) { if(!c) return '#000'; if(c.startsWith('#')) return c; if(c.startsWith('rgb')) { const m = c.match(/\d+/g); if(m) return '#' + m.slice(0,3).map(x => (+x).toString(16).padStart(2,'0')).join(''); } return '#000'; },

    // Layers
    updateLayers() {
      const objs = this.canvas.getObjects(), list = this.$('layersList');
      list.innerHTML = ''; this.$('objectCount').textContent = objs.length;
      const active = this.canvas.getActiveObject();
      [...objs].reverse().forEach((o, ri) => {
        const idx = objs.length - 1 - ri;
        const sel = active === o || (active && active.type === 'activeSelection' && active.getObjects().includes(o));
        const item = document.createElement('div');
        item.className = `flex items-center gap-2 px-2 py-1.5 rounded cursor-pointer text-sm group ${sel ? 'bg-blue-50 ring-1 ring-blue-300' : 'hover:bg-gray-100'}`;
        let icon = '□', name = 'Object';
        if(o.type === 'i-text' || o.type === 'text') { icon = 'T'; name = o.text ? o.text.substring(0,12) : 'Text'; }
        else if(o.type === 'image') { icon = '🖼'; name = 'Image'; }
        else if(o.type === 'rect') { icon = '□'; name = 'Rectangle'; }
        else if(o.type === 'circle') { icon = '○'; name = 'Circle'; }
        else if(o.type === 'group') { icon = '📁'; name = `Group (${o._objects?.length||0})`; }
        item.innerHTML = `<span class="w-5 text-center text-gray-400">${icon}</span><span class="flex-1 truncate">${name}</span><div class="flex gap-0.5 opacity-0 group-hover:opacity-100"><button class="p-0.5 text-gray-400 hover:text-gray-600" data-action="vis">${o.visible !== false ? '👁' : '👁‍🗨'}</button><button class="p-0.5 text-gray-400 hover:text-gray-600" data-action="lock">${o.lockMovementX ? '🔒' : '🔓'}</button></div>`;
        item.onclick = (e) => {
          if(e.target.closest('[data-action]')) return;
          if(e.shiftKey) {
            const cur = this.canvas.getActiveObject();
            if(!cur) this.canvas.setActiveObject(o);
            else if(cur === o) this.canvas.discardActiveObject();
            else if(cur.type === 'activeSelection') { cur.getObjects().includes(o) ? cur.removeWithUpdate(o) : cur.addWithUpdate(o); if(cur.getObjects().length === 1) this.canvas.setActiveObject(cur.getObjects()[0]); }
            else { this.canvas.setActiveObject(new fabric.ActiveSelection([cur, o], { canvas: this.canvas })); }
          } else { this.canvas.setActiveObject(o); }
          this.canvas.requestRenderAll(); this.updateLayers();
        };
        item.querySelector('[data-action="vis"]').onclick = () => { o.visible = !o.visible; this.canvas.requestRenderAll(); this.updateLayers(); };
        item.querySelector('[data-action="lock"]').onclick = () => { const l = o.lockMovementX; o.lockMovementX = o.lockMovementY = o.lockRotation = o.lockScalingX = o.lockScalingY = !l; o.hasControls = l; this.canvas.requestRenderAll(); this.updateLayers(); };
        list.appendChild(item);
      });
    },

    // Rulers
    drawRulers() {
      if(!this.rulersVisible) return;
      const hC = this.$('rulerHCanvas'), vC = this.$('rulerVCanvas'), hCtx = hC.getContext('2d'), vCtx = vC.getContext('2d');
      const scrollArea = this.$('canvasScrollArea'), container = this.$('canvas-container');
      const sRect = scrollArea.getBoundingClientRect(), cRect = container.getBoundingClientRect();
      const cL = cRect.left - sRect.left + scrollArea.scrollLeft, cT = cRect.top - sRect.top + scrollArea.scrollTop;
      const cW = this.canvas.getWidth() * this.currentZoom, cH = this.canvas.getHeight() * this.currentZoom;
      hC.width = Math.max(sRect.width + scrollArea.scrollLeft, cL + cW + 100); hC.height = 32;
      vC.width = 32; vC.height = Math.max(sRect.height + scrollArea.scrollTop, cT + cH + 100);
      hC.style.left = -scrollArea.scrollLeft + 'px'; vC.style.top = -scrollArea.scrollTop + 'px';
      hCtx.clearRect(0, 0, hC.width, hC.height); vCtx.clearRect(0, 0, vC.width, vC.height);
      hCtx.fillStyle = vCtx.fillStyle = '#64748b'; hCtx.font = vCtx.font = '10px system-ui';
      const ppu = this.unitConversions[this.rulerUnit] * this.currentZoom;
      const int = this.getTickInt();
      // H
      hCtx.beginPath(); hCtx.strokeStyle = '#cbd5e1';
      hCtx.fillStyle = '#dbeafe'; hCtx.fillRect(cL, 0, cW, 32); hCtx.fillStyle = '#64748b';
      for(let u = Math.floor(-cL/ppu); u <= Math.ceil((hC.width-cL)/ppu); u += int.minor) {
        const x = cL + u * ppu; if(x < 0 || x > hC.width) continue;
        const maj = Math.abs(u % int.major) < 0.001;
        hCtx.moveTo(x+0.5, maj?12:22); hCtx.lineTo(x+0.5, 32);
        if(maj) hCtx.fillText(this.rulerUnit==='px'?u:u.toFixed(1), x+3, 20);
      }
      hCtx.stroke();
      // V
      vCtx.beginPath(); vCtx.strokeStyle = '#cbd5e1';
      vCtx.fillStyle = '#dbeafe'; vCtx.fillRect(0, cT, 32, cH); vCtx.fillStyle = '#64748b';
      for(let u = Math.floor(-cT/ppu); u <= Math.ceil((vC.height-cT)/ppu); u += int.minor) {
        const y = cT + u * ppu; if(y < 0 || y > vC.height) continue;
        const maj = Math.abs(u % int.major) < 0.001;
        vCtx.moveTo(maj?12:22, y+0.5); vCtx.lineTo(32, y+0.5);
        if(maj) { vCtx.save(); vCtx.translate(14, y+3); vCtx.rotate(-Math.PI/2); vCtx.fillText(this.rulerUnit==='px'?u:u.toFixed(1), 0, 0); vCtx.restore(); }
      }
      vCtx.stroke();
    },
    getTickInt() {
      const base = { px: [10,50,100], mm: [1,5,10], cm: [0.5,1,5], in: [0.125,0.5,1] }[this.rulerUnit];
      const ppu = this.unitConversions[this.rulerUnit] * this.currentZoom;
      for(let i = 0; i < base.length; i++) if(base[i] * ppu >= 30) return { minor: base[i], major: base[Math.min(i+1, base.length-1)] };
      return { minor: base[0], major: base[1] || base[0]*5 };
    },
    toggleRulers() {
      this.rulersVisible = !this.rulersVisible;
      this.$('rulerH').style.display = this.$('rulerV').style.display = this.$('rulerCorner').style.display = this.rulersVisible ? 'block' : 'none';
      this.$('rulerCorner').style.display = this.rulersVisible ? 'flex' : 'none';
      this.$('toggleRulers').classList.toggle('text-blue-600', this.rulersVisible);
      this.$('toggleRulers').classList.toggle('bg-blue-50', this.rulersVisible);
      this.$('canvasScrollArea').style.left = this.$('canvasScrollArea').style.top = this.rulersVisible ? '32px' : '0';
      if(this.rulersVisible) this.drawRulers();
    },

    // Mobile panels
    openMobilePanel(side) {
      this.$(side === 'left' ? 'mobileLeftPanel' : 'mobileRightPanel').classList.remove('hidden');
      this.$(side === 'left' ? 'mobileLeftPanel' : 'mobileRightPanel').classList.add('flex');
      this.$('panelBackdrop').classList.remove('hidden');
      if(side === 'right') this.$('mobileLayersContent').innerHTML = this.$('layersList').innerHTML;
    },
    closeMobilePanels() {
      this.$('mobileLeftPanel').classList.add('hidden'); this.$('mobileLeftPanel').classList.remove('flex');
      this.$('mobileRightPanel').classList.add('hidden'); this.$('mobileRightPanel').classList.remove('flex');
      this.$('panelBackdrop').classList.add('hidden');
    },

    // Context menu
    handleContextAction(a) {
      switch(a) {
        case 'copy': this.copy(); break;
        case 'paste': this.paste(); break;
        case 'duplicate': this.duplicate(); break;
        case 'group': this.group(); break;
        case 'ungroup': this.ungroup(); break;
        case 'bringForward': this.$('bringForward').click(); break;
        case 'sendBackward': this.$('sendBackward').click(); break;
        case 'delete': this.deleteSelected(); break;
      }
    },

    // Keyboard
    handleKey(e) {
      if(['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) return;
      const o = this.canvas.getActiveObject();
      if(e.ctrlKey || e.metaKey) {
        switch(e.key.toLowerCase()) {
          case 'z': e.preventDefault(); e.shiftKey ? this.redo() : this.undo(); break;
          case 'y': e.preventDefault(); this.redo(); break;
          case 'c': e.preventDefault(); this.copy(); break;
          case 'v': e.preventDefault(); this.paste(); break;
          case 'd': e.preventDefault(); this.duplicate(); break;
          case 'g': e.preventDefault(); e.shiftKey ? this.ungroup() : this.group(); break;
          case 'a': e.preventDefault(); this.canvas.discardActiveObject(); this.canvas.setActiveObject(new fabric.ActiveSelection(this.canvas.getObjects(), { canvas: this.canvas })); this.canvas.requestRenderAll(); break;
        }
      } else {
        switch(e.key) {
          case 'Delete': case 'Backspace': if(o) { e.preventDefault(); this.deleteSelected(); } break;
          case 'Escape': this.canvas.discardActiveObject(); this.canvas.requestRenderAll(); this.updateLayers(); break;
          case '?': this.$('shortcutsModal').classList.remove('hidden'); break;
          case 'r': this.toggleRulers(); break;
          case 'ArrowUp': if(o) { e.preventDefault(); o.set('top', o.top - (e.shiftKey ? 1 : 10)); this.canvas.requestRenderAll(); this.updateProps(o); } break;
          case 'ArrowDown': if(o) { e.preventDefault(); o.set('top', o.top + (e.shiftKey ? 1 : 10)); this.canvas.requestRenderAll(); this.updateProps(o); } break;
          case 'ArrowLeft': if(o) { e.preventDefault(); o.set('left', o.left - (e.shiftKey ? 1 : 10)); this.canvas.requestRenderAll(); this.updateProps(o); } break;
          case 'ArrowRight': if(o) { e.preventDefault(); o.set('left', o.left + (e.shiftKey ? 1 : 10)); this.canvas.requestRenderAll(); this.updateProps(o); } break;
        }
      }
    },

    // Templates
    loadTemplate() {
      const type = this.$('templateType').value;
      this.toast('Loading template...');
      
      fetch(`/get-header-template/${type}`)
        .then(r => {
          if (!r.ok) throw new Error('Network response was not ok');
          return r.json();
        })
        .then(d => { 
          if(d.template_json) {
            // Parse if it's a string
            let templateData = typeof d.template_json === 'string' ? JSON.parse(d.template_json) : d.template_json;
            
            // Clear current canvas first
            this.canvas.clear();
            this.canvas.setBackgroundColor('#ffffff');
            
            // Reset history
            this.historyStack = [];
            this.historyIndex = -1;
            
            // Check format: new format has width/height/json, old format has version/objects
            if (templateData.width && templateData.height && templateData.json) {
              // New format with dimensions
              this.canvas.setWidth(templateData.width);
              this.canvas.setHeight(templateData.height);
              this.$('canvasSize').textContent = `${templateData.width}×${templateData.height}`;
              
              this.canvas.loadFromJSON(templateData.json, () => { 
                this.canvas.renderAll(); 
                this.saveHistory(); 
                this.updateLayers(); 
                this.drawRulers();
                this.toast('Template loaded'); 
              });
            } else if (templateData.version && templateData.objects) {
              // Old format (raw Fabric.js JSON) - keep current canvas size
              // You can set a default size here if needed:
              // this.canvas.setWidth(794);
              // this.canvas.setHeight(224);
              
              this.canvas.loadFromJSON(templateData, () => { 
                this.canvas.renderAll();
                this.$('canvasSize').textContent = `${this.canvas.getWidth()}×${this.canvas.getHeight()}`;
                this.saveHistory(); 
                this.updateLayers(); 
                this.drawRulers();
                this.toast('Template loaded (legacy format)'); 
              });
            } else {
              // Unknown format, try to load as-is
              this.canvas.loadFromJSON(templateData, () => { 
                this.canvas.renderAll(); 
                this.saveHistory(); 
                this.updateLayers(); 
                this.drawRulers();
                this.toast('Template loaded'); 
              });
            }
          } else { 
            this.canvas.clear(); 
            this.canvas.setBackgroundColor('#ffffff'); 
            this.canvas.renderAll();
            this.historyStack = [];
            this.historyIndex = -1;
            this.saveHistory();
            this.updateLayers();
            this.toast('No template found'); 
          }
        })
        .catch((err) => {
          console.error('Load error:', err);
          this.toast('Load failed: ' + err.message);
        });
    },
    saveTemplate() {
      const type = this.$('templateType').value;
      const canvasData = {
        width: this.canvas.getWidth(),
        height: this.canvas.getHeight(),
        json: this.canvas.toJSON()
      };
      fetch('/save-header-image', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        body: JSON.stringify({ 
          image: this.canvas.toDataURL({ format: 'png', multiplier: 2 }), 
          template: JSON.stringify(canvasData), 
          type 
        })
      }).then(r => r.json()).then(d => this.toast(d.status === 'success' ? 'Saved!' : 'Save failed')).catch(() => this.toast('Save failed'));
    },

    // Canvas Resize
    openResizeModal() {
      // Set current canvas dimensions
      this.$('canvasWidth').value = this.canvas.getWidth();
      this.$('canvasHeight').value = this.canvas.getHeight();
      this.aspectRatio = this.canvas.getWidth() / this.canvas.getHeight();
      this.updateResizePreview();
      // Clear preset selection
      document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50'));
      // Show modal
      this.$('resizeModal').classList.remove('hidden');
    },
    
    handleResizeInput(changed) {
      const w = parseInt(this.$('canvasWidth').value) || 100;
      const h = parseInt(this.$('canvasHeight').value) || 100;
      
      if (this.$('lockAspectRatio').checked && this.aspectRatio) {
        if (changed === 'width') {
          this.$('canvasHeight').value = Math.round(w / this.aspectRatio);
        } else {
          this.$('canvasWidth').value = Math.round(h * this.aspectRatio);
        }
      }
      
      // Clear preset selection when typing custom values
      document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50'));
      this.updateResizePreview();
    },
    
    swapCanvasDimensions() {
      const w = this.$('canvasWidth').value;
      const h = this.$('canvasHeight').value;
      this.$('canvasWidth').value = h;
      this.$('canvasHeight').value = w;
      this.aspectRatio = parseInt(h) / parseInt(w);
      this.updateResizePreview();
    },
    
    updateResizePreview() {
      const w = parseInt(this.$('canvasWidth').value) || 100;
      const h = parseInt(this.$('canvasHeight').value) || 100;
      
      // Calculate preview size (max 150px in any dimension)
      const maxSize = 100;
      const scale = Math.min(maxSize / w, maxSize / h);
      const previewW = Math.max(20, w * scale);
      const previewH = Math.max(20, h * scale);
      
      const preview = this.$('resizePreview');
      preview.style.width = previewW + 'px';
      preview.style.height = previewH + 'px';
      
      this.$('resizePreviewSize').textContent = `${w} × ${h} px`;
    },
    
    applyCanvasResize() {
      const newWidth = parseInt(this.$('canvasWidth').value) || 794;
      const newHeight = parseInt(this.$('canvasHeight').value) || 400;
      
      // Validate dimensions
      if (newWidth < 100 || newWidth > 4000 || newHeight < 100 || newHeight > 4000) {
        this.toast('Size must be between 100 and 4000 pixels');
        return;
      }
      
      // Resize canvas
      this.canvas.setWidth(newWidth);
      this.canvas.setHeight(newHeight);
      this.canvas.renderAll();
      
      // Update status bar
      this.$('canvasSize').textContent = `${newWidth}×${newHeight}`;
      
      // Save to history
      this.saveHistory();
      
      // Redraw rulers
      this.drawRulers();
      
      // Close modal
      this.$('resizeModal').classList.add('hidden');
      
      this.toast(`Canvas resized to ${newWidth} × ${newHeight}`);
    },

    // Image Tools
    updateImageTools(obj) {
      const isImage = obj && obj.type === 'image';
      this.$('imageTools').classList.toggle('hidden', !isImage);
    },

    // Crop functionality
    openCropModal() {
      const activeObj = this.canvas.getActiveObject();
      if (!activeObj || activeObj.type !== 'image') {
        this.toast('Please select an image to crop');
        return;
      }

      this.originalImage = activeObj;
      this.$('cropModal').classList.remove('hidden');
      this.$('cropAspectRatio').value = 'free';
      this.cropAspectRatio = null;

      // Initialize crop canvas
      setTimeout(() => this.initCropCanvas(), 100);
    },

    initCropCanvas() {
      const canvasEl = this.$('cropCanvas');
      const container = canvasEl.parentElement;

      // Get the original image source
      const imgSrc = this.originalImage._element.src;

      // Create a temporary image to get original dimensions
      const tempImg = new Image();
      tempImg.onload = () => {
        // Calculate canvas size to fit container while maintaining aspect ratio
        const maxWidth = Math.min(container.clientWidth - 40, 800);
        const maxHeight = Math.min(400, window.innerHeight * 0.5);
        const scale = Math.min(maxWidth / tempImg.width, maxHeight / tempImg.height, 1);

        const canvasWidth = tempImg.width * scale;
        const canvasHeight = tempImg.height * scale;

        // Destroy existing crop canvas if any
        if (this.cropCanvas) {
          this.cropCanvas.dispose();
        }

        // Set canvas dimensions
        canvasEl.width = canvasWidth;
        canvasEl.height = canvasHeight;

        // Create crop canvas
        this.cropCanvas = new fabric.Canvas('cropCanvas', {
          backgroundColor: '#f3f4f6',
          selection: false
        });

        // Add the image
        fabric.Image.fromURL(imgSrc, (img) => {
          img.set({
            left: 0,
            top: 0,
            scaleX: scale,
            scaleY: scale,
            selectable: false,
            evented: false
          });
          this.cropImage = img;
          this.cropCanvas.add(img);

          // Create crop rectangle
          const rectWidth = canvasWidth * 0.8;
          const rectHeight = canvasHeight * 0.8;
          this.cropRect = new fabric.Rect({
            left: (canvasWidth - rectWidth) / 2,
            top: (canvasHeight - rectHeight) / 2,
            width: rectWidth,
            height: rectHeight,
            fill: 'rgba(0, 0, 0, 0)',
            stroke: '#3b82f6',
            strokeWidth: 2,
            strokeDashArray: [5, 5],
            cornerColor: '#3b82f6',
            cornerSize: 10,
            transparentCorners: false,
            hasRotatingPoint: false,
            lockRotation: true
          });

          this.cropCanvas.add(this.cropRect);
          this.cropCanvas.setActiveObject(this.cropRect);

          // Add dark overlay outside crop area
          this.updateCropOverlay();

          // Update overlay on crop rect modification
          this.cropRect.on('moving', () => this.constrainCropRect());
          this.cropRect.on('scaling', () => this.constrainCropRect());
          this.cropRect.on('modified', () => this.updateCropOverlay());

          this.cropCanvas.renderAll();
          this.updateCropSelectionSize();
        }, { crossOrigin: 'anonymous' });
      };
      tempImg.src = imgSrc;
    },

    constrainCropRect() {
      const rect = this.cropRect;
      const img = this.cropImage;
      if (!rect || !img) return;

      const imgWidth = img.width * img.scaleX;
      const imgHeight = img.height * img.scaleY;

      // Constrain position
      if (rect.left < 0) rect.left = 0;
      if (rect.top < 0) rect.top = 0;
      if (rect.left + rect.getScaledWidth() > imgWidth) rect.left = imgWidth - rect.getScaledWidth();
      if (rect.top + rect.getScaledHeight() > imgHeight) rect.top = imgHeight - rect.getScaledHeight();

      // Constrain size
      if (rect.getScaledWidth() > imgWidth) rect.scaleX = imgWidth / rect.width;
      if (rect.getScaledHeight() > imgHeight) rect.scaleY = imgHeight / rect.height;

      // Apply aspect ratio if set
      if (this.cropAspectRatio) {
        const currentWidth = rect.getScaledWidth();
        const newHeight = currentWidth / this.cropAspectRatio;
        rect.scaleY = newHeight / rect.height;

        // Re-constrain after aspect ratio adjustment
        if (rect.getScaledHeight() > imgHeight) {
          rect.scaleY = imgHeight / rect.height;
          rect.scaleX = (rect.getScaledHeight() * this.cropAspectRatio) / rect.width;
        }
      }

      rect.setCoords();
      this.updateCropOverlay();
      this.updateCropSelectionSize();
    },

    updateCropOverlay() {
      if (!this.cropCanvas || !this.cropRect || !this.cropImage) return;

      // Remove existing overlay
      const objects = this.cropCanvas.getObjects();
      objects.forEach(obj => {
        if (obj.isOverlay) this.cropCanvas.remove(obj);
      });

      const rect = this.cropRect;
      const canvasWidth = this.cropCanvas.getWidth();
      const canvasHeight = this.cropCanvas.getHeight();

      // Create 4 overlay rectangles around the crop area
      const overlays = [
        // Top
        { left: 0, top: 0, width: canvasWidth, height: rect.top },
        // Bottom
        { left: 0, top: rect.top + rect.getScaledHeight(), width: canvasWidth, height: canvasHeight - rect.top - rect.getScaledHeight() },
        // Left
        { left: 0, top: rect.top, width: rect.left, height: rect.getScaledHeight() },
        // Right
        { left: rect.left + rect.getScaledWidth(), top: rect.top, width: canvasWidth - rect.left - rect.getScaledWidth(), height: rect.getScaledHeight() }
      ];

      overlays.forEach(o => {
        if (o.width > 0 && o.height > 0) {
          const overlay = new fabric.Rect({
            left: o.left,
            top: o.top,
            width: o.width,
            height: o.height,
            fill: 'rgba(0, 0, 0, 0.5)',
            selectable: false,
            evented: false
          });
          overlay.isOverlay = true;
          this.cropCanvas.add(overlay);
        }
      });

      // Bring crop rect to front
      this.cropRect.bringToFront();
      this.cropCanvas.renderAll();
    },

    updateCropSelectionSize() {
      if (!this.cropRect || !this.cropImage) return;

      const scale = this.cropImage.scaleX;
      const w = Math.round(this.cropRect.getScaledWidth() / scale);
      const h = Math.round(this.cropRect.getScaledHeight() / scale);
      this.$('cropSelectionSize').textContent = `${w} × ${h}`;
    },

    setCropAspectRatio(value) {
      if (value === 'free') {
        this.cropAspectRatio = null;
      } else {
        const [w, h] = value.split(':').map(Number);
        this.cropAspectRatio = w / h;
        this.constrainCropRect();
        this.cropCanvas.renderAll();
      }
    },

    resetCrop() {
      if (!this.cropCanvas || !this.cropImage) return;

      const canvasWidth = this.cropCanvas.getWidth();
      const canvasHeight = this.cropCanvas.getHeight();
      const rectWidth = canvasWidth * 0.8;
      const rectHeight = canvasHeight * 0.8;

      this.cropRect.set({
        left: (canvasWidth - rectWidth) / 2,
        top: (canvasHeight - rectHeight) / 2,
        scaleX: 1,
        scaleY: 1,
        width: rectWidth,
        height: rectHeight
      });
      this.cropRect.setCoords();
      this.updateCropOverlay();
      this.updateCropSelectionSize();
      this.cropCanvas.renderAll();
    },

    applyCrop() {
      if (!this.cropRect || !this.cropImage || !this.originalImage) {
        this.toast('No crop selection');
        return;
      }

      const rect = this.cropRect;
      const scale = this.cropImage.scaleX;

      // Calculate crop coordinates relative to original image
      const cropX = rect.left / scale;
      const cropY = rect.top / scale;
      const cropWidth = rect.getScaledWidth() / scale;
      const cropHeight = rect.getScaledHeight() / scale;

      // Create a temporary canvas to crop the image
      const tempCanvas = document.createElement('canvas');
      tempCanvas.width = cropWidth;
      tempCanvas.height = cropHeight;
      const ctx = tempCanvas.getContext('2d');

      // Draw the cropped portion
      ctx.drawImage(
        this.originalImage._element,
        cropX, cropY, cropWidth, cropHeight,
        0, 0, cropWidth, cropHeight
      );

      // Get the cropped image data URL
      const croppedDataUrl = tempCanvas.toDataURL('image/png');

      // Get the original image's position and properties
      const origLeft = this.originalImage.left;
      const origTop = this.originalImage.top;
      const origScaleX = this.originalImage.scaleX;
      const origScaleY = this.originalImage.scaleY;

      // Remove the original image
      this.canvas.remove(this.originalImage);

      // Add the cropped image
      fabric.Image.fromURL(croppedDataUrl, (img) => {
        img.set({
          left: origLeft,
          top: origTop,
          scaleX: origScaleX,
          scaleY: origScaleY
        });
        this.canvas.add(img);
        this.canvas.setActiveObject(img);
        this.canvas.renderAll();
        this.saveHistory();
        this.updateLayers();
        this.toast('Image cropped successfully');
      });

      this.closeCropModal();
    },

    closeCropModal() {
      this.$('cropModal').classList.add('hidden');
      if (this.cropCanvas) {
        this.cropCanvas.dispose();
        this.cropCanvas = null;
      }
      this.cropImage = null;
      this.cropRect = null;
    },

    // Toast
    toast(msg) {
      const t = this.$('toast'); t.textContent = msg; t.classList.remove('opacity-0', '-translate-y-4'); t.classList.add('opacity-100', 'translate-y-0');
      setTimeout(() => { t.classList.add('opacity-0', '-translate-y-4'); t.classList.remove('opacity-100', 'translate-y-0'); }, 2000);
    }
  };
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  window.canvasApp = canvasBuilder();
  window.canvasApp.init();
});
</script>