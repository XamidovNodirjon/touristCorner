<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.Events & Activities - Welcome to Uzbekistan') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    <style>
        
        body {
            background-image: url("{{ asset('fon/fon.png') }}");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed; /* Sahifani aylantirganda (scroll) rasm joyida turishi uchun */
            padding-top: 80px; /* Headerning qotirilishini hisobga olib, bodyga padding berish */
        }
        /* Headerni qadash uchun stil */
        .header-bar {
            background-color: #1C3F3A;
            border-bottom: 1px solid #EBE9DA;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        /* Scroll-to-Top Button Stili (PASTKI O'NG UCHUN TASDIQLANDI) */
        .scroll-to-top-btn {
            display: none;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
            border: none;
            outline: none;
            background-color: #1C3F3A;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 50%;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s, opacity 0.3s;
            opacity: 0.8;
        }

        .scroll-to-top-btn:hover {
            background-color: #2b5c54;
            opacity: 1;
        }

        /* Bodyga padding qo'shish (fixed header uchun) */
        body {
            padding-top: 80px;
        }

        /* Category buttons */
        .category-bar {
            display: flex;
            flex-wrap: wrap;
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

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }


        .card {
            display: flex;
            flex-direction: column;
            height: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background: #fff;
            margin-top: 20px;
        }

        .card:not(:first-child) {
            margin-top: 24px;
        }

        .card img.card-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }


        .category-button:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .category-button.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        /* Loading Animation Modal */
        .loading-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .loading-animation {
            width: 80px;
            height: 80px;
            position: relative;
            animation: rotate 2s linear infinite;
        }

        .loading-animation svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 0 5px rgba(0, 123, 255, 0.5));
        }

        .loading-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-modal.show {
            display: flex;
        }

        .left-logo img {
            max-width: 130px;
            height: auto;
            display: block;
        }

        /* YANGI: Virtual Keyboard stili */
        #virtual-keyboard {
            display: none; 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            background: #222; 
            padding: -20px; 
            z-index: 9999; 
            text-align: center; 
            border-top: 3px solid #444; 
            user-select: none;
        }
        /* Email Modal stili */
        .modal-content {
            /* Hozirgi stillar: (background, border-radius, etc.) */
            position: relative !important; 
            /* Agar modal vertikal centrda bo'lsa, quyidagini qo'shamiz: */
            transition: transform 0.3s ease-out; /* Silliq ko'tarilish uchun */
        }

        /* Yangi stil: klaviatura chiqishi uchun modalni surish */
        .modal-content.keyboard-active {
            /* 250px bu klaviaturaning taxminiy balandligi. 
            Buni kamroq yoki ko'proq qilib sinab ko'rishingiz mumkin. */
            transform: translateY(-150px); 
            /* Agar modal mutlaq centrda bo'lmasa, transform: translateY(0) ishlashi ham mumkin,
            ammo bu yechim centrda bo'lgan holat uchun universalroq. */
        }
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
            <a href="{{ route('libraries.index') }}" class="nav-link"><i class="fas fa-book-open"></i>{{ __('messages.Materials Library') }}</a>
            <a href="{{ route('events.index') }}" class="nav-link active"><i class="fas fa-calendar-alt"></i>{{ __('messages.Events & Activities') }}</a>
        </nav>

        @php
            $currentLocale = session('locale', 'en'); // Default — uz
            $flags = [
                'uz' => 'https://flagcdn.com/w40/uz.png',
                'en' => 'https://flagcdn.com/w40/gb.png',
                'ru' => 'https://flagcdn.com/w40/ru.png',
            ];
            $langLabels = [
                'uz' => 'UZ',
                'en' => 'EN',
                'ru' => 'RU',
            ];
        @endphp

        <div class="language-switcher">
            <div class="lang-select" id="lang-select-btn" aria-label="Language selector">
                <img src="{{ $flags[$currentLocale] }}" alt="Flag" class="flag" id="selected-flag">
                <span id="selected-lang-text">{{ $langLabels[$currentLocale] }}</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <ul class="lang-dropdown" id="lang-dropdown">
                <li data-lang="uz" data-flag="{{ $flags['uz'] }}">
                    <img src="{{ $flags['uz'] }}" alt="Uzbek Flag" class="flag"> O'zbekcha
                </li>
                <li data-lang="en" data-flag="{{ $flags['en'] }}">
                    <img src="{{ $flags['en'] }}" alt="UK Flag" class="flag"> English
                </li>
                <li data-lang="ru" data-flag="{{ $flags['ru'] }}">
                    <img src="{{ $flags['ru'] }}" alt="Russia Flag" class="flag"> Русский
                </li>
            </ul>
        </div>

    </div>
</header>

<main>
    <div class="container">
        <div class="category-bar mb-3">
        <button class="category-button {{ request()->is('events') ? 'active' : '' }}"
                onclick="window.location='{{ route('events.index') }}'">
            {{ __('messages.All') }}
        </button>

        @foreach($categories as $category)
            <button class="category-button {{ request()->is('events/category/'.$category->id) ? 'active' : '' }}"
                    onclick="window.location='{{ route('events.filter', $category->id) }}'">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    @if($events->isNotEmpty())
        <div class="row">
            @foreach($events as $event)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="{{ asset('storage/' . $event->image) }}"
                             alt="{{ $event->title_uz }}"
                             class="card-image"
                             onerror="this.src='https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80'">
                        
                        <div class="card-content">
                            <div class="tag-container">
                                <span class="tag {{ $event->category->slug ?? 'default' }}">{{ $event->category->name ?? 'Tadbir' }}</span>
                                <span class="price">
                                    {!! $event->price > 0
                                        ? 'UZS ' . number_format($event->price, 0, '.', ' ') 
                                        : '<i class="fa fa-ticket"></i> Free' !!}
                                </span>
                            </div>
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-description">{{ Str::limit($event->description, 80) }}</p>

                            <div class="card-details">
                                <div class="detail-item">
                                    <i class="far fa-calendar-alt"></i>
                                   <span>
                                        @if($event->start_date && $event->end_date)
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}
                                            –
                                            {{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') }}
                                        @elseif($event->start_date)
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}
                                        @elseif($event->end_date)
                                            {{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') }}
                                        @else
                                            <em>Sana ko‘rsatilmagan</em>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                            </div>
                            
                            <button class="card-button"
                                onclick="openEmailModal(
                                            '{{ $event->id }}',
                                            '{{ $event->title }}',
                                            '{{ $event->date }}',
                                            '{{ $event->location }}',
                                            '{{ $event->description }}',
                                            '{{ $event->image }}',
                                        )">
                                <i class="fas fa-envelope"></i> {{ __('messages.Send Details') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">{{ __('messages.No events found.') }}</p>
    @endif
</main>

<button onclick="scrollToTop()" id="scrollToTopBtn" title="Yuqoriga" class="scroll-to-top-btn">
    <i class="fas fa-chevron-up"></i>
</button>

<div class="modal" id="emailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>{{ __('messages.Email the event name') }}</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p><strong>{{ __('messages.Event Name:') }}</strong> <span id="modalMaterialName"></span></p>
            <br>
            <p><strong>{{ __('messages.Description:') }}</strong> <span id="modalMaterialDescription"></span></p>
            <br>
            <div class="form-group mb-4">
                <label for="emailInput" class="mb-2"><strong>{{ __('messages.Email Address') }}</strong></label>
                <input type="email" id="emailInput" class="form-input" placeholder=".email@example.com" onclick="showKeyboard()" require>
                <div id="virtual-keyboard" style="display:none;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">{{ __('messages.Cancel') }}</button>
            <button class="btn btn-primary" onclick="sendEmail()"><i class="fas fa-paper-plane"></i> {{ __('messages.Send to email') }}</button>
        </div>
    </div>
</div>

<div class="modal" id="successModal">
    <div class="modal-content success-modal">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle text-success"></i> {{ __('messages.Success')}}!</h3>
            <button class="modal-close" onclick="closeSuccessModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p>{{ __('messages.Material sent successfully')}}✅</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>
</div>

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
    // === Virtual Keyboard Logic ===
    let isUpperCase = false; // katta/kichik holat
    const keyboardLayout = [
    ['1','2','3','4','5','6','7','8','9','0','-','_','←'],
    ['q','w','e','r','t','y','u','i','o','p','@'],
    ['a','s','d','f','g','h','j','k','l','.','!','?'],
    ['CAPS','z','x','c','v','b','n','m',':',';','CAPS'],
    // ['Enter','CLOSE']
    ];

    function showKeyboard() {
        const keyboard = document.getElementById('virtual-keyboard');
        keyboard.style.display = 'block';
        keyboard.innerHTML = '';
        renderKeyboard();
    }

    function renderKeyboard() {
        const keyboard = document.getElementById('virtual-keyboard');
        keyboard.innerHTML = '';

        keyboardLayout.forEach(row => {
            const rowDiv = document.createElement('div');
            rowDiv.style.marginBottom = '8px';

            row.forEach(key => {
            const btn = document.createElement('button');
            btn.textContent = isUpperCase && key.length === 1 ? key.toUpperCase() : key;
            btn.style = `
                margin:3px; 
                padding:12px 16px;
                font-size:18px;
                border:none;
                border-radius:6px; 
                background:#444; 
                color:white; 
                cursor:pointer;
                min-width:50px;
            `;

            btn.onclick = () => handleKeyPress(key);
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
            case 'Space':
            input.value += ' ';
            break;
            case 'Enter':
            // Enter tugmasi bu yerda e'tiborga olinmaydi, chunki modalda submit tugmasi bor
            break;
            case 'CAPS':
            isUpperCase = !isUpperCase;
            renderKeyboard();
            break;
            case 'CLOSE':
            document.getElementById('virtual-keyboard').style.display = 'none';
            break;
            default:
            const charToAdd = isUpperCase ? key.toUpperCase() : key;
            input.value += charToAdd;
            break;
        }

        input.focus();
    }
    
    // Virtual Keyboard uchun bodyga touch event qo'shish, agar kerak bo'lsa (Mobil uchun)
    // window.addEventListener('DOMContentLoaded', () => {
    //     const input = document.getElementById('emailInput');
    //     input.focus();
    // });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const langSelectBtn = document.getElementById('lang-select-btn');
        const langDropdown = document.getElementById('lang-dropdown');
        const selectedFlag = document.getElementById('selected-flag');
        const selectedLangText = document.getElementById('selected-lang-text');
        const scrollToTopBtn = document.getElementById('scrollToTopBtn'); 

        langSelectBtn.addEventListener('click', function() {
            langDropdown.classList.toggle('show');
        });

        langDropdown.addEventListener('click', function(event) {
            const target = event.target.closest('li');
            if (!target) return;

            const lang = target.getAttribute('data-lang');
            const flagSrc = target.getAttribute('data-flag');

            // Tanlangan flag va tilni yangilash
            selectedFlag.src = flagSrc;
            selectedLangText.textContent = lang.toUpperCase();
            langDropdown.classList.remove('show');

            // Laravel routiga so‘rov yuborish:
            window.location.href = `/lang/${lang}`;
        });

        // Dropdown tashqarisiga bosilganda yopish
        document.addEventListener('click', function(event) {
            if (!langSelectBtn.contains(event.target) && !langDropdown.contains(event.target)) {
                langDropdown.classList.remove('show');
            }
        });

        // YANGI: Scroll funksiyasini yuklash
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            // 300px dan ko'proq pastga aylantirilganda tugmani ko'rsatish
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                scrollToTopBtn.style.display = "block";
            } else {
                scrollToTopBtn.style.display = "none";
            }
        }
    });

    // YANGI: Sahifani yuqoriga aylantirish funksiyasi
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth' 
        });
    }

    let selectedEvent = {};

    function openEmailModal(id, title_uz, date, location, description_uz, image) {
        selectedEvent = { id, title_uz, date, location, description_uz, image };

        document.getElementById('modalMaterialName').textContent = title_uz;
        document.getElementById('modalMaterialDescription').textContent = description_uz;
        document.getElementById('emailModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('emailModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        document.getElementById('emailInput').value = '';
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
        const email = document.getElementById('emailInput').value;
        if (!email) return alert('Iltimos, email manzilni kiriting');

        closeModal(); // Close email modal immediately
        showLoadingAnimation(); // Show loading animation

        try {
            const response = await fetch("{{ route('send.event') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({
                    email,
                    event: selectedEvent
                })
            });

            hideLoadingAnimation(); // Hide loading animation

            if (response.ok) {
                openSuccessModal();
                // 3 soniyadan keyin avtomatik yopish
                setTimeout(closeSuccessModal, 3000);
            } else {
                alert('Email yuborishda xatolik yuz berdi');
            }
        } catch (error) {
            hideLoadingAnimation(); // Hide loading animation on error
            alert('Email yuborishda xatolik yuz berdi');
        }
    }

    function openSuccessModal() {
        document.getElementById('successModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSuccessModal() {
        document.getElementById('successModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }
</script>
</body>
</html>