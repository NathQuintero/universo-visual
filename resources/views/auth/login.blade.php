<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Universo Visual</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /**
         * Login Page — Universo Visual
         * Página independiente (no usa el layout principal)
         * Diseño centrado con fondo oscuro azul
         */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(145deg, #060a1a, #0a1030, #0c0f28);
            color: #e4e9f2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-logo {
            font-size: 52px;
            margin-bottom: 14px;
            filter: drop-shadow(0 0 25px rgba(74,108,247,0.4));
        }

        .login-title {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #4a6cf7, #7c5bf5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 4px;
        }

        .login-sub {
            font-size: 14px;
            color: #8490ab;
            margin-bottom: 30px;
        }

        .login-card {
            background: rgba(17,24,39,0.8);
            border: 1px solid #1e2a4a;
            border-radius: 10px;
            padding: 30px;
            text-align: left;
        }

        .fg { margin-bottom: 18px; }

        .fg label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #8490ab;
            margin-bottom: 6px;
        }

        .fg input {
            width: 100%;
            background: #0e1529;
            border: 1px solid #1e2a4a;
            border-radius: 6px;
            padding: 12px 14px;
            color: #e4e9f2;
            font-family: 'Outfit';
            font-size: 14px;
            outline: none;
            transition: .18s ease;
        }

        .fg input:focus { border-color: #4a6cf7; }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #8490ab;
        }

        .login-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 6px;
            background: linear-gradient(135deg, #4a6cf7, #7c5bf5);
            color: #fff;
            font-family: 'Outfit';
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s ease;
            box-shadow: 0 4px 18px rgba(74,108,247,0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(74,108,247,0.4);
        }

        .login-error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #ef4444;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .login-footer {
            margin-top: 24px;
            font-size: 12px;
            color: #556078;
        }
    </style>
</head>
<body>
    <div class="login-box">
        {{-- Logo --}}
        <div class="login-logo">👁️</div>
        <h1 class="login-title">Universo Visual</h1>
        <p class="login-sub">Sistema de Gestión Óptica</p>

        {{-- Tarjeta del formulario --}}
        <div class="login-card">
            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="login-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="fg">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" 
                           value="{{ old('email') }}" 
                           placeholder="admin@universovisual.com"
                           required autofocus>
                </div>

                <div class="fg">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" 
                           placeholder="••••••••"
                           required>
                </div>

                <label class="remember-row">
                    <input type="checkbox" name="remember"> Recordarme en este equipo
                </label>

                <button type="submit" class="login-btn">Iniciar Sesión</button>
            </form>
        </div>

        <p class="login-footer">📍 C.C. La Isla, Bucaramanga — © {{ date('Y') }} Universo Visual</p>
    </div>
</body>
</html>