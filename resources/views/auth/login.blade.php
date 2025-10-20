<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tizimga kirish | National PR Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #d9dcdfff, #06ecd9e0);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 40px 35px;
            width: 100%;
            max-width: 420px;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo h2 {
            font-weight: 700;
            color: #007bff;
            letter-spacing: 0.5px;
        }

        .login-title {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 10px;
        }

        .login-subtext {
            text-align: center;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #00b4d8);
            border: none;
            border-radius: 10px;
            padding: 12px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0069d9, #0096c7);
            transform: translateY(-2px);
        }

        .alert {
            font-size: 0.9rem;
        }

        .text-link {
            color: #007bff;
            text-decoration: none;
        }

        .text-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">
            <h2>National PR Center</h2>
        </div>
        <h5 class="login-title">Tizimga kirish</h5>
        <p class="login-subtext">Ishchi paneliga kirish uchun ma'lumotlarni kiriting</p>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Foydalanuvchi nomi</label>
                <input type="text" class="form-control" id="username" name="username"
                       value="{{ old('username') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Parol</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-muted" for="remember">Meni eslab qolish</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="small text-link" href="{{ route('password.request') }}">
                        Parolni unutdingizmi?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary w-100">Tizimga kirish</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
