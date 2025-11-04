{{-- resources/views/maps/index.blade.php --}}
<!DOCTYPE html>
<html lang="{{ $locale ?? 'uz' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.Uzbekistan Map – Interactive map') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo2.png') }}">

    <!-- Preload map image -->
    <link rel="preload" href="{{ asset('map/uzbekistan-map.png') }}?v={{ time() }}" as="image">

    <!-- Font Awesome + Inter -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-dark: #1C3F3A;
            --c-white: #FFFFFF;
            --c-light: #E0E9E9;
            --radius: 20px;
            --shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
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
            opacity: 1;
            min-height: 80px;
            text-align: center;
        }

        #animated-title {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .loading-spinner {
            margin-top: 2.5rem;
            width: 80px;
            height: 80px;
            border: 6px solid rgba(255,255,255,0.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

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

        #map-wrapper:active { cursor: grabbing; }

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

        .marker:hover { transform: translate(-50%, -50%) scale(1.4); }
        .marker.active { animation: activePulse 1.8s infinite; }

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

        /* INFO PANEL */
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

        .marker-info p { color: #444; line-height: 1.7; }

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

        .close-info:hover { transform: rotate(90deg) scale(1.1); }

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

        .send-email-btn:hover { transform: translateY(-2px); }

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

        .back-button:hover { transform: translateX(-5px); }
        .back-button::before { content: "{{ __('messages.Back') }}"; font-size: 1.2rem; }

        /* === EMAIL MODAL – EVENTS & LIBRARY BILAN BIR XIL === */
        .email-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .email-modal.show {
            display: flex;
            opacity: 1;
        }

        .modern-modal {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 420px;
            max-height: 75vh;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: modalPop 0.3s ease-out;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalPop {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Klaviatura chiqqanda – modal yuqoriga siljiydi */
        .keyboard-active .modern-modal {
            transform: translateY(-100px) !important;
        }

        @media (max-height: 700px) {
            .keyboard-active .modern-modal {
                transform: translateY(-80px) !important;
            }
        }

        @media (max-height: 600px) {
            .keyboard-active .modern-modal {
                transform: translateY(-60px) !important;
            }
        }

        .modal-header {
            padding: 18px 20px;
            color: black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        .modal-header p {
            margin: 0;
            font-size: 1.2rem;
        }

        .modal-close {
            background: none;
            border: none;
            color: #1a1a1a;
            font-size: 1.4rem;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.2);
        }

        .modal-body {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            color: black;
            
        }

        .material-info h3 {
            margin: 0 0 8px 0;
            font-size: 1.1rem;
            color: #1C3F3A;
        }

        .material-desc {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 12px 0 20px;
            word-break: break-word;
            max-height: 120px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .note {
            display: block;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #777;
            font-style: italic;
        }

        .modal-footer {
            padding: 16px 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            border-top: 1px solid #eee;
            flex-shrink: 0;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-primary {
            background: #1C3F3A;
            color: white;
        }

        .btn-primary:hover {
            background: #16332e;
        }

        /* === VIRTUAL KEYBOARD === */
        .virtual-keyboard {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #1a1a1a;
            padding: 12px 8px;
            z-index: 10001;
            border-top: 1px solid #333;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.2);
        }

        .virtual-keyboard.show {
            display: block;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .virtual-keyboard .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
            gap: 6px;
        }

        .virtual-keyboard button {
            flex: 1;
            min-width: 40px;
            max-width: 60px;
            padding: 14px 8px;
            background: linear-gradient(145deg, #2a2a2a, #1f1f1f);
            color: #fff;
            border: none;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .virtual-keyboard button:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .virtual-keyboard button.wide { flex: 2; }

        /* === LOADING & SUCCESS === */
        .loading-modal, .success-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .loading-modal.show, .success-modal.show {
            display: flex;
            opacity: 1;
        }

        .loading-animation {
            width: 80px; height: 80px;
            animation: rotate 2s linear infinite;
        }

        .loading-animation svg {
            width: 100%; height: 100%;
            filter: drop-shadow(0 0 5px rgba(0, 123, 255, 0.5));
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- LOADING -->
    <div id="loading">
        <div class="title-wrap" id="animated-title"></div>
        <div class="loading-spinner"></div>
    </div>

    <!-- MAP -->
    <div class="map-container">
        <div id="map-wrapper">
            <a href="{{ route('welcome') }}" class="back-button"></a>

            <div class="controls">
                <button class="control-btn" id="zoom-in" title="{{ __('messages.Zoom In') }}">+</button>
                <button class="control-btn" id="zoom-out" title="{{ __('messages.Zoom Out') }}">-</button>
                <button class="control-btn" id="reset-view" title="{{ __('messages.Reset') }}"><i class="fas fa-sync-alt"></i></button>
            </div>

            <div id="map-inner">
                <img id="map-image" src="{{ asset('map/uzbekistan-map.png') }}?v={{ time() }}" alt="Uzbekistan Map" loading="eager">
                <div id="marker-container"></div>
            </div>

            <!-- INFO PANEL -->
            <div class="marker-info" id="marker-info">
                <button class="close-info" id="close-info">×</button>
                <div class="image-carousel" id="image-carousel"></div>
                <h3 id="info-title"></h3>
                <p id="info-description"></p>
                <button class="send-email-btn" id="send-email-btn">
                    <i class="fas fa-envelope"></i> {{ __('messages.Send to email') }}
                </button>
            </div>
        </div>
    </div>

    <!-- EMAIL MODAL – EVENTS & LIBRARY BILAN BIR XIL -->
    <div class="email-modal" id="emailModal">
        <div class="modal-content modern-modal">
            <div class="modal-header">
                <p><strong>{{ __('messages.Send the material to your email') }}</strong></p>
                <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>

            <div class="modal-body">
                <div class="material-info">
                    <h3 id="modal-info-title"></h3>
                    <p id="modal-info-description" class="material-desc"></p>
                </div>

                <div class="form-group">
                    <label for="emailInput">{{ __('messages.Your email address') }}</label>
                    <input type="email" id="emailInput" class="form-input" placeholder="misol@gmail.com" required>
                    <small class="note">
                        {{ __('messages.* We will also send you the latest news and useful materials to your email address.') }}
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">{{ __('messages.Cancel') }}</button>
                <button class="btn btn-primary" onclick="sendEmail()">
                    <i class="fas fa-paper-plane"></i> {{ __('messages.Send to email') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Virtual Keyboard -->
    <div id="virtual-keyboard" class="virtual-keyboard"></div>

    <!-- Success Modal -->
    <div class="success-modal" id="successModal">
        <div class="modal-content modern-modal">
            <div class="modal-body">
                <p>{{ __('messages.Material sent successfully') }}</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeSuccessModal()">OK</button>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="loading-modal" id="loadingModal">
        <div class="loading-animation">
            <svg viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="40" fill="none" stroke="#007bff" stroke-width="6" stroke-dasharray="20,10"/>
                <circle cx="50" cy="50" r="30" fill="none" stroke="#ffd700" stroke-width="4" stroke-dasharray="15,10"/>
                <circle cx="50" cy="50" r="20" fill="none" stroke="#ff4500" stroke-width="3" stroke-dasharray="10,10"/>
            </svg>
        </div>
    </div>

<script>
    // Inactivity redirect
    let inactivityTime = 0;
    ['mousemove', 'click', 'scroll', 'keydown', 'touchstart'].forEach(ev => 
        document.addEventListener(ev, () => inactivityTime = 0)
    );
    setInterval(() => {
        if (++inactivityTime === 60) {
            window.location.href = "{{ route('welcome') }}";
        }
    }, 1000);

    const maps = @json($maps);
    const currentLang = '{{ $locale ?? "uz" }}';

    const $ = id => document.getElementById(id);
    const els = {
        wrapper: $('map-wrapper'),
        mapInner: $('map-inner'),
        mapImg: $('map-image'),
        markerCont: $('marker-container'),
        infoPanel: $('marker-info'),
        carousel: $('image-carousel'),
        title: $('info-title'),
        desc: $('info-description'),
        closeInfo: $('close-info'),
        sendBtn: $('send-email-btn'),
        zoomIn: $('zoom-in'),
        zoomOut: $('zoom-out'),
        reset: $('reset-view'),
        loading: $('loading'),
        modal: $('emailModal'),
        modalClose: $('emailModal').querySelector('.modal-close'),
        cancel: $('emailModal').querySelector('.btn-secondary'),
        send: $('emailModal').querySelector('.btn-primary'),
        emailInp: $('emailInput'),
        modalTitle: $('modal-info-title'),
        modalDesc: $('modal-info-description'),
        keyboard: $('virtual-keyboard'),
        animatedTitle: $('animated-title'),
        loadingModal: $('loadingModal'),
        successModal: $('successModal')
    };

    let scale = 1, minScale = 1, maxScale = 3;
    let tx = 0, ty = 0;
    let isPanning = false, startX = 0, startY = 0;
    const ZOOM_STEP = 0.25;
    let imgW = 0, imgH = 0;
    let currentMap = null;
    let isUpperCase = false;
    let keyboardShown = false;

    const keyboardLayout = [
        ['1','2','3','4','5','6','7','8','9','0','-','_','←'],
        ['q','w','e','r','t','y','u','i','o','p','@'],
        ['a','s','d','f','g','h','j','k','l','.','!','?'],
        ['CAPS','z','x','c','v','b','n','m',':',';','CAPS'],
    ];

    function showKeyboard() {
        if (keyboardShown) return;
        const keyboard = document.getElementById('virtual-keyboard');
        keyboard.classList.add('show');
        document.body.classList.add('keyboard-active');
        renderKeyboard();
        keyboardShown = true;
    }

    function hideKeyboard() {
        if (!keyboardShown) return;
        const keyboard = document.getElementById('virtual-keyboard');
        keyboard.classList.remove('show');
        document.body.classList.remove('keyboard-active');
        keyboardShown = false;
    }

    function renderKeyboard() {
        const keyboard = document.getElementById('virtual-keyboard');
        keyboard.innerHTML = '';

        keyboardLayout.forEach(row => {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'keyboard-row';

            row.forEach(key => {
                const btn = document.createElement('button');
                let displayKey = key;

                if (isUpperCase && key.length === 1 && !['@','!','?','.'].includes(key)) {
                    displayKey = key.toUpperCase();
                }
                if (key === '←') displayKey = '←';
                if (key === 'CAPS') displayKey = 'CAPS';

                btn.textContent = displayKey;
                btn.onclick = () => handleKeyPress(key);

                if (['CAPS', '←'].includes(key)) {
                    btn.className = 'special';
                    if (key === '←') btn.className += ' wide';
                }

                rowDiv.appendChild(btn);
            });

            keyboard.appendChild(rowDiv);
        });
    }

    function handleKeyPress(key) {
        const input = document.getElementById('emailInput');
        switch (key) {
            case '←':
                input.value = input.value.slice(0, -1);
                break;
            case 'Enter':
                sendEmail();
                break;
            case 'CAPS':
                isUpperCase = !isUpperCase;
                renderKeyboard();
                break;
            default:
                input.value += isUpperCase ? key.toUpperCase() : key;
                break;
        }
        input.focus();
    }

    function startLetterAnimation() {
        const text = "{{ __('messages.Uzbekistan Travel') }}";
        const titleEl = els.animatedTitle;
        titleEl.textContent = '';
        titleEl.style.opacity = '1';
        let i = 0;
        const interval = setInterval(() => {
            if (i < text.length) {
                titleEl.textContent += text[i++];
            } else {
                clearInterval(interval);
            }
        }, 120);
    }

    function clamp() {
        const wrapperW = els.wrapper.clientWidth;
        const wrapperH = els.wrapper.clientHeight;
        const scaledW = imgW * scale;
        const scaledH = imgH * scale;

        if (scaledW > wrapperW) {
            const maxTx = (scaledW - wrapperW) / (2 * scale);
            tx = Math.max(-maxTx, Math.min(maxTx, tx));
        } else tx = 0;

        if (scaledH > wrapperH) {
            const maxTy = (scaledH - wrapperH) / (2 * scale);
            ty = Math.max(-maxTy, Math.min(maxTy, ty));
        } else ty = 0;
    }

    function adjustMarkers() {
        const wrapperW = els.wrapper.clientWidth;
        const wrapperH = els.wrapper.clientHeight;
        const scaledW = imgW * scale;
        const scaledH = imgH * scale;
        const offsetX = scaledW > wrapperW ? 0 : (wrapperW - scaledW) / 2 / scale;
        const offsetY = scaledH > wrapperH ? 0 : (wrapperH - scaledH) / 2 / scale;

        document.querySelectorAll('.marker').forEach(m => {
            const d = maps.find(x => x.id == m.dataset.id);
            if (!d) return;
            const px = (d.longitude / 100) * imgW + offsetX;
            const py = (d.latitude / 100) * imgH + offsetY;
            m.style.left = `${(px / imgW) * 100}%`;
            m.style.top = `${(py / imgH) * 100}%`;
        });
    }

    function applyTransform() {
        els.mapInner.style.transition = isPanning ? 'none' : 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        els.mapInner.style.transform = `scale(${scale}) translate(${tx}px, ${ty}px)`;
    }

    function initMap() {
        const rect = els.wrapper.getBoundingClientRect();
        imgW = els.mapImg.naturalWidth;
        imgH = els.mapImg.naturalHeight;
        if (!imgW || !imgH) return showImageError();

        const fitW = rect.width / imgW;
        const fitH = rect.height / imgH;
        scale = Math.min(fitW, fitH) * 2;
        minScale = scale;
        tx = ty = 0;
        createMarkers();
        clamp();
        adjustMarkers();
        applyTransform();
    }

    function createMarkers() {
        els.markerCont.innerHTML = '';
        maps.forEach(m => {
            const el = document.createElement('div');
            el.className = 'marker';
            el.dataset.id = m.id;
            el.title = m.name;
            el.onclick = e => { e.stopPropagation(); zoomToMarker(el, m); };
            els.markerCont.appendChild(el);
        });
    }

    function zoomIn() { scale = Math.min(maxScale, scale + ZOOM_STEP); clamp(); adjustMarkers(); applyTransform(); }
    function zoomOut() { scale = Math.max(minScale, scale - ZOOM_STEP); clamp(); adjustMarkers(); applyTransform(); }

    function resetView() {
        const r = els.wrapper.getBoundingClientRect();
        scale = Math.min(r.width / imgW, r.height / imgH) * 2;
        minScale = scale;
        tx = ty = 0;
        document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        els.infoPanel.classList.remove('active');
        closeModal();
        clamp(); adjustMarkers(); applyTransform();
    }

    function showCarousel(url) {
        els.carousel.innerHTML = '';
        if (!url) {
            els.carousel.innerHTML = `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f8f9fa;color:#6c757d;font-size:1.1rem;">{{ __('messages.No image') }}</div>`;
            return;
        }
        const img = new Image();
        img.src = url;
        img.alt = 'Place';
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:16px;';
        img.onerror = () => img.src = 'https://via.placeholder.com/400x280?text=Not+Found';
        els.carousel.appendChild(img);
    }

    function zoomToMarker(el, data) {
        document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        el.classList.add('active');
        currentMap = data;
        els.title.textContent = data.name;
        els.desc.textContent = data.description || '{{ __('messages.No description.') }}';
        showCarousel(data.image_url);
        els.infoPanel.classList.add('active');

        const mr = el.getBoundingClientRect();
        const wr = els.wrapper.getBoundingClientRect();
        const cx = mr.left + mr.width / 2 - wr.left;
        const cy = mr.top + mr.height / 2 - wr.top;
        const targetX = (wr.width - 420) / 2;
        const targetY = wr.height / 2;
        const targetScale = Math.min(maxScale, scale * 1.5);

        tx += (targetX - cx) / scale;
        ty += (targetY - cy) / scale;
        scale = targetScale;

        clamp(); adjustMarkers(); applyTransform();
    }

    function openModal() {
        if (!currentMap) return alert('{{ __('messages.Select a location first!') }}');
        els.modalTitle.textContent = currentMap.name;
        els.modalDesc.textContent = currentMap.description;
        els.emailInp.value = '';
        els.modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        hideKeyboard();
    }

    function closeModal() {
        els.modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        els.emailInp.value = '';
        hideKeyboard();
    }

    function showLoading() {
        els.loadingModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function hideLoading() {
        els.loadingModal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function openSuccessModal() {
        els.successModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSuccessModal() {
        els.successModal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    async function sendEmail() {
        const mail = els.emailInp.value.trim();
        if (!mail || !/^\S+@\S+\.\S+$/.test(mail)) {
            alert('{{ __('messages.Valid email required!') }}');
            return;
        }

        closeModal();
        showLoading();

        try {
            const r = await fetch('{{ route("send.map.email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: mail, map_id: currentMap.id, lang: currentLang })
            });
            const j = await r.json();

            hideLoading();

            if (j.success) {
                openSuccessModal();
            } else {
                alert('{{ __('messages.Error') }}: ' + (j.error || 'unknown'));
            }
        } catch {
            hideLoading();
            alert('{{ __('messages.Network error!') }}');
        }
    }

    function showImageError() {
        els.loading.innerHTML = `
            <div style="color:#fff;text-align:center;padding:20px;">
                <div style="font-size:3rem;margin-bottom:20px;">Warning</div>
                <div style="font-size:1.5rem;margin-bottom:15px;">{{ __('messages.Map image not loaded') }}</div>
                <button onclick="location.reload()" 
                        style="padding:12px 24px;background:#f5576c;color:white;border:none;border-radius:12px;font-size:1rem;cursor:pointer;">
                    {{ __('messages.Refresh') }}
                </button>
            </div>
        `;
    }

    function hideLoadingAndInit() {
        const loading = els.loading;
        loading.style.transition = 'opacity 0.8s ease';
        loading.style.opacity = '0';
        setTimeout(() => {
            loading.style.display = 'none';
            initMap();
        }, 800);
    }

    function startAppWhenReady() {
        const img = els.mapImg;
        startLetterAnimation();

        if (img.complete && img.naturalWidth > 0 && img.naturalHeight > 0) {
            setTimeout(hideLoadingAndInit, 800);
            return;
        }

        let attempts = 0;
        const maxAttempts = 100;
        const check = setInterval(() => {
            if (img.naturalWidth > 0 && img.naturalHeight > 0) {
                clearInterval(check);
                setTimeout(hideLoadingAndInit, 800);
            } else if (++attempts >= maxAttempts) {
                clearInterval(check);
                showImageError();
            }
        }, 100);

        img.onload = () => { clearInterval(check); setTimeout(hideLoadingAndInit, 800); };
        img.onerror = () => { clearInterval(check); showImageError(); };
    }

    function bindEvents() {
        els.zoomIn.onclick = zoomIn;
        els.zoomOut.onclick = zoomOut;
        els.reset.onclick = resetView;
        els.closeInfo.onclick = resetView;
        els.sendBtn.onclick = openModal;
        els.modalClose.onclick = closeModal;
        els.cancel.onclick = closeModal;
        els.send.onclick = sendEmail;

        els.emailInp.addEventListener('focus', showKeyboard);
        document.getElementById('virtual-keyboard').addEventListener('mousedown', e => e.preventDefault());

        els.modal.onclick = e => e.target === els.modal && closeModal();
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (els.successModal.classList.contains('show')) closeSuccessModal();
                else if (els.modal.classList.contains('show')) closeModal();
            }
        });

        els.wrapper.addEventListener('wheel', e => {
            e.preventDefault();
            scale = Math.max(minScale, Math.min(maxScale, scale + (e.deltaY < 0 ? 1 : -1) * ZOOM_STEP));
            clamp(); adjustMarkers(); applyTransform();
        }, { passive: false });

        const mouse = {
            down: e => {
                if (e.target.closest('.marker')) return;
                if (imgW * scale <= els.wrapper.clientWidth && imgH * scale <= els.wrapper.clientHeight) return;
                isPanning = true;
                startX = e.clientX - tx * scale;
                startY = e.clientY - ty * scale;
                els.wrapper.style.cursor = 'grabbing';
            },
            move: e => {
                if (!isPanning) return;
                tx = (e.clientX - startX) / scale;
                ty = (e.clientY - startY) / scale;
                clamp();
                requestAnimationFrame(() => { applyTransform(); adjustMarkers(); });
            },
            up: () => { isPanning = false; els.wrapper.style.cursor = 'grab'; }
        };
        ['mousedown', 'mousemove', 'mouseup', 'mouseleave'].forEach((ev, i) => 
            els.wrapper.addEventListener(ev, mouse[['down','move','up','up'][i]])
        );

        els.wrapper.addEventListener('touchstart', e => {
            if (e.touches.length !== 1) return;
            isPanning = true;
            startX = e.touches[0].clientX - tx * scale;
            startY = e.touches[0].clientY - ty * scale;
        }, { passive: true });
        els.wrapper.addEventListener('touchmove', e => {
            if (!isPanning || e.touches.length !== 1) return;
            e.preventDefault();
            tx = (e.touches[0].clientX - startX) / scale;
            ty = (e.touches[0].clientY - startY) / scale;
            clamp();
            requestAnimationFrame(() => { applyTransform(); adjustMarkers(); });
        }, { passive: false });
        els.wrapper.addEventListener('touchend', () => isPanning = false);

        els.wrapper.onclick = e => {
            if (e.target.closest('.marker,.marker-info,.controls')) return;
            els.infoPanel.classList.remove('active');
            closeModal();
            document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        };

        window.addEventListener('resize', () => { clamp(); adjustMarkers(); applyTransform(); });
    }

    function init() {
        bindEvents();
        startAppWhenReady();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
</script>
</body>
</html>