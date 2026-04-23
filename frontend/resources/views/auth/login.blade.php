<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | Sistem Penjadwalan</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Background dengan overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset('assets/images/login2.png') }}') center center/cover no-repeat;
            opacity: 0.25;
            z-index: 0;
        }

        /* Animated background bubbles */
        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: float 6s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        /* Container utama */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* Card Login */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }

        /* Header Card */
        .card-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            /* margin: 0 auto 20px; */
            margin-bottom: 10px;
            font-size: 24px;
            color: white;
        }

        .card-header h3 {
            color: white;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .card-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        /* Body Card */
        .card-body {
            padding: 35px 30px;
        }

        /* Input Group */
        .input-group {
            margin-bottom: 15px;
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            transition: color 0.3s;
            z-index: 1;
        }

        .input-field {
            width: 100%;
            padding: 12px 12px 12px 40px;
            font-size: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            background: white;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .input-field:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-field:hover {
            border-color: #cbd5e1;
        }

        /* Label */
        .input-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        /* Remember Me */
        .remember-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .checkbox span {
            font-size: 14px;
            color: #6b7280;
        }

        .forgot-link {
            font-size: 14px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #3720b9;
            text-decoration: underline;
        }

        /* Button Login */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Alert Messages */
        .alert {
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* Footer */
        .card-footer {
            text-align: center;
            /* padding: 20px 30px 30px; */
            padding: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .card-footer p {
            color: #9ca3af;
            font-size: 13px;
        }

        .card-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        /* Loading State */
        .btn-login.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-login.loading .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                max-width: 100%;
            }

            .card-header {
                padding: 25px 20px;
            }

            .card-body {
                padding: 20px;
            }

            .card-header h3 {
                font-size: 24px;
            }
        }

        @media (max-height: 700px) {
            .login-card {
                transform: scale(0.9);
            }
        }

        @media (max-height: 600px) {
            .login-card {
                transform: scale(0.8);
            }
        }

        .login-card {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <!-- Animated Bubbles -->
    <div class="bubble" style="width: 80px; height: 80px; top: 10%; left: 5%; animation-delay: 0s;"></div>
    <div class="bubble" style="width: 120px; height: 120px; top: 70%; left: 85%; animation-delay: 1s;"></div>
    <div class="bubble" style="width: 60px; height: 60px; top: 40%; left: 15%; animation-delay: 2s;"></div>
    <div class="bubble" style="width: 100px; height: 100px; top: 80%; left: 10%; animation-delay: 0.5s;"></div>
    <div class="bubble" style="width: 50px; height: 50px; top: 20%; left: 90%; animation-delay: 1.5s;"></div>
    <div class="bubble" style="width: 90px; height: 90px; top: 50%; left: 95%; animation-delay: 2.5s;"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <div class="logo-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Selamat Datang</h3>
                <p>Sistem Penjadwalan Mata Pelajaran</p>
                <p>SMPN 1 Enam Lingkung</p>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-user"></i> Username / Email
                        </label>
                        <input type="text" name="username" class="input-field"
                            placeholder="Masukkan username atau email" value="{{ old('username') }}" required autofocus>
                    </div>

                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" name="password" class="input-field" placeholder="Masukkan password"
                            required>
                    </div>

                    <div class="remember-group">
                        <label class="checkbox">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>
                </form>
            </div>

            <div class="card-footer">
                <p>&copy; {{ date('Y') }} Sistem Penjadwalan Mata Pelajaran. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // Loading effect on form submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnLogin');
            btn.classList.add('loading');
            btn.innerHTML = '<span class="spinner"></span> Memproses...';
            btn.disabled = true;
        });

        // Smooth animation for inputs
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = '#667eea';
            });
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('.input-icon').style.color = '#9ca3af';
            });
        });
    </script>
</body>

</html>
