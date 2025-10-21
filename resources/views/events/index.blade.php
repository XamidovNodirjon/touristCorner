<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.Events & Activities - Welcome to Uzbekistan') }}</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    
    <style>
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
        .card {
            display: flex;
            flex-direction: column;
            height: 100%;
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
    </style>
</head>

<body>
<header class="header-bar">
    <a href="{{route('welcome')}}" style="text-decoration: dashed;">
        <div class="header-left">
            <div class="logo-text">
                <h1>{{ __('messages.Welcome to Uzbekistan') }}</h1>
                <p>{{ __('messages.Your Gateway to the Heart of Central Asia') }}</p>
            </div>
        </div>
    </a>

    <div class="header-right">
        <nav class="nav-links">
            <a href="{{ route('welcome') }}" class="nav-link"><i class="fas fa-home"></i>{{ __('messages.Home') }}</a>
            <a href="{{ route('map-road') }}" class="nav-link"><i class="fas fa-map-location-dot"></i>{{ __('messages.Interactive Map') }}</a>
            <a href="{{ route('libraries.index') }}" class="nav-link"><i class="fas fa-book-open"></i>{{ __('messages.Materials Library') }}</a>
            <a href="{{ route('events.index') }}" class="nav-link active"><i class="fas fa-calendar-alt"></i>{{ __('messages.Events & Activities') }}</a>
        </nav>

        @php
            $currentLocale = session('locale', 'uz'); // Default — uz
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
        <div class="page-header">
            <h2>{{ __('messages.Find top events and celebrations across Uzbekistan') }}</h2>
            <p>{{ __('messages.Learn about upcoming cultural and tourism events in Uzbekistan') }}</p>
        </div>

        <div class="category-bar mb-3">
        <button class="category-button {{ request()->is('events') ? 'active' : '' }}"
                onclick="window.location='{{ route('events.index') }}'">
            Barchasi
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
                                    <span>{{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="far fa-clock"></i>
                                    <span>{{ \Carbon\Carbon::parse($event->time)->format('H:i') }}</span>
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
                                    '{{ $event->time }}'
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

<!-- Email Modal -->
<div class="modal" id="emailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Emailga material yuborish</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p><strong>Material:</strong> <span id="modalMaterialName"></span></p>
            <p><strong>Ma'lumot:</strong> <span id="modalMaterialDescription"></span></p>

            <div class="form-group">
                <label for="emailInput">Email manzil</label>
                <input type="email" id="emailInput" class="form-input" placeholder="sizning.email@example.com" required>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Bekor qilish</button>
            <button class="btn btn-primary" onclick="sendEmail()"><i class="fas fa-paper-plane"></i> Yuborish</button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal" id="successModal">
    <div class="modal-content success-modal">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle text-success"></i> Muvaffaqiyatli!</h3>
            <button class="modal-close" onclick="closeSuccessModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p>Material email manzilingizga muvaffaqiyatli yuborildi ✅</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>
</div>

<!-- Loading Animation Modal -->
<div class="loading-modal" id="loadingModal">
    <div class="loading-animation">
        <svg viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#007bff" stroke-width="6" stroke-dasharray="20,10"/>
            <circle cx="50" cy="50" r="30" fill="none" stroke="#ffd700" stroke-width="4" stroke-dasharray="15,10"/>
            <circle cx="50" cy="50" r="20" fill="none" stroke="#ff4500" stroke-width="3" stroke-dasharray="10,10"/>
        </svg>
        <span class="loading-text">Yuborilmoqda...</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const langSelectBtn = document.getElementById('lang-select-btn');
        const langDropdown = document.getElementById('lang-dropdown');
        const selectedFlag = document.getElementById('selected-flag');
        const selectedLangText = document.getElementById('selected-lang-text');

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
        
    });
    document.addEventListener('click', () => langDropdown.classList.remove('show'));
    
    let selectedEvent = {};

    function openEmailModal(id, title_uz, date, location, description_uz, image, time) {
        selectedEvent = { id, title_uz, date, location, description_uz, image, time };

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