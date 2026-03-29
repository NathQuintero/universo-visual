{{--
    Vista: Portal de Seguimiento (Vista del Cliente)
    Ruta: GET /seguimiento/{code?}
    Controlador: TrackingController@index
    
    Página PÚBLICA — no requiere login.
    Diseño independiente (no usa el layout del admin).
    Se ve en pantalla completa como una página web para el cliente.
--}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento — Universo Visual</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: linear-gradient(145deg, #060a1a, #0a1030, #0c0f28); color: #e4e9f2; min-height: 100vh; }
        .wrap { max-width: 520px; margin: 0 auto; padding: 40px 20px; text-align: center; }
        .logo { font-size: 52px; margin-bottom: 14px; filter: drop-shadow(0 0 25px rgba(74,108,247,0.4)); }
        .title { font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #4a6cf7, #7c5bf5); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; }
        .sub { font-size: 14px; color: #8490ab; margin-bottom: 28px; }
        .bx { background: rgba(17,24,39,0.8); border: 1px solid #1e2a4a; border-radius: 8px; padding: 18px; margin-bottom: 22px; text-align: left; }
        .bx-label { font-size: 12px; color: #556078; margin-bottom: 8px; }
        .search-row { display: flex; gap: 8px; }
        .search-row input { flex: 1; background: #0e1529; border: 1px solid #1e2a4a; border-radius: 6px; padding: 13px 16px; color: #e4e9f2; font-family: 'JetBrains Mono'; font-size: 16px; text-align: center; outline: none; }
        .search-row input:focus { border-color: #4a6cf7; }
        .search-btn { padding: 13px 24px; border: none; border-radius: 6px; background: linear-gradient(135deg, #4a6cf7, #7c5bf5); color: #fff; font-family: 'Outfit'; font-size: 15px; font-weight: 700; cursor: pointer; }
        .badge { display: inline-flex; padding: 5px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .badge-green { background: rgba(0,230,118,0.1); color: #00e676; }
        .badge-blue { background: rgba(74,108,247,0.12); color: #4a6cf7; }
        .badge-yellow { background: rgba(255,193,7,0.12); color: #ffc107; }
        .badge-orange { background: rgba(255,145,0,0.12); color: #ff9100; }
        .badge-red { background: rgba(239,68,68,0.1); color: #ef4444; }
        .badge-gray { background: rgba(85,96,120,0.15); color: #8490ab; }
        .result-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .code { font-family: 'JetBrains Mono'; font-size: 20px; font-weight: 800; color: #4a6cf7; }
        /* Timeline */
        .tl { display: flex; align-items: center; justify-content: space-between; padding: 22px 0; position: relative; margin-bottom: 20px; }
        .tl::before { content: ''; position: absolute; top: 50%; left: 30px; right: 30px; height: 3px; background: #1e2a4a; transform: translateY(-50%); z-index: 1; }
        .ts { display: flex; flex-direction: column; align-items: center; gap: 7px; z-index: 2; }
        .ts-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 17px; border: 3px solid #1e2a4a; background: #060a1a; }
        .ts.done .ts-icon { background: linear-gradient(135deg, #4a6cf7, #7c5bf5); border-color: #4a6cf7; box-shadow: 0 0 12px rgba(74,108,247,0.3); }
        .ts.now .ts-icon { background: linear-gradient(135deg, #ffc107, #ff9100); border-color: #ffc107; box-shadow: 0 0 12px rgba(255,193,7,0.3); animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100% { box-shadow: 0 0 12px rgba(255,193,7,0.3); } 50% { box-shadow: 0 0 22px rgba(255,193,7,0.5); } }
        .ts-lbl { font-size: 11px; font-weight: 600; }
        .ts-date { font-size: 10px; color: #556078; font-family: 'JetBrains Mono'; }
        .ts.pending .ts-lbl { color: #556078; }
        .msg-box { border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
        .msg-green { background: rgba(0,230,118,0.06); border: 1px solid rgba(0,230,118,0.15); }
        .msg-blue { background: rgba(74,108,247,0.06); border: 1px solid rgba(74,108,247,0.15); }
        .msg-yellow { background: rgba(255,193,7,0.06); border: 1px solid rgba(255,193,7,0.15); }
        .msg-title { font-size: 14px; font-weight: 600; }
        .msg-sub { font-size: 12px; color: #8490ab; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
        .info-card { background: #111827; padding: 12px; border-radius: 6px; }
        .info-card .label { font-size: 11px; color: #556078; }
        .info-card .value { font-size: 14px; font-weight: 700; margin-top: 2px; }
        .wa-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 14px; border-radius: 6px; background: #25D366; color: #fff; border: none; font-family: 'Outfit'; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .footer { margin-top: 16px; font-size: 11px; color: #556078; }
        .error-box { background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15); border-radius: 8px; padding: 20px; text-align: center; color: #ef4444; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">👁️</div>
        <h1 class="title">Universo Visual</h1>
        <p class="sub">Consulta el estado de tu pedido</p>

        {{-- Buscador --}}
        <div class="bx">
            <p class="bx-label">Ingresa el código de tu recibo o escanea el QR</p>
            <form action="{{ route('tracking.search') }}" method="POST" class="search-row">
                @csrf
                <input name="code" value="{{ $code ?? '' }}" placeholder="UV-2026-00234" required>
                <button type="submit" class="search-btn">🔍 Buscar</button>
            </form>
        </div>

        {{-- Resultado --}}
        @if($code && !$work)
            <div class="error-box">
                <p style="font-size:20px;margin-bottom:8px">😕</p>
                <p style="font-size:15px;font-weight:700">No encontramos ese código</p>
                <p style="font-size:12px;color:#8490ab;margin-top:4px">Verifica que el código sea correcto e intenta de nuevo.</p>
            </div>
        @endif

        @if($work)
            @php
                $statusOrder = ['registered', 'sent_to_lab', 'in_process', 'received', 'ready', 'delivered'];
                $statusEmojis = ['📝', '📦', '🔬', '📬', '✅', '🎉'];
                $statusLabels = ['Recibido', 'Enviado', 'En Proceso', 'Recibido', 'Listo', 'Entregado'];
                $currentIndex = array_search($work->status, $statusOrder);
                if ($currentIndex === false) $currentIndex = -1;
                $statusColors = ['blue', 'yellow', 'orange', 'purple', 'green', 'gray'];
            @endphp

            <div class="bx">
                {{-- Header --}}
                <div class="result-head">
                    <div>
                        <p style="font-size:11px;color:#556078">Pedido</p>
                        <p class="code">{{ $work->tracking_code }}</p>
                    </div>
                    <span class="badge badge-{{ $statusColors[$currentIndex] ?? 'gray' }}">
                        {{ $work->status_emoji }} {{ $work->status_name }}
                    </span>
                </div>

                {{-- Timeline --}}
                <div class="tl">
                    @foreach($statusOrder as $i => $status)
                        @php
                            $change = $work->statusChanges->firstWhere('to_status', $status);
                            $cls = 'pending';
                            if ($i < $currentIndex) $cls = 'done';
                            elseif ($i == $currentIndex) $cls = ($work->status == 'delivered' ? 'done' : 'now');
                        @endphp
                        <div class="ts {{ $cls }}">
                            <div class="ts-icon">{{ $statusEmojis[$i] }}</div>
                            <div class="ts-lbl">{{ $statusLabels[$i] }}</div>
                            <div class="ts-date">{{ $change ? $change->created_at->format('d M') : '—' }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Mensaje contextual --}}
                @php
                    $msgType = match($work->status) {
                        'ready', 'delivered' => 'green',
                        'registered', 'sent_to_lab' => 'blue',
                        default => 'yellow',
                    };
                    $msgEmoji = match($work->status) {
                        'ready' => '🎉',
                        'delivered' => '✅',
                        'in_process' => '🔬',
                        default => '📋',
                    };
                @endphp
                <div class="msg-box msg-{{ $msgType }}">
                    <span style="font-size:22px">{{ $msgEmoji }}</span>
                    <div>
                        <p class="msg-title">¡Hola {{ $work->client->first_name }}!</p>
                        <p class="msg-sub">{{ $work->tracking_message }}</p>
                    </div>
                </div>

                {{-- Info del trabajo --}}
                <div class="info-grid">
                    <div class="info-card">
                        <div class="label">Tipo de lente</div>
                        <div class="value">{{ $work->lens_type_name }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Tratamientos</div>
                        <div class="value">{{ $work->treatments_text }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Saldo pendiente</div>
                        <div class="value" style="color:{{ $work->pending_balance > 0 ? '#ef4444' : '#00e676' }}">
                            ${{ number_format($work->pending_balance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="label">Laboratorio</div>
                        <div class="value">{{ $work->laboratory->name }}</div>
                    </div>
                </div>

                {{-- Botón WhatsApp --}}
                @php $whatsapp = \App\Models\Setting::getValue('business_whatsapp', '573001234567'); @endphp
                <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola, consulto por mi pedido ' . $work->tracking_code) }}" target="_blank" class="wa-btn">
                    💬 ¿Tienes preguntas? Escríbenos por WhatsApp
                </a>
            </div>
        @endif

        <p class="footer">📍 Óptica Universo Visual — C.C. La Isla, Bucaramanga — © {{ date('Y') }}</p>
    </div>
</body>
</html>