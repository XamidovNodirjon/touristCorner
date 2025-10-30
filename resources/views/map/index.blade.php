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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--c-dark);
            color: var(--c-white);
            min-height: 100vh;
            overflow: hidden;
        }

        /* LOADING — YANGI: HARFMA-HARF + SPINNER */
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

        /* EMAIL MODAL */
        .email-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 100;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 20px;
        }

        .email-modal.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .modal-content {
            background: #ffffff;
            padding: 32px;
            border-radius: 24px;
            width: 90%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
            animation: modalPop 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            position: relative;
            border: 1px solid rgba(28, 63, 58, 0.1);
        }

        @keyframes modalPop {
            0% { opacity: 0; transform: scale(0.8) translateY(-40px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eee;
        }

        .modal-title {
            font-size: 1.55rem;
            font-weight: 500;
            background: linear-gradient(135deg, var(--c-dark), #2c5a57);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .modal-close-btn {
            background: #f1f3f5;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.3rem;
            color: #6c757d;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .modal-close-btn:hover {
            background: #e9ecef;
            color: #333;
            transform: scale(1.1);
        }

        .modal-info-box {
            background: linear-gradient(135deg, #f8f9fa, #e9f5f2);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            border-left: 5px solid var(--c-dark);
            box-shadow: 0 4px 15px rgba(28, 63, 58, 0.08);
            max-height: 180px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #a0c4c0 #f1f3f5;
        }

        .modal-info-box::-webkit-scrollbar { width: 6px; }
        .modal-info-box::-webkit-scrollbar-track { background: #f1f3f5; border-radius: 10px; }
        .modal-info-box::-webkit-scrollbar-thumb { background: #a0c4c0; border-radius: 10px; }
        .modal-info-box::-webkit-scrollbar-thumb:hover { background: #8ab0ac; }

        .info-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .info-title-wrap i { color: var(--c-dark); font-size: 1.3rem; }
        .info-title { margin: 0; font-size: 1.35rem; font-weight: 700; color: #1a3a36; }
        .info-desc { margin: 0; color: #555; font-size: 1rem; line-height: 1.6; }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-weight: 700;
            color: #1C3F3A;
            font-size: 1.05rem;
        }

        .form-input {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid #d1d9e0;
            border-radius: 14px;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--c-dark);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(28, 63, 58, 0.15);
            transform: translateY(-1px);
        }

        .input-hint {
            display: block;
            margin-top: 8px;
            font-size: 0.875rem;
            color: #6c757d;
            font-style: italic;
        }

        .modal-footer {
            margin: 24px -32px -32px;
            padding: 20px 32px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
        }

        .modal-buttons {
            display: flex;
            gap: 14px;
            justify-content: flex-end;
            flex-wrap: nowrap;
        }

        .btn {
            padding: 13px 26px;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.02rem;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 110px;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--c-dark), #2c5a57);
            color: #fff;
            box-shadow: 0 6px 20px rgba(28, 63, 58, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(28, 63, 58, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }

        .btn-secondary:hover {
            background: #dee2e6;
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .modal-content { padding: 24px; border-radius: 20px; }
            .modal-buttons { flex-direction: column; }
            .btn { width: 100%; }
        }

        /* VIRTUAL KEYBOARD */
        #virtual-keyboard {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #2c2c2c;
            padding: 10px 8px;
            box-shadow: 0 -6px 25px rgba(0,0,0,0.3);
            z-index: 110;
            display: none;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .keyboard-key {
            min-width: 45px;
            height: 50px;
            background: #3a3a3a;
            color: #f0f0f0;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.3);
            text-transform:lowercase;
        }

        .keyboard-key:hover {
            background: #4a4a4a;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.35);
        }

        .keyboard-key:active {
            transform: translateY(0);
        }

        .keyboard-key[data-key="Enter"] {
            background: #43a047;
            color: white;
            min-width: 70px;
        }

        .keyboard-key[data-key="CAPS"].caps-active {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }

        @media (max-width: 768px) {
            .keyboard-key {
                min-width: 38px;
                height: 44px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <!-- LOADING — HARFMA-HARF + SPINNER -->
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
                <img
                    id="map-image" 
                    src="{{ asset('map/uzbekistan-map.png') }}?v={{ time() }}" 
                    alt="Uzbekistan Map"
                    loading="eager"
                    crossorigin="anonymous"
                >
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

    <!-- EMAIL MODAL -->
    <div class="email-modal" id="email-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">{{ __('messages.Send to email') }}</h2>
                <button class="modal-close-btn" id="modal-close" aria-label="{{ __('messages.Close') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-info-box">
                <div class="info-title-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3 id="modal-info-title" class="info-title"></h3>
                </div>
                <p id="modal-info-description" class="info-desc"></p>
            </div>

            <div class="form-group">
                <label class="form-label">
                    {{ __('messages.📧 Your email address') }}
                </label>
                <input 
                    type="email" 
                    id="email-input" 
                    class="form-input" 
                    placeholder="example@gmail.com" 
                    autocomplete="off"
                    aria-label="{{ __('messages.Enter your email') }}"
                >
                <small class="input-hint">{{ __('messages.* We will also send you the latest news and useful materials to your email address.') }}</small>
            </div>

            <div class="modal-footer">
                <div class="modal-buttons">
                    <button class="btn btn-secondary" id="cancel-btn">
                        <i class="fas fa-ban"></i> {{ __('messages.Cancel') }}
                    </button>
                    <button class="btn btn-primary" id="send-btn">
                        <i class="fas fa-paper-plane"></i> <span>{{ __('messages.Send to email') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIRTUAL KEYBOARD -->
    <div id="virtual-keyboard"></div>

<script>
    // Inactivity redirect
    let inactivityTime = 0;
    ['mousemove', 'click', 'scroll', 'keydown'].forEach(ev => 
        document.addEventListener(ev, () => inactivityTime = 0)
    );
    setInterval(() => {
        if (++inactivityTime === 60) {
            window.location.href = "{{ route('welcome') }}";
        }
    }, 1000);

    /* ============================================================= */
    /* 1. GLOBAL VARIABLES                                          */
    /* ============================================================= */
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
        modal: $('email-modal'),
        modalClose: $('modal-close'),
        cancel: $('cancel-btn'),
        send: $('send-btn'),
        emailInp: $('email-input'),
        modalTitle: $('modal-info-title'),
        modalDesc: $('modal-info-description'),
        keyboard: $('virtual-keyboard'),
        animatedTitle: $('animated-title')
    };

    let scale = 1, minScale = 1, maxScale = 3;
    let tx = 0, ty = 0;
    let isPanning = false, startX = 0, startY = 0;
    const ZOOM_STEP = 0.25;
    let imgW = 0, imgH = 0;
    let currentMap = null;
    let capsLock = false;

    const KB_LAYOUT = [
        ['1','2','3','4','5','6','7','8','9','0','-','_','←'],
        ['q','w','e','r','t','y','u','i','o','p','@'],
        ['a','s','d','f','g','h','j','k','l','.'],
        ['CAPS','z','x','c','v','b','n','m','CAPS'],
    ];

    /* ============================================================= */
    /* 2. HARFMA-HARF ANIMATSIYA                                   */
    /* ============================================================= */
    function startLetterAnimation() {
        const text = "{{ __('messages.Uzbekistan Travel') }}";
        const titleEl = els.animatedTitle;
        titleEl.textContent = '';
        titleEl.style.opacity = '1';

        let i = 0;
        const interval = setInterval(() => {
            if (i < text.length) {
                titleEl.textContent += text[i];
                i++;
            } else {
                clearInterval(interval);
            }
        }, 120); // Harf orasi 120ms — sekin, jiddiy
    }

    /* ============================================================= */
    /* 3. KEYBOARD                                                 */
    /* ============================================================= */
    function renderKB() {
        els.keyboard.innerHTML = '';
        KB_LAYOUT.forEach(row => {
            const r = document.createElement('div');
            r.className = 'keyboard-row';
            row.forEach(k => {
                const b = document.createElement('button');
                b.className = 'keyboard-key';
                if (['←','CAPS','Enter',' '].includes(k)) b.classList.add('special');

                // Harflarni faqat CAPS bosilganda katta qilish
                if (k.length === 1 && /[a-z]/.test(k)) {
                    b.textContent = capsLock ? k.toUpperCase() : k;
                } else {
                    b.textContent = k === ' ' ? '' : k;
                }

                b.dataset.key = k;
                if (k === 'CAPS') {
                    b.classList.toggle('caps-active', capsLock);
                    b.textContent = 'CAPS';
                }
                if (k === ' ') b.innerHTML = '&nbsp;';
                b.onclick = () => kbPress(k);
                r.appendChild(b);
            });
            els.keyboard.appendChild(r);
        });
    }

    function kbPress(k) {
        if (k === 'CAPS') {
            capsLock = !capsLock;
            renderKB();
            return;
        }
        if (k === '←') {
            els.emailInp.value = els.emailInp.value.slice(0, -1);
        } else if (k === 'Enter') {
            sendEmail();
        } else if (k !== ' ') {
            // Harflarni faqat CAPS bosilganda katta kiritish
            if (k.length === 1 && /[a-z]/.test(k)) {
                els.emailInp.value += capsLock ? k.toUpperCase() : k;
            } else {
                els.emailInp.value += k;
            }
        }
        els.emailInp.focus();
    }

    function showKB() {
        els.keyboard.style.display = 'block';
        renderKB();
        els.modal.style.transform = 'translateY(-110px)';
        els.modal.style.transition = 'transform 0.3s ease';
    }

    function hideKB() {
        els.keyboard.style.display = 'none';
        els.modal.style.transform = 'translateY(0)';
    }

    /* ============================================================= */
    /* 4. CLAMP — TO'LIQ TO'G'RI                                   */
    /* ============================================================= */
    function clamp() {
        const wrapperW = els.wrapper.clientWidth;
        const wrapperH = els.wrapper.clientHeight;
        const scaledW = imgW * scale;
        const scaledH = imgH * scale;

        if (scaledW > wrapperW) {
            const maxTx = (scaledW - wrapperW) / (2 * scale);
            const minTx = -maxTx;
            tx = Math.max(minTx, Math.min(maxTx, tx));
        } else {
            tx = 0;
        }

        if (scaledH > wrapperH) {
            const maxTy = (scaledH - wrapperH) / (2 * scale);
            const minTy = -maxTy;
            ty = Math.max(minTy, Math.min(maxTy, ty));
        } else {
            ty = 0;
        }
    }

    /* ============================================================= */
    /* 5. MARKER ADJUST                                            */
    /* ============================================================= */
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

    /* ============================================================= */
    /* 6. TRANSFORM                                                */
    /* ============================================================= */
    function applyTransform() {
        els.mapInner.style.transition = isPanning ? 'none' : 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        els.mapInner.style.transform = `scale(${scale}) translate(${tx}px, ${ty}px)`;
    }

    /* ============================================================= */
    /* 7. MAP INITIALISATION                                        */
    /* ============================================================= */
    function initMap() {
        const rect = els.wrapper.getBoundingClientRect();
        imgW = els.mapImg.naturalWidth;
        imgH = els.mapImg.naturalHeight;

        if (!imgW || !imgH) return showImageError();

        const fitW = rect.width / imgW;
        const fitH = rect.height / imgH;
        scale = Math.min(fitW, fitH) * 2;
        minScale = scale;

        tx = 0; ty = 0;
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

    /* ============================================================= */
    /* 8. ZOOM & RESET                                             */
    /* ============================================================= */
    function zoomIn() {
        scale = Math.min(maxScale, scale + ZOOM_STEP);
        clamp(); adjustMarkers(); applyTransform();
    }

    function zoomOut() {
        scale = Math.max(minScale, scale - ZOOM_STEP);
        clamp(); adjustMarkers(); applyTransform();
    }

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

    /* ============================================================= */
    /* 9. MARKER → INFO PANEL                                      */
    /* ============================================================= */
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

    /* ============================================================= */
    /* 10. EMAIL MODAL                                             */
    /* ============================================================= */
    function openModal() {
        if (!currentMap) return alert('{{ __('messages.Select a location first!') }}');
        els.modalTitle.textContent = currentMap.name;
        els.modalDesc.textContent = currentMap.description;
        els.emailInp.value = '';
        els.modal.classList.add('active');
        setTimeout(() => els.emailInp.focus(), 150);
    }

    function closeModal() {
        els.modal.classList.remove('active');
        hideKB();
        els.emailInp.value = '';
    }

    async function sendEmail() {
        const mail = els.emailInp.value.trim();
        if (!mail || !/^\S+@\S+\.\S+$/.test(mail)) {
            alert('{{ __('messages.Valid email required!') }}');
            return;
        }

        els.send.disabled = true;
        els.send.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.Sending…') }}';

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
            alert(j.success ? (j.message || '{{ __('messages.Send!') }}') : '{{ __('messages.Error') }}: ' + (j.error || 'unknown'));
            if (j.success) closeModal();
        } catch {
            alert('{{ __('messages.Network error!') }}');
        } finally {
            els.send.disabled = false;
            els.send.innerHTML = '<i class="fas fa-paper-plane"></i> {{ __('messages.Send') }}';
        }
    }

    /* ============================================================= */
    /* 11. IMAGE LOAD — 100% ISHONCHLI                             */
    /* ============================================================= */
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

    function startAppWhenReady() {
        const img = els.mapImg;

        // Harf animatsiyasini boshlash
        startLetterAnimation();

        // Rasim yuklanishini kutish
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

        img.onload = () => {
            clearInterval(check);
            setTimeout(hideLoadingAndInit, 800);
        };

        img.onerror = () => {
            clearInterval(check);
            showImageError();
        };
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

    /* ============================================================= */
    /* 12. EVENT LISTENERS                                          */
    /* ============================================================= */
    function bindEvents() {
        els.zoomIn.onclick = zoomIn;
        els.zoomOut.onclick = zoomOut;
        els.reset.onclick = resetView;
        els.closeInfo.onclick = resetView;
        els.sendBtn.onclick = openModal;
        els.modalClose.onclick = closeModal;
        els.cancel.onclick = closeModal;
        els.send.onclick = sendEmail;
        els.emailInp.onfocus = () => {
            showKB();
            setTimeout(() => els.emailInp.scrollIntoView({ behavior: 'smooth', block: 'center' }), 300);
        };
        els.emailInp.onkeydown = e => e.key === 'Enter' && sendEmail();
        els.modal.onclick = e => e.target === els.modal && closeModal();

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (els.modal.classList.contains('active')) closeModal();
                else if (els.infoPanel.classList.contains('active')) resetView();
            }
        });

        // Wheel
        els.wrapper.addEventListener('wheel', e => {
            e.preventDefault();
            scale = Math.max(minScale, Math.min(maxScale, scale + (e.deltaY < 0 ? 1 : -1) * ZOOM_STEP));
            clamp(); adjustMarkers(); applyTransform();
        }, { passive: false });

        // Mouse Pan
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
            up: () => {
                isPanning = false;
                els.wrapper.style.cursor = 'grab';
            }
        };
        ['mousedown', 'mousemove', 'mouseup', 'mouseleave'].forEach((ev, i) => 
            els.wrapper.addEventListener(ev, mouse[['down','move','up','up'][i]])
        );

        // Touch
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

        // Click outside
        els.wrapper.onclick = e => {
            if (e.target.closest('.marker,.marker-info,.controls')) return;
            els.infoPanel.classList.remove('active');
            closeModal();
            document.querySelectorAll('.marker').forEach(m => m.classList.remove('active'));
        };

        window.addEventListener('resize', () => {
            clamp(); adjustMarkers(); applyTransform();
        });
    }

    /* ============================================================= */
    /* 13. INIT                                                    */
    /* ============================================================= */
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