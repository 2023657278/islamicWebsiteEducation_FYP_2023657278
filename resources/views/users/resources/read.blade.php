<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - Reading Mode</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';</script>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { height: 100vh; overflow: hidden; background-color: #2c3035; display: flex; flex-direction: column; font-family: 'Segoe UI', sans-serif; }
        
        /* --- TOP TOOLBAR --- */
        .top-bar { height: 64px; background: white; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 20; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .toolbar-group { display: flex; align-items: center; gap: 10px; }
        .book-title { font-weight: 700; font-size: 1rem; color: #333; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Icon Buttons */
        .btn-icon { width: 36px; height: 36px; border-radius: 6px; border: none; background: transparent; color: #555; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .btn-icon:hover { background: #f0f0f0; color: #000; }
        .btn-icon.active { background: #eef2ff; color: #4f46e5; }
        
        /* View Mode Toggle (Single/Double) */
        .view-toggle { display: flex; background: #f1f3f4; border-radius: 8px; padding: 2px; }
        .view-toggle button { width: 32px; height: 32px; border: none; background: transparent; border-radius: 6px; color: #666; }
        .view-toggle button.active { background: white; shadow: 0 1px 3px rgba(0,0,0,0.1); color: #000; font-weight: bold; }

        /* --- SIDEBAR --- */
        .sidebar { width: 280px; background: white; border-right: 1px solid #ddd; position: absolute; top: 64px; bottom: 50px; left: 0; transform: translateX(-100%); transition: transform 0.3s ease; z-index: 15; display: flex; flex-direction: column; }
        .sidebar.open { transform: translateX(0); }
        .sidebar-header { padding: 15px; border-bottom: 1px solid #eee; font-weight: bold; display: flex; justify-content: space-between; }
        .chapter-list { flex: 1; overflow-y: auto; list-style: none; padding: 0; margin: 0; }
        .chapter-item { padding: 12px 20px; border-bottom: 1px solid #f9f9f9; cursor: pointer; font-size: 0.9rem; color: #444; }
        .chapter-item:hover { background: #f8f9fa; color: #dc3545; }

        /* --- MAIN READING AREA --- */
        .reader-container { flex: 1; position: relative; overflow: auto; display: flex; justify-content: center; align-items: center; background: #525659; transition: all 0.3s; }
        
        /* Book Wrapper */
        .book-wrapper { display: flex; gap: 0; box-shadow: 0 20px 50px rgba(0,0,0,0.5); transition: transform 0.2s ease; transform-origin: center top; }
        canvas { display: block; background: white; }
        
        /* Navigation Arrows (Floating) */
        .nav-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; background: rgba(255,255,255,0.9); border-radius: 50%; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #333; z-index: 10; transition: all 0.2s; opacity: 0; }
        .reader-container:hover .nav-arrow { opacity: 1; }
        .nav-arrow:hover { background: white; transform: translateY(-50%) scale(1.1); }
        .nav-prev { left: 20px; }
        .nav-next { right: 20px; }

        /* --- BOTTOM PROGRESS BAR --- */
        .bottom-bar { height: 50px; background: white; border-top: 1px solid #ddd; display: flex; align-items: center; padding: 0 20px; z-index: 20; }
        .progress-track { flex: 1; height: 6px; background: #e0e0e0; border-radius: 3px; margin: 0 15px; position: relative; cursor: pointer; }
        .progress-fill { height: 100%; background: #dc3545; border-radius: 3px; width: 0%; transition: width 0.3s; }
        .page-info { font-size: 0.85rem; color: #666; font-weight: 600; min-width: 100px; text-align: right; }

    </style>
</head>
<body>

    {{-- TOP TOOLBAR --}}
    <div class="top-bar">
        <div class="toolbar-group">
            <a href="{{ route('student.textbooks.index') }}" class="btn-icon" title="Exit"><i class="fas fa-times"></i></a>
            <button class="btn-icon" onclick="toggleSidebar()" title="Table of Contents"><i class="fas fa-list"></i></button>
            <span class="book-title ms-2">{{ $book->title }}</span>
        </div>

        <div class="toolbar-group">
            <div class="view-toggle me-3">
                <button id="btnSingle" onclick="setViewMode('single')" title="Single Page"><i class="far fa-file"></i></button>
                <button id="btnDouble" class="active" onclick="setViewMode('double')" title="Double Page (Book View)"><i class="fas fa-book-open"></i></button>
            </div>

            <button class="btn-icon" onclick="changeZoom(-0.1)"><i class="fas fa-minus"></i></button>
            <span id="zoomLevel" class="small fw-bold mx-2">100%</span>
            <button class="btn-icon" onclick="changeZoom(0.1)"><i class="fas fa-plus"></i></button>
            
            <button class="btn-icon ms-2" onclick="toggleFullScreen()"><i class="fas fa-expand"></i></button>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span>Contents</span>
            <button class="btn-icon" onclick="toggleSidebar()" style="width:24px; height:24px;"><i class="fas fa-times"></i></button>
        </div>
        <ul class="chapter-list">
            <li class="chapter-item" onclick="goToPage(1)">Cover Page</li>
            <li class="chapter-item" onclick="goToPage(3)">Chapter 1</li>
            <li class="chapter-item" onclick="goToPage(10)">Chapter 2</li>
            <li class="chapter-item" onclick="goToPage(20)">Chapter 3</li>
            <li class="chapter-item" onclick="goToPage(30)">Chapter 4</li>
            </ul>
    </div>

    {{-- READER AREA --}}
    <div class="reader-container" id="readerContainer">
        <button class="nav-arrow nav-prev" onclick="prevPage()"><i class="fas fa-chevron-left"></i></button>
        
        <div class="book-wrapper" id="bookWrapper">
            <canvas id="canvasLeft"></canvas>
            <canvas id="canvasRight"></canvas>
        </div>

        <button class="nav-arrow nav-next" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
    </div>

    {{-- BOTTOM BAR --}}
    <div class="bottom-bar">
        <span class="small text-muted fw-bold">Progress</span>
        <div class="progress-track" onclick="scrub(event)">
            <div class="progress-fill" id="progressBar"></div>
        </div>
        <div class="page-info">
            Page <span id="pageCurrent">1</span> / <span id="pageTotal">--</span>
        </div>
    </div>

    <script>
        // --- CONFIGURATION ---
        const url = "{{ asset('storage/' . $book->file_url) }}";
        const resourceId = {{ $book->id }};
        let pdfDoc = null;
        let pageNum = {{ $startPage }};
        let scale = 1.0; // Zoom 100%
        let isDoublePage = true; // Default to Book View
        let pageRendering = false;
        let pageNumPending = null;

        const canvasLeft = document.getElementById('canvasLeft');
        const ctxLeft = canvasLeft.getContext('2d');
        const canvasRight = document.getElementById('canvasRight');
        const ctxRight = canvasRight.getContext('2d');

        // --- INITIALIZATION ---
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('pageTotal').textContent = pdfDoc.numPages;
            
            // Adjust Zoom to fit screen initially
            fitToScreen();
            
            render();
        });

        // --- RENDER LOGIC ---
        function render() {
            // If Double Page, ensure we start on odd number (1, 3, 5) or even-odd pairs depending on preference
            // Standard Book: Cover is 1 (Single), then 2-3, 4-5.
            
            renderPage(pageNum, canvasLeft, ctxLeft);

            if (isDoublePage && pageNum < pdfDoc.numPages) {
                canvasRight.style.display = 'block';
                renderPage(pageNum + 1, canvasRight, ctxRight);
            } else {
                canvasRight.style.display = 'none';
            }

            updateUI();
        }

        function renderPage(num, canvas, ctx) {
            pageRendering = true;
            pdfDoc.getPage(num).then(function(page) {
                var viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                var renderContext = { canvasContext: ctx, viewport: viewport };
                var renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        render(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
        }

        // --- NAVIGATION ---
        function prevPage() {
            let step = isDoublePage ? 2 : 1;
            if (pageNum <= 1) return;
            pageNum -= step;
            if(pageNum < 1) pageNum = 1;
            render();
            saveProgress();
        }

        function nextPage() {
            let step = isDoublePage ? 2 : 1;
            if (pageNum >= pdfDoc.numPages) return;
            pageNum += step;
            render();
            saveProgress();
        }

        function goToPage(num) {
            pageNum = num;
            render();
            saveProgress();
        }

        // --- VIEW MODES ---
        function setViewMode(mode) {
            isDoublePage = (mode === 'double');
            
            // Toggle Buttons
            document.getElementById('btnSingle').classList.toggle('active', !isDoublePage);
            document.getElementById('btnDouble').classList.toggle('active', isDoublePage);

            // Adjust sizing
            fitToScreen();
            render();
        }

        function changeZoom(delta) {
            scale += delta;
            if(scale < 0.5) scale = 0.5;
            if(scale > 3.0) scale = 3.0;
            
            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
            render();
        }

        function fitToScreen() {
            // Simple logic: if double, zoom out a bit. If single, zoom in.
            if(isDoublePage) scale = 0.8; 
            else scale = 1.2;
            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
        }

        // --- UI UPDATES ---
        function updateUI() {
            // Update Page Count Text
            let displayNum = pageNum;
            if(isDoublePage && pageNum < pdfDoc.numPages) displayNum = pageNum + "-" + (pageNum + 1);
            document.getElementById('pageCurrent').textContent = displayNum;

            // Update Progress Bar
            let percent = (pageNum / pdfDoc.numPages) * 100;
            document.getElementById('progressBar').style.width = percent + '%';
        }

        // --- UTILS ---
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
            }
        }

        function scrub(e) {
            let rect = e.target.closest('.progress-track').getBoundingClientRect();
            let clickX = e.clientX - rect.left;
            let width = rect.width;
            let percent = clickX / width;
            let newPage = Math.round(percent * pdfDoc.numPages);
            if(newPage < 1) newPage = 1;
            goToPage(newPage);
        }

        function saveProgress() {
            fetch("{{ route('student.textbooks.save_progress') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    resource_id: resourceId,
                    current_page: pageNum,
                    total_pages: pdfDoc.numPages
                })
            });
        }
    </script>
</body>
</html>