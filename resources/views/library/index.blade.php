<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Materials Library - Welcome to Uzbekistan</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo2.png') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">

    <style>
        body {
            background-image: url("{{ asset('fon/fon.png') }}");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            padding-top: 80px;
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        .header-bar {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #1C3F3A;
        }

        .scroll-to-top-btn {
            display: none;
            position: fixed;
            bottom: 30px; right: 30px;
            z-index: 999;
            border: none;
            background-color: #1C3F3A;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 50%;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            opacity: 0.8;
        }

        .scroll-to-top-btn:hover {
            background-color: #2b5c54;
            opacity: 1;
        }

        .loading-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
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

        .loading-modal.show { display: flex; }

        .category-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 25px 0;
            justify-content: center;
        }

        .category-button {
            border: 1px solid #ddd;
            background: #fff;
            padding: 10px 18px;
            border-radius: 25px;
            font-weight: 500;
            color: #333;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .category-button:hover,
        .category-button.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .left-logo img {
            max-width: 130px;
            height: auto;
        }

        /* === MODAL – KLAVYATURA CHIQGANDA YUQORIGA SILJISH === */
        .modal {
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

        .modal.show {
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

        /* KLAVYATURA CHIQGANDA – MODAL YUQORIGA SILJIDI */
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

        .modal-header p, .modal-header h3 {
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
            margin-bottom: 20px;
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
    </style>
</head>

<body>
<header class="header-bar">
    <div class="header-left">
        <a href="{{route('welcome')}}">
            <div class="left-logo">
                <img src="{{asset('/logo/logo.png')}}" alt="Logo">
            </div>
        </a>
    </div>

    <div class="header-right">
        <nav class="nav-links">
            <a href="{{ route('welcome') }}" class="nav-link"><i class="fas fa-home"></i>{{ __('messages.Home') }}</a>
            <a href="{{ route('map-road') }}" class="nav-link"><i class="fas fa-map-location-dot"></i>{{ __('messages.Interactive Map') }}</a>
            <a href="{{ route('libraries.index') }}" class="nav-link active"><i class="fas fa-book-open"></i>{{ __('messages.Materials Library') }}</a>
            <a href="{{ route('events.index') }}" class="nav-link"><i class="fas fa-calendar-alt"></i>{{ __('messages.Events & Festivals') }}</a>
        </nav>

        @php
            $currentLocale = session('locale', 'en');
            $flags = ['uz' => 'https://flagcdn.com/w40/uz.png', 'en' => 'https://flagcdn.com/w40/gb.png', 'ru' => 'https://flagcdn.com/w40/ru.png'];
            $langLabels = ['uz' => 'UZ', 'en' => 'EN', 'ru' => 'RU'];
        @endphp

        <div class="language-switcher">
            <div class="lang-select" id="lang-select-btn">
                <img src="{{ $flags[$currentLocale] }}" alt="Flag" class="flag" id="selected-flag">
                <span id="selected-lang-text">{{ $langLabels[$currentLocale] }}</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <ul class="lang-dropdown" id="lang-dropdown">
                <li data-lang="uz" data-flag="{{ $flags['uz'] }}"><img src="{{ $flags['uz'] }}" alt="Uzbek Flag" class="flag"> O'zbekcha</li>
                <li data-lang="en" data-flag="{{ $flags['en'] }}"><img src="{{ $flags['en'] }}" alt="UK Flag" class="flag"> English</li>
                <li data-lang="ru" data-flag="{{ $flags['ru'] }}"><img src="{{ $flags['ru'] }}" alt="Russia Flag" class="flag"> Русский</li>
            </ul>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <div class="category-bar mb-3">
            <button class="category-button {{ request()->is('libraries') ? 'active' : '' }}"
                    onclick="window.location='{{ route('libraries.index') }}'">
                All
            </button>
            @foreach($categories as $category)
                <button class="category-button {{ request()->is('libraries/category/'.$category->id) ? 'active' : '' }}"
                        onclick="window.location='{{ route('libraries.filter', $category->id) }}'">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        @if($materials->isEmpty())
            <div class="empty-state text-center py-5">
                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No material found</h3>
                <p class="text-muted">There are no materials in the library yet.</p>
            </div>
        @else
            @php $locale = app()->getLocale(); @endphp

            <div class="library-grid">
                @foreach($materials as $material)
                    @php
                        $titleField = 'title_' . $locale;
                        $descField = 'description_' . $locale;
                        $filePathField = 'file_path_' . $locale;
                        if (empty($material->$titleField)) continue;
                        $filePath = storage_path('app/public/' . $material->$filePathField);
                        $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1048576, 1) . ' MB' : '';
                    @endphp

                    <div class="library-card">
                        <img src="{{ asset('storage/' . $material->image) }}"
                             alt="{{ $material->$titleField }}"
                             class="card-image"
                             onerror="this.src='https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80'">

                        <div class="card-body">
                            <h3 class="card-title">{{ $material->$titleField }}</h3>
                            <p class="card-description">{{ Str::limit($material->$descField, 80) }}</p>

                            <div class="card-meta">
                                <span class="card-pages">PDF Document</span>
                                <span class="card-size">{{ $fileSize }}</span>
                            </div>

                            <div class="card-actions">
                                <button class="btn btn-primary"
                                        onclick="openEmailModal({{ $material->id }}, '{{ addslashes($material->$titleField) }}', '{{ addslashes($material->$descField) }}')">
                                    <i class="fas fa-envelope"></i>{{ __('messages.Send to email') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>

<button onclick="scrollToTop()" id="scrollToTopBtn" title="Yuqoriga" class="scroll-to-top-btn">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Email Modal -->
<div class="modal" id="emailModal">
    <div class="modal-content modern-modal">
        <div class="modal-header">
            <p><strong>{{ __('messages.Send the material to your email') }}</strong></p>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body">
            <div class="material-info">
                <h3 id="modalMaterialName"></h3>
                <p id="modalMaterialDescription" class="material-desc"></p>
            </div>

            <div class="form-group">
                <label for="emailInput">{{ __('messages.Your email address') }}</label>
                <input type="email" id="emailInput" class="form-input" placeholder="excample@gmail.com" required>
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
<div class="modal" id="successModal">
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
    // === Virtual Keyboard ===
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
        document.body.classList.add('keyboard-active'); // MODALNI SILJISH UCHUN
        renderKeyboard();
        keyboardShown = true;
    }

    function hideKeyboard() {
        if (!keyboardShown) return;
        const keyboard = document.getElementById('virtual-keyboard');
        keyboard.classList.remove('show');
        document.body.classList.remove('keyboard-active'); // SILJISH O‘CHADI
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

    // === Inactivity Timer ===
    let inactivityTime = 0;
    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('click', resetTimer);
    document.addEventListener('scroll', resetTimer);
    document.addEventListener('keydown', resetTimer);

    function resetTimer() {
        inactivityTime = 0;
    }

    setInterval(() => {
        inactivityTime++;
        if (inactivityTime === 60) {
            window.location.href = "{{ route('welcome') }}";
        }
    }, 1000);

    // === Scroll to Top ===
    const scrollBtn = document.getElementById('scrollToTopBtn');
    window.onscroll = () => {
        scrollBtn.style.display = (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) ? 'block' : 'none';
    };

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // === Language Switcher ===
    document.addEventListener('DOMContentLoaded', () => {
        const langBtn = document.getElementById('lang-select-btn');
        const dropdown = document.getElementById('lang-dropdown');
        const flag = document.getElementById('selected-flag');
        const text = document.getElementById('selected-lang-text');

        langBtn.addEventListener('click', () => dropdown.classList.toggle('show'));
        dropdown.addEventListener('click', (e) => {
            const li = e.target.closest('li');
            if (!li) return;
            flag.src = li.dataset.flag;
            text.textContent = li.dataset.lang.toUpperCase();
            dropdown.classList.remove('show');
            window.location.href = `/lang/${li.dataset.lang}`;
        });

        document.addEventListener('click', (e) => {
            if (!langBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });

    // === Modal & Email ===
    let selectedMaterialId = null;

    function openEmailModal(id, title, description) {
        selectedMaterialId = id;
        document.getElementById('modalMaterialName').textContent = title;
        document.getElementById('modalMaterialDescription').textContent = description;
        document.getElementById('emailModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.getElementById('emailInput').value = '';
        hideKeyboard();
    }

    function closeModal() {
        document.getElementById('emailModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        document.getElementById('emailInput').value = '';
        hideKeyboard();
    }

    function openSuccessModal() {
        document.getElementById('successModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSuccessModal() {
        document.getElementById('successModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function showLoadingAnimation() {
        document.getElementById('loadingModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function hideLoadingAnimation() {
        document.getElementById('loadingModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    async function sendEmail() {
        const email = document.getElementById('emailInput').value.trim();
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
            alert('Iltimos, to\'g\'ri email kiriting');
            return;
        }

        closeModal();
        showLoadingAnimation();

        const lang = document.getElementById('selected-lang-text').textContent.toLowerCase();

        try {
            const res = await fetch("{{ route('send.material') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email, material_id: selectedMaterialId, lang })
            });

            hideLoadingAnimation();

            if (res.ok) {
                openSuccessModal();
                setTimeout(closeSuccessModal, 3000);
            } else {
                const err = await res.json();
                alert('Xatolik: ' + (err.error || 'Server xatosi'));
            }
        } catch (error) {
            hideLoadingAnimation();
            alert('Internet aloqasi yo\'q');
        }
    }

    // Input fokusda – klaviatura chiqadi
    document.getElementById('emailInput').addEventListener('focus', showKeyboard);
</script>
</body>
</html>