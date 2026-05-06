<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Universo Visual</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background: #f8f9fc;
            display: flex;
        }

        /* ==========================================
           LAYOUT SPLIT 50/50
           ========================================== */
        .split-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #fff;
            animation: fadeInUp 0.8s ease both;
        }

        .split-right {
            flex: 1;
            position: relative;
            background: linear-gradient(135deg, #1a4fd0 0%, #103192 50%, #0a2060 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            min-height: 100vh;
        }

        /* ==========================================
           FORMULARIO — Lado izquierdo
           ========================================== */
        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .brand {
            margin-bottom: 48px;
        }

        .brand-logo {
            height: 104px;
            width: auto;
            object-fit: contain;
        }

        .form-heading {
            font-size: 32px;
            font-weight: 800;
            color: #1a1f36;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .form-subheading {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 36px;
            line-height: 1.5;
        }

        /* Campos de entrada */
        .input-group {
            margin-bottom: 22px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            width: 20px;
            height: 20px;
            color: #9ca3af;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            color: #1a1f36;
            background: #f9fafb;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
        }

        .input-wrapper input:hover {
            border-color: #809bd4;
        }

        .input-wrapper input:focus {
            border-color: #103192;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(16, 49, 146, 0.12);
        }

        .input-wrapper input:focus ~ .input-icon,
        .input-wrapper input:focus + .input-icon {
            color: #103192;
        }

        /* Reordenar para que el selector CSS funcione */
        .input-wrapper .input-icon {
            order: -1;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #103192;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .custom-checkbox {
            position: relative;
            width: 20px;
            height: 20px;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 1;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background: #fff;
        }

        .checkmark svg {
            width: 12px;
            height: 12px;
            stroke: #fff;
            stroke-width: 3;
            fill: none;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s ease;
        }

        .custom-checkbox input:checked + .checkmark {
            background: linear-gradient(135deg, #103192, #1a4fd0);
            border-color: #103192;
        }

        .custom-checkbox input:checked + .checkmark svg {
            opacity: 1;
            transform: scale(1);
        }

        .remember-label {
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }

        /* Botón de login */
        .login-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(16, 49, 146, 0.35);
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 32px rgba(16, 49, 146, 0.45);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        /* Error */
        .login-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            animation: shakeX 0.5s ease;
        }

        .login-error svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* Footer */
        .form-footer {
            margin-top: 36px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
        }

        /* ==========================================
           PANEL DECORATIVO — Lado derecho
           ========================================== */
        .decorative-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 40px;
            max-width: 480px;
        }

        .decorative-content h2 {
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .decorative-content p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            max-width: 360px;
            margin: 0 auto;
        }

        .decorative-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 50px;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 32px;
        }

        .decorative-badge svg {
            width: 18px;
            height: 18px;
        }

        .features-grid {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 40px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .feature-icon:hover {
            transform: translateY(-4px);
        }

        .feature-icon svg {
            width: 22px;
            height: 22px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
        }

        .feature-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        /* ==========================================
           BURBUJAS ANIMADAS
           ========================================== */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .bubble-1 {
            width: 300px;
            height: 300px;
            top: -80px;
            right: -60px;
            animation: float1 8s ease-in-out infinite;
        }

        .bubble-2 {
            width: 200px;
            height: 200px;
            bottom: -40px;
            left: -50px;
            animation: float2 10s ease-in-out infinite;
        }

        .bubble-3 {
            width: 120px;
            height: 120px;
            top: 30%;
            left: 10%;
            animation: float3 7s ease-in-out infinite;
        }

        .bubble-4 {
            width: 80px;
            height: 80px;
            bottom: 25%;
            right: 15%;
            animation: float1 9s ease-in-out infinite reverse;
        }

        .bubble-5 {
            width: 160px;
            height: 160px;
            top: 60%;
            right: -30px;
            animation: float2 12s ease-in-out infinite;
        }

        .bubble-6 {
            width: 60px;
            height: 60px;
            top: 15%;
            left: 40%;
            background: rgba(255, 255, 255, 0.12);
            animation: float3 6s ease-in-out infinite;
        }

        .bubble-7 {
            width: 100px;
            height: 100px;
            bottom: 10%;
            left: 30%;
            animation: float1 11s ease-in-out infinite;
        }

        .bubble-8 {
            width: 45px;
            height: 45px;
            top: 45%;
            right: 35%;
            background: rgba(255, 255, 255, 0.14);
            animation: float2 7.5s ease-in-out infinite reverse;
        }

        /* ==========================================
           KEYFRAMES
           ========================================== */
        @keyframes float1 {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-30px) translateX(10px); }
            50% { transform: translateY(-15px) translateX(-10px); }
            75% { transform: translateY(-35px) translateX(5px); }
        }

        @keyframes float2 {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); }
            33% { transform: translateY(25px) translateX(-15px) scale(1.05); }
            66% { transform: translateY(-20px) translateX(10px) scale(0.95); }
        }

        @keyframes float3 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-40px) rotate(10deg); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shakeX {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* ==========================================
           RESPONSIVE — Móvil
           ========================================== */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .split-right {
                min-height: 35vh;
                flex: none;
            }

            .split-left {
                flex: 1;
                padding: 32px 24px;
            }

            .decorative-content h2 {
                font-size: 24px;
            }

            .decorative-content p {
                font-size: 14px;
            }

            .features-grid {
                margin-top: 20px;
                gap: 16px;
            }

            .feature-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
            }

            .feature-icon svg {
                width: 18px;
                height: 18px;
            }

            .form-heading {
                font-size: 26px;
            }

            .brand {
                margin-bottom: 32px;
            }

            .bubble-1 { width: 180px; height: 180px; }
            .bubble-2 { width: 120px; height: 120px; }
            .bubble-5 { display: none; }
            .bubble-7 { display: none; }
        }

        @media (max-width: 480px) {
            .split-left {
                padding: 24px 20px;
            }

            .form-heading {
                font-size: 24px;
            }

            .features-grid {
                gap: 12px;
            }

            .feature-label {
                font-size: 11px;
            }

            .decorative-badge {
                font-size: 12px;
                padding: 8px 14px;
            }
        }
    </style>
</head>
<body>

    {{-- ==========================================
         PANEL DECORATIVO — Lado derecho (en móvil va arriba)
         ========================================== --}}
    <div class="split-right">
        {{-- Burbujas animadas --}}
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
        <div class="bubble bubble-4"></div>
        <div class="bubble bubble-5"></div>
        <div class="bubble bubble-6"></div>
        <div class="bubble bubble-7"></div>
        <div class="bubble bubble-8"></div>

        <div class="decorative-content">
            <div class="decorative-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Sistema seguro y confiable
            </div>
            <h2>Gestión Óptica Inteligente</h2>
            <p>Trazabilidad, control y eficiencia en un solo lugar. Administra tus trabajos, clientes y laboratorios.</p>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <span class="feature-label">Seguimiento</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span class="feature-label">Clientes</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <span class="feature-label">Reportes</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <span class="feature-label">Recibos PDF</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
         FORMULARIO — Lado izquierdo
         ========================================== --}}
    <div class="split-left">
        <div class="login-form-wrapper">
            {{-- Logo / Marca --}}
            <div class="brand">
                <img src="{{ asset('images/univer_logo_azul_sf.png') }}" alt="Universo Visual" class="brand-logo">
            </div>

            {{-- Encabezado --}}
            <h1 class="form-heading">Bienvenido de vuelta</h1>
            <p class="form-subheading">Ingresa tus credenciales para acceder al sistema</p>

            {{-- Errores --}}
            @if($errors->any())
                <div class="login-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com"
                               required autofocus>
                    </div>
                </div>

                {{-- Contraseña --}}
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg id="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Recordarme --}}
                <div class="remember-row">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember">
                        <div class="checkmark">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                    </label>
                    <span class="remember-label">Recordarme en este equipo</span>
                </div>

                {{-- Botón --}}
                <button type="submit" class="login-btn">Iniciar Sesión</button>
            </form>

            <p class="form-footer">Óptica Universo Visual &copy; {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }

        // Focus animation en los input-icon
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.input-wrapper').querySelector('.input-icon').style.color = '#103192';
            });
            input.addEventListener('blur', () => {
                input.closest('.input-wrapper').querySelector('.input-icon').style.color = '#9ca3af';
            });
        });
    </script>
</body>
</html>