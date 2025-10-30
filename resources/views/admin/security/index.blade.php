@extends('layouts.app')
@section('content')
    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #1e3a6e;
            --accent-color: #4a6fa5;
            --light-bg: #f5f7fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .welcome-card {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6e 100%);
            color: white;
            border-radius: 12px;
            border: none;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .welcome-card .card-body {
            padding: 1.5rem 2rem;
        }

        .user-info-card {
            border-radius: 12px;
            border: 1px solid #e1e5eb;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
            background: white;
        }

        .user-info-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e1e5eb;
            padding: 1.5rem 2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d9e6;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.15);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
            font-size: 0.95rem;
        }

        .btn-update {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-update:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(44, 90, 160, 0.25);
        }

        .user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            font-weight: bold;
            margin-right: 1.5rem;
        }

        .info-icon {
            color: var(--primary-color);
            font-size: 1.1rem;
            margin-right: 0.5rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e1e5eb;
        }

        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e1e5eb;
        }

        .section-title i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #d1d9e6;
            color: var(--primary-color);
        }

        .password-toggle {
            cursor: pointer;
            background: #f8fafc;
            border: 1px solid #d1d9e6;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .password-toggle:hover {
            background: #e9ecef;
        }

        @media (max-width: 768px) {
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
                margin-right: 1rem;
            }

            .welcome-card .card-body {
                padding: 1.25rem 1.5rem;
            }

            .card-header {
                padding: 1.25rem 1.5rem;
            }
        }
    </style>

    <div class="container mt-4 mb-5">
        <!-- Xush kelibsiz kartasi -->


        <!-- Muvaffaqiyat xabari -->
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
    @endif

    <!-- Foydalanuvchi ma'lumotlari formasi -->
        <div class="card user-info-card">
            <div class="card-body p-4">
                <form action="{{ route('admin.security.update', auth()->user()->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-section">
                        <h6 class="section-title"><i class="bi bi-person-circle"></i>Asosiy ma'lumotlar</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ism</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foydalanuvchi nomi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                                    <input type="text" name="username" class="form-control" value="{{ auth()->user()->username }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title"><i class="bi bi-envelope"></i>Aloqa ma'lumotlari</h6>
                        <div class="mb-3">
                            <label class="form-label">Email manzili</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title"><i class="bi bi-shield-lock"></i>Xavfsizlik sozlamalari</h6>
                        <div class="mb-3">
                            <label class="form-label">Yangi parol</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Yangi parol kiriting">
                                <span class="input-group-text password-toggle" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                            <div class="form-text">Parol kamida 8 ta belgidan iborat bo'lishi kerak</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Parolni tasdiqlash</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Parolni qayta kiriting">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-update text-white">
                            <i class="bi bi-check-lg me-2"></i>Ma'lumotlarni yangilash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });
    </script>
@endsection