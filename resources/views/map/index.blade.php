<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uzbekiston Xaritasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4b8a4e56 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        
        .map-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #map-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            cursor: grab;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
        }
        
        #map-wrapper:active {
            cursor: grabbing;
        }
        
        #map-inner {
            position: relative;
            transform-origin: center center;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #map-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
            filter: drop-shadow(0 20px 60px rgba(0, 0, 0, 0.3));
            border-radius: 20px;
        }
        
        #marker-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .marker {
            position: absolute;
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
            z-index: 10;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: auto;
            border: 2px solid white;
        }
        
        .marker:hover {
            transform: translate(-50%, -50%) scale(1.4);
            z-index: 20;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.8);
        }
        
        .marker.active {
            transform: translate(-50%, -50%) scale(1.3);
            box-shadow: 0 8px 35px rgba(0, 0, 0, 0.9);
        }
        
        .marker::after {
            content: '';
            position: absolute;
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6), 0 0 0 0 rgba(255, 255, 255, 0.7); }
            70% { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6), 0 0 0 15px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6), 0 0 0 0 rgba(255, 255, 255, 0); }
        }
        
        @keyframes activePulse {
            0% { box-shadow: 0 8px 35px rgba(0, 0, 0, 0.9), 0 0 0 0 rgba(255, 255, 255, 0.7); }
            70% { box-shadow: 0 8px 35px rgba(0, 0, 0, 0.9), 0 0 0 20px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 8px 35px rgba(0, 0, 0, 0.9), 0 0 0 0 rgba(255, 255, 255, 0); }
        }
        
        .marker {
            animation: pulse 2.5s infinite;
        }
        
        .marker.active {
            animation: activePulse 2s infinite;
        }
        
        .marker-info {
            position: fixed;
            top: 0;
            right: -450px;
            width: 420px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            padding: 30px;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.3);
            z-index: 30;
            overflow-y: auto;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(20px);
            border-left: 2px solid rgba(102, 126, 234, 0.3);
        }
        
        .marker-info.active {
            right: 0;
        }
        
        .marker-info img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .marker-info img:hover {
            transform: scale(1.02);
        }
        
        .marker-info h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .marker-info p {
            color: #555;
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        .close-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
        }
        
        .close-info:hover {
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 6px 25px rgba(245, 87, 108, 0.6);
        }
        
        .controls {
            position: absolute;
            top: 30px;
            right: 30px;
            z-index: 20;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .control-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(102, 126, 234, 0.3);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #667eea;
            backdrop-filter: blur(10px);
        }
        
        .control-btn:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.15);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
        }
        
        .coordinates {
            position: absolute;
            bottom: 30px;
            left: 30px;
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            z-index: 15;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 30;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .back-button::before {
            content: "←";
            font-size: 1.2rem;
        }
        
        .loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 100;
            color: white;
        }
        
        .loading-letters {
            display: flex;
            gap: 10px;
            margin-bottom: 4rem;
        }
        
        .letter {
            font-size: 4rem;
            font-weight: 800;
            opacity: 0;
            transform: translateY(30px);
            animation: letterAppear 0.5s ease forwards;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        .loading-spinner {
            width: 70px;
            height: 70px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid white;
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
            opacity: 0;
            animation: spinnerAppear 1s ease 4s forwards, spin 1.5s linear 4s infinite;
        }
        
        .loading-progress {
            width: 350px;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            margin-top: 2.5rem;
            overflow: hidden;
            opacity: 0;
            animation: progressAppear 1s ease 2s forwards;
        }
        
        .loading-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 10px;
            width: 0%;
            animation: progressFill 3s ease-in-out forwards;
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.8);
        }
        
        @keyframes letterAppear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spinnerAppear {
            to {
                opacity: 1;
            }
        }
        
        @keyframes progressAppear {
            to {
                opacity: 1;
            }
        }
        
        @keyframes progressFill {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .letter {
                font-size: 2.5rem;
            }
            
            .loading-progress {
                width: 280px;
            }
            
            .marker-info {
                width: 100%;
                right: -100%;
            }
            
            .back-button {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .letter {
                font-size: 2rem;
            }
            
            .loading-progress {
                width: 220px;
            }
            
            .back-button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="map-container">
        <div id="map-wrapper">
            <a href="{{ route('welcome') }}" class="back-button">Orqaga</a>
            <div class="controls">
                <button class="control-btn" id="zoom-in" title="Zoom in">+</button>
                <button class="control-btn" id="zoom-out" title="Zoom out">−</button>
                <button class="control-btn" id="reset-view" title="Reset view">⟲</button>
            </div>
            
            <!-- <div class="coordinates" id="coordinates">
                📍 Koordinatalar: 0%, 0%
            </div> -->
            
            <div id="map-inner">
                <img id="map-image" src="{{ asset('map/uzbekistan-map.png') }}" alt="O'zbekiston xaritasi">
                <div id="marker-container"></div>
            </div>
            
            <div class="marker-info" id="marker-info">
                <button class="close-info" id="close-info">×</button>
                <img id="info-image" src="" alt="Marker image">
                <h3 id="info-title">Sarlavha</h3>
                <p id="info-description">Tavsif</p>
            </div>
            
            <div class="loading" id="loading">
                <div class="loading-letters" id="loading-letters"></div>
                <div class="loading-spinner"></div>
                <div class="loading-progress">
                    <div class="loading-progress-bar"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('map-wrapper');
    const mapInner = document.getElementById('map-inner');
    const markerContainer = document.getElementById('marker-container');
    const markerInfo = document.getElementById('marker-info');
    const infoImage = document.getElementById('info-image');
    const infoTitle = document.getElementById('info-title');
    const infoDescription = document.getElementById('info-description');
    const closeInfo = document.getElementById('close-info');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const resetViewBtn = document.getElementById('reset-view');
    const mapImage = document.getElementById('map-image');
    const loading = document.getElementById('loading');
    const loadingLetters = document.getElementById('loading-letters');

    const maps = @json($maps);

    let scale = 1;
    let initialScale = 1;
    const zoomSpeed = 0.2;
    const minScale = 0.7;
    const maxScale = 3;
    let translateX = 0;
    let translateY = 0;
    let isPanning = false;
    let startX, startY;
    let imageNaturalWidth = 0;
    let imageNaturalHeight = 0;
    let boundaries = { minX: 0, maxX: 0, minY: 0, maxY: 0 };
    let lastTouchTime = 0;
    const throttleDelay = 16; // ~60fps

    function calculateBoundaries() {
        if (!imageNaturalWidth || !imageNaturalHeight) return;

        const wrapperRect = wrapper.getBoundingClientRect();
        const imageDisplayWidth = mapImage.offsetWidth;
        const imageDisplayHeight = mapImage.offsetHeight;
        
        const scaledWidth = imageDisplayWidth * scale;
        const scaledHeight = imageDisplayHeight * scale;

        const overflowX = Math.max(0, (scaledWidth - wrapperRect.width) / 2);
        const overflowY = Math.max(0, (scaledHeight - wrapperRect.height) / 2);
        boundaries.minX = -overflowX / scale;
        boundaries.maxX = overflowX / scale;
        boundaries.minY = -overflowY / scale;
        boundaries.maxY = overflowY / scale;
    }

    function applyBoundaryConstraints() {
        translateX = Math.max(boundaries.minX, Math.min(boundaries.maxX, translateX));
        translateY = Math.max(boundaries.minY, Math.min(boundaries.maxY, translateY));
    }

    function applyTransform() {
        mapInner.style.transition = isPanning ? 'none' : 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        mapInner.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
    }

    function initLoadingAnimation() {
        const word = "UZBEKISTAN\n\nTRAVEL";
        let delay = 0;
        
        for (let i = 0; i < word.length; i++) {
            const letter = document.createElement('span');
            letter.className = 'letter';
            letter.textContent = word[i];
            letter.style.animationDelay = `${delay}s`;
            loadingLetters.appendChild(letter);
            delay += 0.2;
        }
        
        setTimeout(() => {
            loading.style.opacity = '0';
            setTimeout(() => {
                loading.style.display = 'none';
                initMap();
            }, 1000);
        }, 5000);
    }

    function initMap() {
        const rect = wrapper.getBoundingClientRect();
        imageNaturalWidth = mapImage.naturalWidth;
        imageNaturalHeight = mapImage.naturalHeight;
        
        const scaleW = rect.width / imageNaturalWidth;
        const scaleH = rect.height / imageNaturalHeight;
        initialScale = Math.min(scaleW, scaleH);
        scale = initialScale * 1.5;
        
        translateX = 0;
        translateY = 0;
        
        calculateBoundaries();
        createMarkers();
        adjustMarkerPositions();
        applyTransform();
        zoomToDefaultMarker();
    }

    function createMarkers() {
        markerContainer.innerHTML = '';
        
        maps.forEach(map => {
            const markerEl = document.createElement('div');
            markerEl.classList.add('marker');
            markerEl.dataset.id = map.id;
            markerEl.dataset.lat = map.latitude;
            markerEl.dataset.lng = map.longitude;
            markerEl.title = map.name_uz;
            
            markerEl.addEventListener('click', function(e) {
                e.stopPropagation();
                zoomToMarker(markerEl, map);
            });
            
            markerContainer.appendChild(markerEl);
        });
    }

    function adjustMarkerPositions() {
        const contWidth = mapInner.offsetWidth;
        const contHeight = mapInner.offsetHeight;
        const natW = imageNaturalWidth;
        const natH = imageNaturalHeight;

        if (!natW || !natH) return;

        const fitScale = Math.min(contWidth / natW, contHeight / natH);
        const dispW = natW * fitScale;
        const dispH = natH * fitScale;
        const offsetX = (contWidth - dispW) / 2;
        const offsetY = (contHeight - dispH) / 2;

        document.querySelectorAll('.marker').forEach(marker => {
            const id = marker.dataset.id;
            const mapData = maps.find(m => m.id == id);
            if (!mapData) return;

            const imgX = (mapData.longitude / 100) * natW;
            const dispX = imgX * fitScale;
            const contX = offsetX + dispX;
            const percX = (contX / contWidth) * 100;

            const imgY = (mapData.latitude / 100) * natH;
            const dispY = imgY * fitScale;
            const contY = offsetY + dispY;
            const percY = (contY / contHeight) * 100;

            marker.style.left = `${percX}%`;
            marker.style.top = `${percY}%`;
        });
    }

    function zoomToMarker(markerEl, map) {
        document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        markerEl.classList.add('active');
        showMarkerInfo(map);

        const markerRect = markerEl.getBoundingClientRect();
        const wrapperRect = wrapper.getBoundingClientRect();
        
        const markerCenterX = markerRect.left + markerRect.width / 2 - wrapperRect.left;
        const markerCenterY = markerRect.top + markerRect.height / 2 - wrapperRect.top;
        
        const targetX = (wrapperRect.width - 420) / 2;
        const targetY = wrapperRect.height / 2;
        
        const targetScale = Math.min(maxScale, scale * 1.5);
        
        const offsetX = (targetX - markerCenterX) / scale;
        const offsetY = (targetY - markerCenterY) / scale;
        
        translateX += offsetX;
        translateY += offsetY;
        scale = targetScale;
        
        calculateBoundaries();
        applyBoundaryConstraints();
        applyTransform();
    }

    function zoomToDefaultMarker() {
        if (maps.length > 0) {
            const defaultMarker = document.querySelector('.marker');
            if (defaultMarker) {
                const defaultMap = maps.find(m => m.id == defaultMarker.dataset.id);
                zoomToMarker(defaultMarker, defaultMap);
            }
        }
    }

    function showMarkerInfo(map) {
        infoImage.src = map.image ? `/storage/${map.image}` : '/placeholder-image.jpg';
        infoTitle.textContent = map.name_uz;
        infoDescription.textContent = map.description_uz;
        markerInfo.classList.add('active');
    }

    function zoomIn() {
        scale = Math.min(maxScale, scale + zoomSpeed);
        calculateBoundaries();
        applyBoundaryConstraints();
        applyTransform();
    }

    function zoomOut() {
        scale = Math.max(minScale, scale - zoomSpeed);
        calculateBoundaries();
        applyBoundaryConstraints();
        applyTransform();
    }

    function resetView() {
        const rect = wrapper.getBoundingClientRect();
        const scaleW = rect.width / imageNaturalWidth;
        const scaleH = rect.height / imageNaturalHeight;
        scale = Math.min(scaleW, scaleH) * 1.5;
        initialScale = scale;
        
        translateX = 0;
        translateY = 0;
        
        document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        markerInfo.classList.remove('active');
        
        calculateBoundaries();
        applyTransform();
    }

    // Event Listeners
    zoomInBtn.addEventListener('click', zoomIn);
    zoomOutBtn.addEventListener('click', zoomOut);
    resetViewBtn.addEventListener('click', resetView);
    
    closeInfo.addEventListener('click', resetView);

    wrapper.addEventListener('wheel', function(e) {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 1 : -1;
        scale = Math.min(Math.max(minScale, scale + delta * zoomSpeed), maxScale);
        calculateBoundaries();
        applyBoundaryConstraints();
        applyTransform();
    }, { passive: false });

    // Mouse panning
    wrapper.addEventListener('mousedown', function(e) {
        if (e.target === wrapper || e.target === mapInner || e.target.id === 'map-image') {
            isPanning = true;
            startX = e.clientX - translateX * scale;
            startY = e.clientY - translateY * scale;
            wrapper.style.cursor = 'grabbing';
        }
    });
    
    wrapper.addEventListener('mousemove', function(e) {
        if (!isPanning) return;
        
        translateX = (e.clientX - startX) / scale;
        translateY = (e.clientY - startY) / scale;
        
        applyBoundaryConstraints();
        requestAnimationFrame(applyTransform);
    });
    
    wrapper.addEventListener('mouseup', () => {
        isPanning = false;
        wrapper.style.cursor = 'grab';
    });
    
    wrapper.addEventListener('mouseleave', () => {
        isPanning = false;
        wrapper.style.cursor = 'grab';
    });

    // Touch panning with throttling
    wrapper.addEventListener('touchstart', function(e) {
        if (e.touches.length === 1 && (e.target === wrapper || e.target === mapInner || e.target.id === 'map-image')) {
            isPanning = true;
            startX = e.touches[0].clientX - translateX * scale;
            startY = e.touches[0].clientY - translateY * scale;
        }
    });
    
    wrapper.addEventListener('touchmove', function(e) {
        if (!isPanning || e.touches.length !== 1) return;
        e.preventDefault();
        
        const now = performance.now();
        if (now - lastTouchTime < throttleDelay) return;
        lastTouchTime = now;

        translateX = (e.touches[0].clientX - startX) / scale;
        translateY = (e.touches[0].clientY - startY) / scale;
        
        applyBoundaryConstraints();
        requestAnimationFrame(applyTransform);
    }, { passive: false });
    
    wrapper.addEventListener('touchend', () => {
        isPanning = false;
    });
    
    wrapper.addEventListener('touchcancel', () => {
        isPanning = false;
    });

    wrapper.addEventListener('click', function(e) {
        if (e.target === wrapper || e.target === mapInner || e.target.id === 'map-image') {
            markerInfo.classList.remove('active');
            document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        }
    });

    window.addEventListener('resize', function() {
        const rect = wrapper.getBoundingClientRect();
        const scaleW = rect.width / imageNaturalWidth;
        const scaleH = rect.height / imageNaturalHeight;
        scale = Math.min(scaleW, scaleH) * 1.5;
        initialScale = scale;
        
        translateX = 0;
        translateY = 0;
        
        calculateBoundaries();
        adjustMarkerPositions();
        applyTransform();
    });

    mapImage.onload = function() {
        imageNaturalWidth = mapImage.naturalWidth;
        imageNaturalHeight = mapImage.naturalHeight;
        initLoadingAnimation();
    };

    mapImage.onerror = function() {
        loading.innerHTML = `
            <div style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                <div style="font-size: 1.5rem; color: white;">Xarita yuklanmadi. Qaytadan urinib ko'ring.</div>
            </div>
        `;
    };
});
    </script>
</body>
</html>