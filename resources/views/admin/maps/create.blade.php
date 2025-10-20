@extends('layouts.app')
@section('content')
    <style>
        .marker {
            position: absolute;
            width: 16px;
            height: 16px;
            background-color: #dc3545;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 10;
            transition: all 0.2s ease;
        }
        .marker:hover {
            background-color: #c82333;
            transform: translate(-50%, -50%) scale(1.3);
        }
        .marker.active {
            background-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.4);
        }
        #map-wrapper {
            position: relative;
            width: 100%;
            height: 80vh;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            cursor: grab;
            background: #f8f9fa;
        }
        #map-wrapper:active {
            cursor: grabbing;
        }
        #map-inner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform-origin: 0 0;
            transition: transform 0.3s ease;
        }
        #map-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        #marker-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        #marker-container .marker {
            pointer-events: auto;
        }
        .zoom-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 5;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .zoom-controls button {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-size: 14px;
        }
        .coordinates-display {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px 12px;
            border-radius: 18px;
            font-size: 13px;
            font-weight: 500;
            z-index: 5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid #dee2e6;
        }
        .instructions {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            z-index: 5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid #dee2e6;
            max-width: 280px;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
            border: 1px solid #dee2e6;
        }
        .boundary-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px dashed rgba(220, 53, 69, 0.5);
            pointer-events: none;
            z-index: 2;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h4 class="mb-4 text-dark fw-bold">Yangi Harita Yaratish</h4>

        <!-- 🔍 Zoom & Marker konteyner -->
        <div id="map-wrapper">
            <div class="instructions">
                <small class="text-muted">
                    <strong>Yo'riqnoma:</strong><br>
                    • Xaritada joyni belgilash uchun bosing<br>
                    • Marker ustiga bosing va sudrab boshqa joyga o'tkazing<br>
                    • Marker o'chirish uchun ustiga ikki marta bosing
                </small>
            </div>
            
            <div class="zoom-controls">
                <button class="btn btn-light" id="zoom-in" title="Zoom in">+</button>
                <button class="btn btn-light" id="zoom-out" title="Zoom out">-</button>
                <button class="btn btn-light" id="reset-view" title="Reset view">⟲</button>
            </div>
            
            <div class="coordinates-display" id="coordinates-display">
                📍 Koordinatalar: belgilanmagan
            </div>
            
            <div id="map-inner">
                <img id="map-image" src="{{ asset('map/uzbekistan-map.png') }}" alt="O'zbekiston Xaritasi">
                <div id="marker-container"></div>
                <div class="boundary-indicator" id="boundary-indicator"></div>
            </div>
        </div>

        <!-- Ma'lumot kiritish formasi -->
        <div class="form-container">
            <form id="map-form" method="POST" action="{{ route('admin.maps.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nomi: O'zbekcha</label>
                        <input type="text" name="name_uz" class="form-control" placeholder="Joy nomini kiriting" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nomi: English</label>
                        <input type="text" name="name_en" class="form-control" placeholder="Joy nomini kiriting" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nomi: Русский</label>
                        <input type="text" name="name_ru" class="form-control" placeholder="Joy nomini kiriting" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Rasm yuklash:</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tavsif: O'zbekcha</label>
                    <textarea name="description_uz" class="form-control" rows="3" placeholder="Joy haqida qisqacha tavsif..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tavsif: English</label>
                    <textarea name="description_en" class="form-control" rows="3" placeholder="Joy haqida qisqacha tavsif..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tavsif: Русский</label>
                    <textarea name="description_ru" class="form-control" rows="3" placeholder="Joy haqida qisqacha tavsif..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-text">
                        <span id="marker-status" class="text-muted">Marker joyi: belgilanmagan</span>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">
                        Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('map-wrapper');
            const mapInner = document.getElementById('map-inner');
            const markerContainer = document.getElementById('marker-container');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const coordinatesDisplay = document.getElementById('coordinates-display');
            const markerStatus = document.getElementById('marker-status');
            const zoomInBtn = document.getElementById('zoom-in');
            const zoomOutBtn = document.getElementById('zoom-out');
            const resetViewBtn = document.getElementById('reset-view');
            const boundaryIndicator = document.getElementById('boundary-indicator');
            const mapImage = document.getElementById('map-image');

            // Zoom va pan parametrlari
            let scale = 1;
            const zoomSpeed = 0.2;
            const minScale = 0.8;
            const maxScale = 3;
            let translateX = 0;
            let translateY = 0;
            let isPanning = false;
            let startX, startY;
            let currentMarker = null;
            let isDraggingMarker = false;

            // Xarita chegara parametrlari
            let maxTranslateX = 0;
            let maxTranslateY = 0;
            let minTranslateX = 0;
            let minTranslateY = 0;

            // Xarita tabiiy o'lchamlari
            let imageNaturalWidth = 0;
            let imageNaturalHeight = 0;

            // Chegaralarni hisoblash
            function calculateBoundaries() {
                const wrapperRect = wrapper.getBoundingClientRect();
                const mapRect = mapInner.getBoundingClientRect();
                
                if (scale > 1) {
                    maxTranslateX = (mapRect.width * scale - wrapperRect.width) / 2;
                    minTranslateX = -maxTranslateX;
                    maxTranslateY = (mapRect.height * scale - wrapperRect.height) / 2;
                    minTranslateY = -maxTranslateY;
                } else {
                    maxTranslateX = 0;
                    minTranslateX = 0;
                    maxTranslateY = 0;
                    minTranslateY = 0;
                }
                
                updateBoundaryIndicator();
            }

            function updateBoundaryIndicator() {
                if (scale > 1) {
                    boundaryIndicator.style.display = 'block';
                    boundaryIndicator.style.width = `${wrapper.offsetWidth / scale}px`;
                    boundaryIndicator.style.height = `${wrapper.offsetHeight / scale}px`;
                } else {
                    boundaryIndicator.style.display = 'none';
                }
            }

            function applyBoundaryConstraints() {
                translateX = Math.max(minTranslateX, Math.min(maxTranslateX, translateX));
                translateY = Math.max(minTranslateY, Math.min(maxTranslateY, translateY));
            }

            // Transformatsiyani qo'llash
            function applyTransform() {
                mapInner.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
            }

            // Zoom funksiyalari
            function zoomIn() {
                const newScale = Math.min(maxScale, scale + zoomSpeed);
                applyZoom(newScale, wrapper.offsetWidth / 2, wrapper.offsetHeight / 2);
            }

            function zoomOut() {
                const newScale = Math.max(minScale, scale - zoomSpeed);
                applyZoom(newScale, wrapper.offsetWidth / 2, wrapper.offsetHeight / 2);
            }

            function resetView() {
                scale = 1;
                translateX = 0;
                translateY = 0;
                applyTransform();
                calculateBoundaries();
            }

            function applyZoom(newScale, centerX, centerY) {
                const zoomFactor = newScale / scale;
                
                translateX = centerX - (centerX - translateX) * zoomFactor;
                translateY = centerY - (centerY - translateY) * zoomFactor;
                
                scale = newScale;
                applyTransform();
                calculateBoundaries();
                applyBoundaryConstraints();
                applyTransform();
            }

            // 🔍 Mouse wheel bilan zoom
            wrapper.addEventListener('wheel', function(e) {
                e.preventDefault();
                const delta = e.deltaY < 0 ? 1 : -1;
                const newScale = Math.min(Math.max(minScale, scale + delta * zoomSpeed), maxScale);
                
                const rect = wrapper.getBoundingClientRect();
                const offsetX = e.clientX - rect.left;
                const offsetY = e.clientY - rect.top;

                applyZoom(newScale, offsetX, offsetY);
            });

            // ✋ Pan qilish
            wrapper.addEventListener('mousedown', function(e) {
                if (e.target === wrapper || e.target === mapInner || e.target.id === 'map-image') {
                    isPanning = true;
                    startX = e.clientX - translateX;
                    startY = e.clientY - translateY;
                    wrapper.style.cursor = 'grabbing';
                }
            });
            
            wrapper.addEventListener('mousemove', function(e) {
                if (!isPanning && !isDraggingMarker) return;
                
                if (isPanning) {
                    translateX = e.clientX - startX;
                    translateY = e.clientY - startY;
                    applyBoundaryConstraints();
                    applyTransform();
                }
                
                if (isDraggingMarker && currentMarker) {
                    updateMarkerPosition(e.clientX, e.clientY);
                }
            });
            
            wrapper.addEventListener('mouseup', () => {
                isPanning = false;
                isDraggingMarker = false;
                wrapper.style.cursor = 'grab';
                if (currentMarker) {
                    currentMarker.classList.remove('active');
                }
            });
            
            wrapper.addEventListener('mouseleave', () => {
                isPanning = false;
                isDraggingMarker = false;
                wrapper.style.cursor = 'grab';
                if (currentMarker) {
                    currentMarker.classList.remove('active');
                }
            });

            // 📍 Marker qo'yish va boshqarish
            wrapper.addEventListener('click', function(event) {
                if ((event.target === wrapper || event.target === mapInner || event.target.id === 'map-image') && !isDraggingMarker) {
                    placeMarker(event.clientX, event.clientY);
                }
            });

            function placeMarker(clientX, clientY) {
                const rect = wrapper.getBoundingClientRect();
                
                // Marker joylashuvi (wrapper nisbatan)
                const markerX = clientX - rect.left;
                const markerY = clientY - rect.top;
                
                // Xarita koordinatalariga o'tkazish (zoom va pan hisobida)
                const mapX = (markerX - translateX) / scale;
                const mapY = (markerY - translateY) / scale;
                
                // object-fit: contain uchun offsetlarni hisoblash
                const contWidth = rect.width;
                const contHeight = rect.height;
                const natW = imageNaturalWidth;
                const natH = imageNaturalHeight;
                
                if (natW === 0 || natH === 0) return; // Rasm yuklanmagan
                
                const fitScale = Math.min(contWidth / natW, contHeight / natH);
                const dispW = natW * fitScale;
                const dispH = natH * fitScale;
                const offsetX = (contWidth - dispW) / 2;
                const offsetY = (contHeight - dispH) / 2;
                
                // Marker xarita ichida ekanligini tekshirish
                if (mapX < offsetX || mapX > offsetX + dispW || mapY < offsetY || mapY > offsetY + dispH) {
                    return; // Xarita chegarasidan tashqarida - marker qo'ymaymiz
                }
                
                // Rasm nisbatan pozitsiya
                const imgX = (mapX - offsetX) / fitScale;
                const imgY = (mapY - offsetY) / fitScale;
                
                // Foizga o'tkazish (tabiiy o'lcham bo'yicha)
                const xPercent = (imgX / natW) * 100;
                const yPercent = (imgY / natH) * 100;
                
                // Marker yaratish yoki yangilash
                if (!currentMarker) {
                    currentMarker = document.createElement('div');
                    currentMarker.classList.add('marker');
                    markerContainer.appendChild(currentMarker);
                    
                    currentMarker.addEventListener('mousedown', function(e) {
                        e.stopPropagation();
                        isDraggingMarker = true;
                        currentMarker.classList.add('active');
                    });
                    
                    currentMarker.addEventListener('dblclick', function(e) {
                        e.stopPropagation();
                        removeMarker();
                    });
                }
                
                // Marker pozitsiyasini konteyner foizida saqlaymiz (displey uchun)
                const contXPercent = (mapX / contWidth) * 100;
                const contYPercent = (mapY / contHeight) * 100;
                currentMarker.style.left = `${contXPercent}%`;
                currentMarker.style.top = `${contYPercent}%`;
                
                // Formaga rasm foizlarini yozamiz
                updateFormCoordinates(xPercent, yPercent);
            }

            function updateMarkerPosition(clientX, clientY) {
                const rect = wrapper.getBoundingClientRect();
                const markerX = clientX - rect.left;
                const markerY = clientY - rect.top;
                
                const mapX = (markerX - translateX) / scale;
                const mapY = (markerY - translateY) / scale;
                
                const contWidth = rect.width;
                const contHeight = rect.height;
                const natW = imageNaturalWidth;
                const natH = imageNaturalHeight;
                
                const fitScale = Math.min(contWidth / natW, contHeight / natH);
                const dispW = natW * fitScale;
                const dispH = natH * fitScale;
                const offsetX = (contWidth - dispW) / 2;
                const offsetY = (contHeight - dispH) / 2;
                
                // Drag vaqtida chegaralarni cheklash
                const clampedMapX = Math.max(offsetX, Math.min(mapX, offsetX + dispW));
                const clampedMapY = Math.max(offsetY, Math.min(mapY, offsetY + dispH));
                
                const imgX = (clampedMapX - offsetX) / fitScale;
                const imgY = (clampedMapY - offsetY) / fitScale;
                
                const xPercent = (imgX / natW) * 100;
                const yPercent = (imgY / natH) * 100;
                
                const contXPercent = (clampedMapX / contWidth) * 100;
                const contYPercent = (clampedMapY / contHeight) * 100;
                
                currentMarker.style.left = `${contXPercent}%`;
                currentMarker.style.top = `${contYPercent}%`;
                
                updateFormCoordinates(xPercent, yPercent);
            }

            function updateFormCoordinates(x, y) {
                lngInput.value = x.toFixed(4); // longitude = x
                latInput.value = y.toFixed(4); // latitude = y
                
                coordinatesDisplay.textContent = `📍 ${x.toFixed(2)}%, ${y.toFixed(2)}%`;
                markerStatus.textContent = `Marker: ${x.toFixed(2)}%, ${y.toFixed(2)}%`;
                markerStatus.className = 'text-success fw-semibold';
            }

            function removeMarker() {
                if (currentMarker) {
                    markerContainer.removeChild(currentMarker);
                    currentMarker = null;
                    
                    latInput.value = '';
                    lngInput.value = '';
                    coordinatesDisplay.textContent = '📍 Koordinatalar: belgilanmagan';
                    markerStatus.textContent = 'Marker joyi: belgilanmagan';
                    markerStatus.className = 'text-muted';
                }
            }

            // Zoom tugmalari
            zoomInBtn.addEventListener('click', zoomIn);
            zoomOutBtn.addEventListener('click', zoomOut);
            resetViewBtn.addEventListener('click', resetView);
            
            // O'lcham o'zgarishlarini kuzatish va marker pozitsiyasini yangilash
            window.addEventListener('resize', function() {
                calculateBoundaries();
                applyBoundaryConstraints();
                applyTransform();
                if (currentMarker && latInput.value && lngInput.value) {
                    adjustMarkerDisplayPosition(parseFloat(lngInput.value), parseFloat(latInput.value));
                }
            });

            // Marker displey pozitsiyasini yangilash (resize vaqtida)
            function adjustMarkerDisplayPosition(xPercent, yPercent) {
                const rect = wrapper.getBoundingClientRect();
                const contWidth = rect.width;
                const contHeight = rect.height;
                const natW = imageNaturalWidth;
                const natH = imageNaturalHeight;
                
                if (natW === 0 || natH === 0) return;
                
                const fitScale = Math.min(contWidth / natW, contHeight / natH);
                const dispW = natW * fitScale;
                const dispH = natH * fitScale;
                const offsetX = (contWidth - dispW) / 2;
                const offsetY = (contHeight - dispH) / 2;
                
                const imgX = (xPercent / 100) * natW;
                const dispX = imgX * fitScale;
                const contX = offsetX + dispX;
                const contXPercent = (contX / contWidth) * 100;
                
                const imgY = (yPercent / 100) * natH;
                const dispY = imgY * fitScale;
                const contY = offsetY + dispY;
                const contYPercent = (contY / contHeight) * 100;
                
                if (currentMarker) {
                    currentMarker.style.left = `${contXPercent}%`;
                    currentMarker.style.top = `${contYPercent}%`;
                }
            }

            // Boshlang'ich sozlash
            mapImage.onload = function() {
                imageNaturalWidth = mapImage.naturalWidth;
                imageNaturalHeight = mapImage.naturalHeight;
                resetView();
            };
            
            // Form yuborishni tekshirish
            document.getElementById('map-form').addEventListener('submit', function(e) {
                if (!latInput.value || !lngInput.value) {
                    e.preventDefault();
                    alert('Iltimos, avval xaritada joy belgilang!');
                    return false;
                }
            });
        });
    </script>
@endsection