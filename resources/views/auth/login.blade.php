<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — ARStock</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .login-container {
            display: flex;
            height: 100vh;
        }

        /* ===== PANEL IZQUIERDO ===== */
        .left-panel {
            width: 42%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 4rem;
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            position: absolute;
            top: 2.5rem;
            left: 4rem;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: #0f1b2d;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 18px;
            height: 18px;
            color: #2dd4bf;
        }

        .logo-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.02em;
        }

        .avatar {
            width: 80px;
            height: 80px;
            background: #0f1b2d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2.5rem;
        }

        .avatar svg {
            width: 40px;
            height: 40px;
            color: #94a3b8;
        }

        .form-wrapper {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .error-alert p {
            font-size: 0.8rem;
            color: #dc2626;
        }

        .success-alert {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .success-alert p {
            font-size: 0.8rem;
            color: #16a34a;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group .icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .input-group .icon svg {
            width: 16px;
            height: 16px;
            color: #94a3b8;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 50px;
            font-size: 0.85rem;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group input::placeholder {
            color: #94a3b8;
        }

        .input-group input:focus {
            border-color: #0f1b2d;
            box-shadow: 0 0 0 3px rgba(15, 27, 45, 0.08);
        }

        .input-group .toggle-pw {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .input-group .toggle-pw svg {
            width: 16px;
            height: 16px;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .input-group .toggle-pw:hover svg {
            color: #475569;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #0f1b2d;
            color: #ffffff;
            border: none;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: #1a2d47;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
        }

        .remember-row input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: #0f1b2d;
            cursor: pointer;
        }

        .remember-row label {
            font-size: 0.75rem;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }

        .footer {
            text-align: center;
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 3rem;
        }

        /* ===== PANEL DERECHO ===== */
        .right-panel {
            width: 58%;
            background: linear-gradient(135deg, #0f1b2d 0%, #153a5c 35%, #1a6b6a 65%, #2dd4bf 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        .right-panel .waves {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .right-panel .wave {
            position: absolute;
            width: 160%;
            left: -30%;
        }

        .right-panel .wave-1 {
            bottom: 5%;
            opacity: 0.08;
        }

        .right-panel .wave-2 {
            bottom: 15%;
            opacity: 0.06;
        }

        .right-panel .wave-3 {
            bottom: 28%;
            opacity: 0.04;
        }

        .right-panel .circle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .right-panel .circle-1 {
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.04);
            top: -10%;
            right: -10%;
        }

        .right-panel .circle-2 {
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.03);
            bottom: 10%;
            left: -8%;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 4rem;
        }

        .welcome-content h1 {
            font-size: 3.8rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.03em;
            margin-bottom: 12px;
        }

        .welcome-content p {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.6);
            max-width: 380px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ===== MOBILE ===== */
        @media (max-width: 1023px) {
            .right-panel { display: none; }
            .left-panel {
                width: 100%;
                padding: 2rem;
            }
            .logo {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 3rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">

    {{-- ===== PANEL IZQUIERDO: FORMULARIO ===== --}}
    <div class="left-panel">

        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <span class="logo-text">ARStock</span>
        </div>

        <div class="avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>

        <div class="form-wrapper">

            @if ($errors->isNotEmpty())
                <div class="error-alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="success-alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <div class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="Correo electrónico">
                </div>

                <div class="input-group">
                    <div class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <input type="password" id="password" name="password" autocomplete="current-password" placeholder="Contraseña">
                    <button type="button" class="toggle-pw" onclick="togglePassword()" tabindex="-1">
                        <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <button type="submit" class="btn-submit">Entrar al Sistema</button>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Recordarme</label>
                </div>
            </form>

            <p class="footer">ARStock &copy; {{ date('Y') }}</p>
        </div>
    </div>

    {{-- ===== PANEL DERECHO: BIENVENIDO ===== --}}
    <div class="right-panel">

        <div class="waves">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <svg class="wave wave-1" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path fill="#fff" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,218.7C672,235,768,245,864,234.7C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/>
            </svg>
            <svg class="wave wave-2" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path fill="#fff" d="M0,288L48,272C96,256,192,224,288,208C384,192,480,192,576,202.7C672,213,768,235,864,240C960,245,1056,235,1152,213.3C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/>
            </svg>
            <svg class="wave wave-3" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path fill="#fff" d="M0,160L48,170.7C96,181,192,203,288,213.3C384,224,480,224,576,208C672,192,768,160,864,154.7C960,149,1056,171,1152,186.7C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/>
            </svg>
        </div>

        <div class="welcome-content">
            <h1>Bienvenido.</h1>
            <p>Ingresá con tu cuenta para acceder al sistema de gestión.</p>
        </div>
    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" x2="23" y1="1" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
</script>

</body>
</html>