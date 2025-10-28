{{-- resources/views/maps/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Uzbekistan Map – Interactive Kiosk</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --c-dark: #1C3F3A;
            --c-white: #FFFFFF;
            --c-light: #E0E9E9;
            --c-beige: #EBE9DA;
            --radius: 20px;
            --shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--c-dark);
            color: var(--c-white);
            min-height: 100vh;
            overflow: hidden;
        }

        /* LOADING */
        #loading {
            position: fixed;
            inset: 0;
            background: var(--c-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.8s ease;
        }

        .title-wrap {
            font-size: 4rem;
            font-weight: 900;
            text-transform: uppercase;
            background: linear-gradient(90deg, var(--c-white), var(--c-light));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulseGlow 2.5s ease-in-out infinite alternate;
        }

        @keyframes pulseGlow {
            0% { text-shadow: 0 0 20px rgba(255,255,255,0.4); }
            100% { text-shadow: 0 0 40px rgba(255,255,255,0.8); }
        }

        .loading-spinner {
            margin-top: 2rem;
            width: 80px;
            height: 80px;
            border: 6px solid rgba(255,255,255,0.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* MAP CONTAINER */
        .map-container {
            position: fixed;
            inset: 0;
            display: flex;
        }

        #map-wrapper {
            flex: 1;
            position: relative;
            overflow: hidden;
            cursor: grab;
            background: var(--c-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #map-wrapper:active {
            cursor: grabbing;
        }

        #map-inner {
            position: relative;
            transform-origin: center;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #map-image {
            max-width: 100%;
            max-height: 100%;
            display: block;
            border-radius: 20px;
            filter: drop-shadow(0 20px 60px rgba(0,0,0,0.4));
            user-select: none;
            -webkit-user-drag: none;
        }

        /* MARKERS */
        #marker-container {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .marker {
            position: absolute;
            width: 16px;
            height: 16px;
            background: #fff;
            border: 2px solid #fff;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            cursor: pointer;
            pointer-events: auto;
            animation: pulse 2s infinite;
            transition: transform 0.2s ease;
        }

        .marker:hover {
            transform: translate(-50%, -50%) scale(1.4);
        }

        .marker.active {
            animation: activePulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 4px 20px rgba(0,0,0,0.6), 0 0 0 0 rgba(255,255,255,0.7); }
            70% { box-shadow: 0 4px 20px rgba(0,0,0,0.6), 0 0 0 15px rgba(255,255,255,0); }
            100% { box-shadow: 0 4px 20px rgba(0,0,0,0.6), 0 0 0 0 rgba(255,255,255,0); }
        }

        @keyframes activePulse {
            0% { box-shadow: 0 8px 35px rgba(0,0,0,0.9), 0 0 0 0 rgba(255,255,255,0.7); }
            70% { box-shadow: 0 8px 35px rgba(0,0,0,0.9), 0 0 0 20px rgba(255,255,255,0); }
            100% { box-shadow: 0 8px 35px rgba(0,0,0,0.9), 0 0 0 0 rgba(255,255,255,0); }
        }

        /* INFO PANEL - YASHIRIN */
        .marker-info {
            position: fixed;
            top: 0;
            right: -500px;
            width: 420px;
            height: 100vh;
            background: rgba(255,255,255,0.98);
            padding: 30px;
            box-shadow: -10px 0 40px rgba(0,0,0,0.3);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 30;
            overflow-y: auto;
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
        }

        .marker-info.active {
            right: 0;
            visibility: visible;
            opacity: 1;
            pointer-events: auto;
        }

        .image-carousel {
            height: 280px;
            border-radius: 16px;
            overflow: hidden;
            background: #f8f9fa;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .image-carousel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .marker-info h3 {
            font-size: 1.8rem;
            margin-bottom: 12px;
            background: linear-gradient(135deg, var(--c-dark), #555);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .marker-info p {
            color: #444;
            line-height: 1.7;
        }

        .close-info {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: #fff;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(245,87,108,0.4);
            transition: transform 0.3s ease;
        }

        .close-info:hover {
            transform: rotate(90deg) scale(1.1);
        }

        .send-email-btn {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background: linear-gradient(135deg, var(--c-dark), #2c5a57);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(28,63,58,0.3);
            transition: transform 0.2s ease;
        }

        .send-email-btn:hover {
            transform: translateY(-2px);
        }

        /* CONTROLS */
        .controls {
            position: absolute;
            top: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 20;
        }

        .control-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.95);
            border: 2px solid rgba(28,63,58,0.3);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--c-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            background: linear-gradient(135deg, var(--c-dark), #2c5a57);
            color: #fff;
            transform: scale(1.15);
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 30;
            padding: 12px 24px;
            background: var(--c-dark);
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s ease;
        }

        .back-button:hover {
            transform: translateX(-5px);
        }

        .back-button::before {
            content: "Back";
            font-size: 1.2rem;
        }

        /* EMAIL MODAL - YANGI DIZAYN */
        .email-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 100;
            backdrop-filter: blur(8px);
            transition: opacity 0.3s ease, visibility 0.3s ease;
            padding: 20px;
        }

        .email-modal.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: var(--radius);
            width: 90%;
            max-width: 460px;
            max-height: calc(90vh - 100px);
            overflow-y: auto;
            box-shadow: var(--shadow);
            animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 60px;
            position: relative;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: scale(0.85) translateY(-30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--c-dark), #555);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #666;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close-btn:hover {
            background: #f0f0f0;
            color: #333;
        }

        .modal-info-box {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .modal-info-box h3 {
            margin: 0 0 8px 0;
            color: #1C3F3A;
            font-size: 1.3rem;
        }

        .modal-info-box p {
            margin: 0;
            color: #444;
            font-size: 1rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1C3F3A;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid rgba(28,63,58,0.2);
            border-radius: 12px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--c-dark);
            box-shadow: 0 0 0 3px rgba(28,63,58,0.1);
        }

        /* STICKY FOOTER */
        .modal-footer {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 16px 0 0;
            margin: 20px -30px -30px;
            border-top: 1px solid #eee;
            z-index: 10;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--c-dark);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(28,63,58,0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #6c757d;
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        /* VIRTUAL KEYBOARD - YANGI DIZAYN */
        #virtual-keyboard {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            /* background: rgba(0, 0, 0, 0.95); */
            display: none;
            z-index: 110;
            max-height: 30vh;
            overflow-y: auto;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            /* box-shadow: 0 -10px 30px rgba(0,0,0,0.3); */
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 4px;
            gap: 4px;
        }

        .keyboard-key {
            margin: 0;
            padding: 10px 12px;
            min-width: 44px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            background: #444;
            color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .keyboard-key:hover {
            background: #666;
            transform: translateY(-1px);
        }

        .keyboard-key.special {
            background: #555;
            min-width: 70px;
            font-size: 14px;
        }

        .keyboard-key.special:hover {
            background: #777;
        }

        /* RESPONSIVE */
        @media (min-width: 1900px) and (max-height: 1100px) {
            .title-wrap { font-size: 5.5rem; }
            .control-btn { width: 80px; height: 80px; font-size: 2.4rem; }
            .marker { width: 20px; height: 20px; }
            .marker-info { width: 520px; padding: 40px; }
            .image-carousel { height: 380px; }
            .send-email-btn { font-size: 1.4rem; padding: 18px; }
            .modal-content { max-width: 560px; padding: 40px; }
            .form-input { font-size: 1.3rem; padding: 18px; }
            .keyboard-key { padding: 16px 20px; font-size: 22px; min-width: 56px; }
        }

        @media (max-width: 768px) {
            .marker-info { width: 100%; right: -100%; }
            .modal-content { max-height: 75vh; }
            .controls { right: 15px; top: 15px; }
            .control-btn { width: 50px; height: 50px; font-size: 1.5rem; }
            .keyboard-key { padding: 8px 10px; font-size: 14px; min-width: 40px; }
        }
    </style>
</head>
<body>

    <!-- LOADING -->
    <div id="loading">
        <div class="title-wrap">Uzbekistan Travel</div>
        <div class="loading-spinner"></div>
    </div>

    <!-- MAP -->
    <div class="map-container">
        <div id="map-wrapper">

            <a href="{{ route('welcome') }}" class="back-button"></a>

            <div class="controls">
                <button class="control-btn" id="zoom-in" title="Zoom In">+</button>
                <button class="control-btn" id="zoom-out" title="Zoom Out">-</button>
                <button class="control-btn" id="reset-view" title="Reset"><i class="fas fa-sync-alt"></i></button>
            </div>

            <div id="map-inner">
                <img id="map-image" src="{{ asset('map/uzbekistan-map.png') }}" alt="Uzbekistan Map">
                <div id="marker-container"></div>
            </div>

            <!-- INFO PANEL -->
            <div class="marker-info" id="marker-info">
                <button class="close-info" id="close-info">×</button>
                <div class="image-carousel" id="image-carousel"></div>
                <h3 id="info-title"></h3>
                <p id="info-description"></p>
                <button class="send-email-btn" id="send-email-btn">
                    <i class="fas fa-envelope"></i> Send to Email
                </button>
            </div>

        </div>
    </div>

    <!-- EMAIL MODAL - YANGI FOOTER -->
    <div class="email-modal" id="email-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Send to Email</h2>
                <button class="modal-close-btn" id="modal-close">×</button>
            </div>

            <div class="modal-info-box">
                <h3 id="modal-info-title"></h3>
                <p id="modal-info-description"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Your Email Address</label>
                <input type="email" id="email-input" class="form-input" placeholder="example@gmail.com" autocomplete="off">
            </div>

            <!-- STICKY FOOTER -->
            <div class="modal-footer">
                <div class="modal-buttons">
                    <button class="btn btn-secondary" id="cancel-btn">Cancel</button>
                    <button class="btn btn-primary" id="send-btn">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIRTUAL KEYBOARD -->
    <div id="virtual-keyboard"></div>

    <script>
        // ===================================
        // 1. GLOBAL VARIABLES
        // ===================================
        const maps = @json($maps);
        let currentMap = null;

        const elements = {
            wrapper: document.getElementById('map-wrapper'),
            mapInner: document.getElementById('map-inner'),
            mapImage: document.getElementById('map-image'),
            markerContainer: document.getElementById('marker-container'),
            markerInfo: document.getElementById('marker-info'),
            imageCarousel: document.getElementById('image-carousel'),
            infoTitle: document.getElementById('info-title'),
            infoDesc: document.getElementById('info-description'),
            closeInfo: document.getElementById('close-info'),
            sendEmailBtn: document.getElementById('send-email-btn'),
            zoomInBtn: document.getElementById('zoom-in'),
            zoomOutBtn: document.getElementById('zoom-out'),
            resetBtn: document.getElementById('reset-view'),
            loading: document.getElementById('loading'),
            emailModal: document.getElementById('email-modal'),
            modalClose: document.getElementById('modal-close'),
            cancelBtn: document.getElementById('cancel-btn'),
            sendBtn: document.getElementById('send-btn'),
            emailInput: document.getElementById('email-input'),
            modalInfoTitle: document.getElementById('modal-info-title'),
            modalInfoDesc: document.getElementById('modal-info-description'),
            keyboard: document.getElementById('virtual-keyboard')
        };

        let scale = 1, minScale = 1, maxScale = 3;
        let translateX = 0, translateY = 0;
        let isPanning = false, startX = 0, startY = 0;
        const zoomSpeed = 0.2;
        let imgW = 0, imgH = 0;
        const boundaries = { minX: 0, maxX: 0, minY: 0, maxY: 0 };

        let capsLock = false;
        const keyboardLayout = [
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_', 'Backspace'],
            ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '@'],
            ['a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', '.',],
            ['CAPS', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'CAPS'],
            ['Space', 'Enter', 'CLOSE']
        ];

        // ===================================
        // 2. KEYBOARD
        // ===================================
        function showKeyboard() {
            elements.keyboard.style.display = 'block';
            renderKeyboard();
        }

        function hideKeyboard() {
            elements.keyboard.style.display = 'none';
        }

        function renderKeyboard() {
            elements.keyboard.innerHTML = '';
            keyboardLayout.forEach(row => {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'keyboard-row';
                row.forEach(key => {
                    const btn = document.createElement('button');
                    btn.className = 'keyboard-key';
                    if (['Backspace', 'Enter', 'CAPS', 'CLOSE', 'Space'].includes(key)) {
                        btn.classList.add('special');
                    }
                    btn.textContent = (capsLock && key.length === 1) ? key.toUpperCase() : key;
                    btn.onclick = () => handleKeyPress(key);
                    rowDiv.appendChild(btn);
                });
                elements.keyboard.appendChild(rowDiv);
            });
        }

        function handleKeyPress(key) {
            if (key === 'CAPS') {
                capsLock = !capsLock;
                renderKeyboard();
                return;
            }
            if (key === 'Backspace') elements.emailInput.value = elements.emailInput.value.slice(0, -1);
            else if (key === 'Enter') sendEmail();
            else if (key === 'CLOSE') hideKeyboard();
            else if (key === 'Space') elements.emailInput.value += ' ';
            else elements.emailInput.value += capsLock && key.length === 1 ? key.toUpperCase() : key;
            elements.emailInput.focus();
        }

        // ===================================
        // 3. MAP INIT
        // ===================================
        function initMap() {
            const rect = elements.wrapper.getBoundingClientRect();
            imgW = elements.mapImage.naturalWidth;
            imgH = elements.mapImage.naturalHeight;

            const scaleW = rect.width / imgW;
            const scaleH = rect.height / imgH;
            const initScale = Math.min(scaleW, scaleH);
            scale = initScale * 2.0;
            minScale = scale;

            createMarkers();
            adjustMarkers();
            applyTransform();
        }

        function createMarkers() {
            elements.markerContainer.innerHTML = '';
            maps.forEach(map => {
                const markerEl = document.createElement('div');
                markerEl.className = 'marker';
                markerEl.dataset.id = map.id;
                markerEl.title = map.name_en || 'Place';
                markerEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    zoomToMarker(markerEl, map);
                });
                elements.markerContainer.appendChild(markerEl);
            });
        }

        function adjustMarkers() {
            const contW = elements.mapInner.offsetWidth;
            const contH = elements.mapInner.offsetHeight;
            const fit = Math.min(contW / imgW, contH / imgH);
            const dispW = imgW * fit;
            const dispH = imgH * fit;
            const offX = (contW - dispW) / 2;
            const offY = (contH - dispH) / 2;

            document.querySelectorAll('.marker').forEach(marker => {
                const mapData = maps.find(m => m.id == marker.dataset.id);
                if (!mapData) return;
                const px = (mapData.longitude / 100) * imgW * fit + offX;
                const py = (mapData.latitude / 100) * imgH * fit + offY;
                marker.style.left = `${(px / contW) * 100}%`;
                marker.style.top = `${(py / contH) * 100}%`;
            });
        }

        function showImage(imgUrl) {
            elements.imageCarousel.innerHTML = '';
            if (!imgUrl) {
                const placeholder = document.createElement('div');
                placeholder.textContent = 'No Image Available';
                placeholder.style.cssText = 'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f8f9fa;color:#6c757d;font-size:1.1rem;';
                elements.imageCarousel.appendChild(placeholder);
                return;
            }
            const img = document.createElement('img');
            img.src = imgUrl;
            img.alt = 'Place Image';
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:16px;';
            img.onerror = () => img.src = 'https://via.placeholder.com/400x280?text=Image+Not+Found';
            elements.imageCarousel.appendChild(img);
        }

        function zoomToMarker(markerEl, mapData) {
            document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
            markerEl.classList.add('active');
            currentMap = mapData;

            elements.infoTitle.textContent = mapData.name_en || 'Place';
            elements.infoDesc.textContent = mapData.description_en || 'No description available.';
            showImage(mapData.image_url || (mapData.image ? '/storage/' + mapData.image : null));

            elements.markerInfo.classList.add('active');

            const markerRect = markerEl.getBoundingClientRect();
            const wrapperRect = elements.wrapper.getBoundingClientRect();
            const centerX = markerRect.left + markerRect.width / 2 - wrapperRect.left;
            const centerY = markerRect.top + markerRect.height / 2 - wrapperRect.top;
            const targetX = (wrapperRect.width - 420) / 2;
            const targetY = wrapperRect.height / 2;
            const targetScale = Math.min(maxScale, scale * 1.5);

            translateX += (targetX - centerX) / scale;
            translateY += (targetY - centerY) / scale;
            scale = targetScale;
            recalcBounds();
            clamp();
            applyTransform();
        }

        // ===================================
        // 4. ZOOM & PAN
        // ===================================
        function recalcBounds() {
            const wrapperRect = elements.wrapper.getBoundingClientRect();
            const displayW = elements.mapImage.offsetWidth * scale;
            const displayH = elements.mapImage.offsetHeight * scale;
            const overflowX = Math.max(0, (displayW - wrapperRect.width) / 2);
            const overflowY = Math.max(0, (displayH - wrapperRect.height) / 2);
            boundaries.minX = -overflowX / scale;
            boundaries.maxX = overflowX / scale;
            boundaries.minY = -overflowY / scale;
            boundaries.maxY = overflowY / scale;
        }

        function clamp() {
            translateX = Math.max(boundaries.minX, Math.min(boundaries.maxX, translateX));
            translateY = Math.max(boundaries.minY, Math.min(boundaries.maxY, translateY));
        }

        function applyTransform() {
            elements.mapInner.style.transition = isPanning ? 'none' : 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            elements.mapInner.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
        }

        function zoomIn() { scale = Math.min(maxScale, scale + zoomSpeed); recalcBounds(); clamp(); applyTransform(); }
        function zoomOut() { scale = Math.max(minScale, scale - zoomSpeed); recalcBounds(); clamp(); applyTransform(); }

        function resetView() {
            const rect = elements.wrapper.getBoundingClientRect();
            const scaleW = rect.width / imgW;
            const scaleH = rect.height / imgH;
            scale = Math.min(scaleW, scaleH) * 2;
            minScale = scale;
            translateX = 0;
            translateY = 0;
            document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
            elements.markerInfo.classList.remove('active');
            closeEmailModal();
            recalcBounds();
            applyTransform();
        }

        // ===================================
        // 5. EMAIL MODAL
        // ===================================
        function openEmailModal() {
            if (!currentMap) return alert('Please select a location first!');
            elements.modalInfoTitle.textContent = currentMap.name_en || 'Place';
            elements.modalInfoDesc.textContent = currentMap.description_en || 'No description';
            elements.emailInput.value = '';
            elements.emailModal.classList.add('active');
            
            setTimeout(() => {
                elements.emailInput.focus();
                const modalContent = elements.emailModal.querySelector('.modal-content');
                modalContent.scrollTop = 0;
            }, 100);
        }

        function closeEmailModal() {
            elements.emailModal.classList.remove('active');
            hideKeyboard();
            elements.emailInput.value = '';
        }

        async function sendEmail() {
            const email = elements.emailInput.value.trim();
            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                alert('Please enter a valid email address!');
                elements.emailInput.focus();
                return;
            }
            if (!currentMap) return alert('No location selected!');

            elements.sendBtn.disabled = true;
            elements.sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                const response = await fetch('{{ route("send.map.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email, map_id: currentMap.id, lang: 'en' })
                });
                const result = await response.json();
                if (result.success) {
                    alert('Email sent successfully!');
                    closeEmailModal();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                alert('Network error! Please try again.');
            } finally {
                elements.sendBtn.disabled = false;
                elements.sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
            }
        }

        // ===================================
        // 6. EVENT LISTENERS
        // ===================================
        function setupEventListeners() {
            elements.zoomInBtn.addEventListener('click', zoomIn);
            elements.zoomOutBtn.addEventListener('click', zoomOut);
            elements.resetBtn.addEventListener('click', resetView);
            elements.closeInfo.addEventListener('click', resetView);
            elements.sendEmailBtn.addEventListener('click', openEmailModal);
            elements.modalClose.addEventListener('click', closeEmailModal);
            elements.cancelBtn.addEventListener('click', closeEmailModal);
            elements.sendBtn.addEventListener('click', sendEmail);
            elements.emailInput.addEventListener('focus', showKeyboard);
            elements.emailInput.addEventListener('keypress', e => e.key === 'Enter' && sendEmail());
            elements.emailModal.addEventListener('click', e => e.target === elements.emailModal && closeEmailModal());
            document.addEventListener('keydown', e => e.key === 'Escape' && (elements.emailModal.classList.contains('active') ? closeEmailModal() : elements.markerInfo.classList.contains('active') && resetView()));

            elements.wrapper.addEventListener('wheel', e => { e.preventDefault(); scale = Math.min(maxScale, Math.max(minScale, scale + (e.deltaY < 0 ? 1 : -1) * zoomSpeed)); recalcBounds(); clamp(); applyTransform(); }, { passive: false });

            ['mousedown', 'mousemove', 'mouseup', 'mouseleave'].forEach((ev, i, arr) => {
                elements.wrapper.addEventListener(ev, e => {
                    if (e.target.closest('.marker')) return;
                    if (ev === 'mousedown') { isPanning = true; startX = e.clientX - translateX * scale; startY = e.clientY - translateY * scale; elements.wrapper.style.cursor = 'grabbing'; }
                    else if (ev === 'mousemove' && isPanning) { translateX = (e.clientX - startX) / scale; translateY = (e.clientY - startY) / scale; clamp(); requestAnimationFrame(applyTransform); }
                    else if (arr.indexOf(ev) > 1) { isPanning = false; elements.wrapper.style.cursor = 'grab'; }
                });
            });

            elements.wrapper.addEventListener('touchstart', e => { if (e.touches.length === 1) { isPanning = true; startX = e.touches[0].clientX - translateX * scale; startY = e.touches[0].clientY - translateY * scale; } });
            elements.wrapper.addEventListener('touchmove', e => { if (isPanning && e.touches.length === 1) { e.preventDefault(); translateX = (e.touches[0].clientX - startX) / scale; translateY = (e.touches[0].clientY - startY) / scale; clamp(); requestAnimationFrame(applyTransform); } }, { passive: false });
            elements.wrapper.addEventListener('touchend', () => isPanning = false);

            elements.wrapper.addEventListener('click', e => {
                if (e.target.closest('.marker') || e.target.closest('.marker-info') || e.target.closest('.controls')) return;
                elements.markerInfo.classList.remove('active');
                closeEmailModal();
                document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
            });

            window.addEventListener('resize', () => {
                const rect = elements.wrapper.getBoundingClientRect();
                const scaleW = rect.width / imgW;
                const scaleH = rect.height / imgH;
                scale = Math.min(scaleW, scaleH) * 2;
                minScale = scale;
                translateX = 0;
                translateY = 0;
                recalcBounds();
                adjustMarkers();
                applyTransform();
            });
        }

        // ===================================
        // 7. INIT
        // ===================================
        function init() {
            setupEventListeners();
            elements.mapImage.onload = () => {
                imgW = elements.mapImage.naturalWidth;
                imgH = elements.mapImage.naturalHeight;
                setTimeout(() => elements.loading.style.opacity = '0', 3500);
                setTimeout(() => { elements.loading.style.display = 'none'; initMap(); }, 4300);
            };
            elements.mapImage.onerror = () => {
                elements.loading.innerHTML = `<div style="color:#fff;text-align:center;"><div style="font-size:3rem;margin-bottom:20px;">Warning</div><div style="font-size:1.5rem;">Error loading map</div><div style="margin-top:10px;">Please refresh</div></div>`;
            };
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    </script>
</body>
</html>
