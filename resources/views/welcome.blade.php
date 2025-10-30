<!doctype html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('messages.Welcome to Uzbekistan') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo2.png') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <style>
        /* === ORQA FON STILI QO'SHILDI === */
        body {
            background-image: url("{{ asset('fon/fon.png') }}");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
        /* ================================ */

        .left-logo img {
            max-width: 130px;
            height: auto;
            display: block;
        }
        #ad-popup {
            position: fixed;
            top: 110px; /* Biroz pastga tushirdik */
            right: -200px;
            width: 170px;
            height: auto;
            z-index: 9999;
            opacity: 0;
            transition: right 0.6s ease, opacity 0.6s ease;
        }

        #ad-popup.show {
            right: 20px;
            opacity: 1;
            animation: ad-bounce 2s infinite ease-in-out;
        }

        #ad-popup img {
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
        }

        /* Animatsiya: tepa-past + kattalashish */
        @keyframes ad-bounce {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-8px) scale(1.07);
            }
        }

        .header-bar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10000;
        }
        main {
            margin-top: 90px; /* Header balandligiga moslab */
        }


    </style>
</head>
<body>
<header class="header-bar">
    <div class="header-left">
        <div class="header-left">
            <a href="{{route('welcome')}}">
                <div class="left-logo">
                    <img src="{{asset('/logo/logo.png')}}" alt="Logo">
                </div>
            </a>
        </div>
    </div>

    <div class="header-right">
        <nav class="nav-links">
            <a href="{{ route('welcome') }}" class="nav-link active"><i class="fas fa-home"></i>{{ __('messages.Home') }}</a>
            <a href="{{ route('map-road') }}" class="nav-link"><i class="fas fa-map-location-dot"></i>{{ __('messages.Interactive Map') }}</a>
            <a href="{{ route('libraries.index') }}" class="nav-link"><i class="fas fa-book-open"></i>{{ __('messages.Materials Library') }}</a>
            <a href="{{ route('events.index') }}" class="nav-link"><i class="fas fa-calendar-alt"></i>{{ __('messages.Events & Festivals') }}</a>
        </nav>

        @php
            $currentLocale = session('locale', 'en'); // Default — en
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
    <section class="journey-section">
        <div class="section-header">
            <h2>{{ __('messages.Plan Your Journey') }}</h2>
            <p>{{ __('messages.All the information a traveler needs is available here') }}</p>
        </div>
        <div class="cards-container" >
            <div class="card">
                <a href="{{ route('map-road') }}" style="text-decoration: none; color: inherit;">
                    <div class="card-image-container">
                        <img src="{{asset('images/1.png')}}"
                              alt="Uzbekistan map" class="card-image" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>{{ __('messages.Interactive Map') }}</h3>
                        <p>{{ __('messages.Discover the famous destinations in Uzbekistan for yourself') }}</p>
                        <span class="card-link">{{ __('messages.Get Started') }}→</span>
                    </div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('libraries.index') }}" style="text-decoration: none; color: inherit;">
                    <div class="card-image-container">
                        <img src="{{asset('images/2.png')}}"
                              alt="Uzbekistan map" class="card-image" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>{{ __('messages.Materials Library') }}</h3>
                        <p>{{ __('messages.Access useful materials for your Uzbekistan journey') }}</p>
                        <span class="card-link">{{ __('messages.Get Started') }}→</span>
                    </div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('events.index') }}" style="text-decoration: none; color: inherit;">
                    <div class="card-image-container">
                        <img src="{{asset('images/3.png')}}"
                              alt="Uzbekistan map" class="card-image" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>{{ __('messages.Events & Festivals') }}</h3>
                        <p>{{ __('messages.Explore festivals, exhibitions, and celebrations across Uzbekistan') }}</p>
                        <span class="card-link">{{ __('messages.Get Started') }}→</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
</main>
<div id="ad-popup">
    <img src="{{ asset('advertisement/titf_uz.png') }}" alt="Advertisement">
</div>

<script>
    let inactivityTime = 0;

    // Harakat kuzatuvchi eventlar
    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('click', resetTimer);
    document.addEventListener('scroll', resetTimer);
    document.addEventListener('keydown', resetTimer);

    function resetTimer() {
        inactivityTime = 0;
    }

    setInterval(() => {
        inactivityTime++;
        // 60 sekund bo‘lsa reklamani chiqaramiz
        if (inactivityTime === 15) {
            document.getElementById('ad-popup').classList.add('show');
        }
    }, 1000);

    document.getElementById('ad-popup').addEventListener('click', function () {
        window.location.href = "{{ route('events.index') }}";
    });

</script>

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
</script>
</body>
</html>