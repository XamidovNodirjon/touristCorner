<!doctype html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Uzbekistan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

</head>
<body>
<header class="header-bar">
    <div class="header-left">
        <div class="logo-icon">
            <i class="fas fa-globe-asia"></i>
        </div>
        <div class="logo-text">
            <h1>Welcome to Uzbekistan</h1>
            <p>Your Gateway to the Heart of Central Asia</p>
        </div>
    </div>

    <div class="header-right">
        <nav class="nav-links">
            <a href="{{ route('welcome') }}" class="nav-link active"><i class="fas fa-home"></i> Home</a>
            <a href="{{ route('map-road') }}" class="nav-link"><i class="fas fa-map-location-dot"></i> Interactive Map</a>
            <a href="{{ route('libraries.index') }}" class="nav-link"><i class="fas fa-book-open"></i> Materials Library</a>
            <a href="{{ route('events.index') }}" class="nav-link"><i class="fas fa-calendar-alt"></i> Events & Activities</a>
        </nav>

        <div class="language-switcher">
            <div class="lang-select" id="lang-select-btn" aria-label="Language selector">
                <img src="https://flagcdn.com/w40/uz.png" alt="Uzbekistan Flag" class="flag" id="selected-flag">
                <span id="selected-lang-text">UZ</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <ul class="lang-dropdown" id="lang-dropdown">
                <li data-lang="uz" data-flag="https://flagcdn.com/w40/uz.png">
                    <img src="https://flagcdn.com/w40/uz.png" alt="Uzbekistan Flag" class="flag"> O'zbekcha
                </li>
                <li data-lang="en" data-flag="https://flagcdn.com/w40/gb.png">
                    <img src="https://flagcdn.com/w40/gb.png" alt="UK Flag" class="flag"> English
                </li>
                <li data-lang="ru" data-flag="https://flagcdn.com/w40/ru.png">
                    <img src="https://flagcdn.com/w40/ru.png" alt="Russia Flag" class="flag"> Русский
                </li>
            </ul>
        </div>
    </div>
</header>

<main>
    <section class="journey-section">
        <div class="section-header">
            <h2>Plan Your Journey</h2>
            <p>Everything you need to explore Uzbekistan is at your fingertips</p>
        </div>
        <div class="cards-container">
            <!-- First Card - Interactive Map -->
            <div class="card">
                <a href="{{ route('map-road') }}" style="text-decoration: none; color: inherit;">
                    <div class="card-image-container">
                        <img src="{{asset('images/map1.png')}}"
                             alt="Uzbekistan map" class="card-image" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>Explore Interactive Map</h3>
                        <p>Discover must-visit destination across Uzbekistan</p>
                        <span class="card-link">Get Started →</span>
                    </div>
                </a>
            </div>

            <!-- Second Card - Browse Materials -->
            <div class="card">
                <a href="{{ route('libraries.index') }}" style="text-decoration: none; color: inherit;">
                    <div class="card-image-container">
                        <img src="{{asset('images/library1.png')}}"
                             alt="Uzbekistan map" class="card-image" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>Online Library</h3>
                        <p>Access useful materials for your Uzbekistan journey</p>
                        <span class="card-link">Get Started →</span>
                    </div>
                </a>
            </div>

            <!-- Third Card - Current Events -->
            <div class="card">
                <a href="{{ route('events.index') }}" style="text-decoration: none; color: inherit;">
                    <div class="card-image-container">
                        <img src="{{asset('images/event1.png')}}"
                             alt="Uzbekistan map" class="card-image" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>Events & Festivals</h3>
                        <p>Explore festivals, exhibitions, and celebrations across Uzbekistan</p>
                        <span class="card-link">Get Started →</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const langSelectBtn = document.getElementById('lang-select-btn');
        const langDropdown = document.getElementById('lang-dropdown');
        const selectedFlag = document.getElementById('selected-flag');
        const selectedLangText = document.getElementById('selected-lang-text');

        langSelectBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            langDropdown.classList.toggle('show');
        });

        langDropdown.addEventListener('click', function (event) {
            const target = event.target.closest('li');
            if (!target) return;

            const lang = target.getAttribute('data-lang');
            const flagSrc = target.getAttribute('data-flag');

            selectedFlag.src = flagSrc;
            selectedLangText.textContent = lang.toUpperCase();
            langDropdown.classList.remove('show');

            console.log(`Selected language: ${lang}`);
        });

        document.addEventListener('click', function () {
            if (langDropdown.classList.contains('show')) {
                langDropdown.classList.remove('show');
            }
        });
    });
</script>
</body>
</html>