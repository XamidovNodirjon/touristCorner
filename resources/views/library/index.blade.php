<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Materials Library - Welcome to Uzbekistan</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">

    <style>
        /* Category buttons */
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
            <a href="{{ route('welcome') }}" class="nav-link "><i class="fas fa-home"></i>{{ __('messages.Home') }}</a>
            <a href="{{ route('map-road') }}" class="nav-link"><i class="fas fa-map-location-dot"></i>{{ __('messages.Interactive Map') }}</a>
            <a href="{{ route('libraries.index') }}" class="nav-link active"><i class="fas fa-book-open"></i>{{ __('messages.Materials Library') }}</a>
            <a href="{{ route('events.index') }}" class="nav-link"><i class="fas fa-calendar-alt"></i>{{ __('messages.Events & Activities') }}</a>
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
    <div class="page-header">
        <h2>{{ __('messages.Materials Library') }}</h2>
        <p>{{ __('messages.View a collection of guides, books and resources about Uzbekistan') }}</p>
    </div>

    <!-- CATEGORY BUTTONS -->
    <div class="category-bar mb-3">
        <button class="category-button {{ request()->is('libraries') ? 'active' : '' }}"
                onclick="window.location='{{ route('libraries.index') }}'">
            Barchasi
        </button>
        @foreach($categories as $category)
            <button class="category-button {{ request()->is('libraries/category/'.$category->id) ? 'active' : '' }}"
                    onclick="window.location='{{ route('libraries.filter', $category->id) }}'">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    @if($materials->isEmpty())
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3>{{ __('messages.No material found') }}</h3>
            <p>{{ __('messages.There are no materials in the library yet.') }}</p>
        </div>
    @else
        <div class="library-grid">
            @foreach($materials as $material)
                @php
                    $filePath = storage_path('app/public/' . $material->file_path_ru);
                    $fileSize = file_exists($filePath)
                        ? round(filesize($filePath) / 1048576, 1) . ' MB'
                        : 'N/A';
                @endphp

                <div class="library-card">
                    <img src="{{ asset('storage/' . $material->image) }}"
                         alt="{{ $material->title_uz }}"
                         class="card-image"
                         onerror="this.src='https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80'">

                    <div class="card-body">
                        <span class="card-badge badge-pdf">PDF Guide</span>
                        <span class="card-size">{{ $fileSize }}</span>

                        <h3 class="card-title">{{ $material->title_uz }}</h3>
                        <p class="card-description">{{ $material->description_uz }}</p>

                        <div class="card-meta">
                            <span class="card-pages">PDF Document</span>
                            <span class="card-price">Bepul yuklab olish</span>
                        </div>

                        <div class="card-actions">
                            <button class="btn btn-primary"
                                    onclick="openEmailModal({{ $material->id }}, '{{ addslashes($material->title_uz) }}', '{{ addslashes($material->description_uz) }}')">
                                <i class="fas fa-paper-plane"></i> Send to Email
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
<div class="modal" id="emailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Emailga material yuborish</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p><strong>Material:</strong> <span id="modalMaterialName"></span></p>
            <p><strong>Ma'lumotnoma:</strong> <span id="modalMaterialDescription"></span></p>
            <br>

            <div class="form-group">
                <label for="emailInput">Email manzil</label class="form-label" style="margin-bottom: 5px;">
                <input type="email" id="emailInput" class="form-input" placeholder="sizning.email@example.com" required autofocus>
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
<script>
    // === Language Dropdown ===
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

    // === Modal funksiyalari ===
    let selectedMaterialId = null;
    function openEmailModal(id, title, description_uz) {
        selectedMaterialId = id;
        document.getElementById('modalMaterialName').textContent = title;
        // document.getElementById('modalMaterialSize').textContent = size;
        document.getElementById('modalMaterialDescription').textContent = description_uz;
        document.getElementById('emailModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('emailModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        document.getElementById('emailInput').value = '';
    }
    async function sendEmail() {
        const email = document.getElementById('emailInput').value;
        if (!email) return alert('Iltimos, email kiriting');
        const res = await fetch("{{ route('send.material') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email, material_id: selectedMaterialId })
        });
        if (res.ok) {
            closeModal();
            openSuccessModal();
        } else alert('Xatolik yuz berdi');
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
