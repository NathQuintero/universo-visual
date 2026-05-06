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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seguimiento — Universo Visual</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #F0F2F5; color: #1a1a2e; min-height: 100vh; }
        .wrap { max-width: 540px; margin: 0 auto; padding: 40px 20px; text-align: center; }
        .logo-img { height: 70px; width: auto; object-fit: contain; margin-bottom: 10px; }
        .sub { font-size: 14px; color: #6c757d; margin-bottom: 28px; }
        .bx { background: #FFFFFF; border: 1px solid #e0e0e0; border-radius: 16px; padding: 20px; margin-bottom: 22px; text-align: left; box-shadow: 0 2px 10px rgba(16,49,146,0.06); }
        .bx-label { font-size: 12px; color: #9ca3af; margin-bottom: 8px; }
        .search-row { display: flex; gap: 8px; }
        .search-row input { flex: 1; background: #F8F9FC; border: 1px solid #e0e0e0; border-radius: 10px; padding: 13px 16px; color: #1a1a2e; font-family: 'JetBrains Mono'; font-size: 16px; text-align: center; outline: none; transition: all 0.3s ease; }
        .search-row input:focus { border-color: #103192; box-shadow: 0 0 0 3px rgba(16,49,146,0.08); }
        .search-btn { padding: 13px 24px; border: none; border-radius: 10px; background: linear-gradient(135deg, #103192, #1a4fd0); color: #fff; font-family: 'Outfit'; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(16,49,146,0.25); }
        .search-btn:hover { transform: scale(1.02); box-shadow: 0 6px 18px rgba(16,49,146,0.35); }
        .badge { display: inline-flex; padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; }
        .badge-green { background: rgba(22,163,74,0.08); color: #15803d; }
        .badge-blue { background: rgba(16,49,146,0.08); color: #103192; }
        .badge-yellow { background: rgba(217,119,6,0.08); color: #b45309; }
        .badge-orange { background: rgba(234,88,12,0.08); color: #c2410c; }
        .badge-red { background: rgba(220,38,38,0.08); color: #dc2626; }
        .badge-gray { background: rgba(107,114,128,0.08); color: #6c757d; }
        .badge-purple { background: rgba(26,79,208,0.08); color: #1a4fd0; }
        .result-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .code { font-family: 'JetBrains Mono'; font-size: 20px; font-weight: 800; color: #103192; }
        /* Timeline */
        .tl { display: flex; align-items: center; justify-content: space-between; padding: 22px 0; position: relative; margin-bottom: 20px; }
        .tl::before { content: ''; position: absolute; top: 50%; left: 30px; right: 30px; height: 3px; background: #e0e0e0; transform: translateY(-50%); z-index: 1; }
        .ts { display: flex; flex-direction: column; align-items: center; gap: 7px; z-index: 2; }
        .ts-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 17px; border: 3px solid #e0e0e0; background: #FFFFFF; transition: all 0.3s ease; }
        .ts.done .ts-icon { background: linear-gradient(135deg, #103192, #1a4fd0); border-color: #103192; box-shadow: 0 0 12px rgba(16,49,146,0.2); }
        .ts.now .ts-icon { background: linear-gradient(135deg, #d97706, #ea580c); border-color: #d97706; box-shadow: 0 0 12px rgba(217,119,6,0.3); animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100% { box-shadow: 0 0 12px rgba(217,119,6,0.3); } 50% { box-shadow: 0 0 22px rgba(217,119,6,0.5); } }
        .ts-lbl { font-size: 11px; font-weight: 600; color: #1a1a2e; }
        .ts-date { font-size: 10px; color: #9ca3af; font-family: 'JetBrains Mono'; }
        .ts.pending .ts-lbl { color: #9ca3af; }
        .msg-box { border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
        .msg-green { background: #D4EDDA; border: 1px solid #28A745; }
        .msg-blue { background: rgba(16,49,146,0.06); border: 1px solid rgba(16,49,146,0.15); }
        .msg-yellow { background: #FFF3CD; border: 1px solid #FFD93D; }
        .msg-title { font-size: 14px; font-weight: 600; color: #1a1a2e; }
        .msg-sub { font-size: 12px; color: #6c757d; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
        .info-card { background: #F8F9FC; padding: 14px; border-radius: 12px; border: 1px solid #eaedf3; }
        .info-card .label { font-size: 11px; color: #9ca3af; }
        .info-card .value { font-size: 14px; font-weight: 700; margin-top: 2px; color: #1a1a2e; }
        .wa-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 14px; border-radius: 12px; background: #25D366; color: #fff; border: none; font-family: 'Outfit'; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(37,211,102,0.25); }
        .wa-btn:hover { transform: scale(1.02); box-shadow: 0 6px 18px rgba(37,211,102,0.35); }
        .footer { margin-top: 20px; font-size: 11px; color: #9ca3af; }
        .error-box { background: #FFF5F5; border: 1px solid #FECACA; border-radius: 16px; padding: 24px; text-align: center; color: #dc2626; }
        @media (max-width: 480px) {
            .wrap { padding: 24px 12px; }
            .logo-img { height: 50px; }
            .bx { padding: 14px; }
            .search-row { flex-direction: column; }
            .search-btn { width: 100%; }
            .search-row input { font-size: 14px; }
            .tl { gap: 4px; overflow-x: auto; padding: 14px 4px; }
            .ts-icon { width: 34px; height: 34px; font-size: 14px; }
            .ts-lbl { font-size: 9px; }
            .ts-date { font-size: 8px; }
            .info-grid { grid-template-columns: 1fr; }
            .code { font-size: 17px; }
            .result-head { flex-direction: column; gap: 8px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <img src="{{ asset('images/univer_logo_azul_sf.png') }}" alt="Universo Visual" class="logo-img">
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
                <p style="font-size:12px;color:#6c757d;margin-top:4px">Verifica que el código sea correcto e intenta de nuevo.</p>
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
                        <p style="font-size:11px;color:#9ca3af">Pedido</p>
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
                        <div class="value" style="color:{{ $work->pending_balance > 0 ? '#dc2626' : '#16a34a' }}">
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

    {{-- =============================================
         CHATBOT — Asistente virtual para clientes
         ============================================= --}}
    <style>
        .uv-chat-fab {
            position: fixed; bottom: 22px; right: 22px;
            width: 60px; height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff; border: none;
            font-size: 26px; cursor: pointer;
            box-shadow: 0 6px 20px rgba(16,49,146,0.35);
            display: flex; align-items: center; justify-content: center;
            z-index: 9998;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .uv-chat-fab:hover { transform: scale(1.08); box-shadow: 0 8px 28px rgba(16,49,146,0.45); }
        .uv-chat-fab.hidden { display: none; }

        .uv-chat-win {
            position: fixed; bottom: 22px; right: 22px;
            width: 360px; height: 520px;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(16,49,146,0.25);
            display: none; flex-direction: column;
            overflow: hidden;
            z-index: 9999;
            transform: translateY(20px) scale(0.96); opacity: 0;
            transition: transform .25s ease, opacity .25s ease;
            font-family: 'Outfit', sans-serif;
        }
        .uv-chat-win.open { display: flex; transform: translateY(0) scale(1); opacity: 1; }

        .uv-chat-head {
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff;
            padding: 14px 16px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .uv-chat-head h3 { font-size: 15px; font-weight: 700; margin: 0; }
        .uv-chat-head button {
            background: rgba(255,255,255,0.18); border: none; color: #fff;
            width: 28px; height: 28px; border-radius: 8px; cursor: pointer;
            font-size: 16px; font-weight: 700;
            transition: background .2s ease;
        }
        .uv-chat-head button:hover { background: rgba(255,255,255,0.32); }

        .uv-chat-body {
            flex: 1; overflow-y: auto; padding: 14px;
            background: #F8F9FC; display: flex; flex-direction: column; gap: 10px;
        }
        .uv-chat-body::-webkit-scrollbar { width: 5px; }
        .uv-chat-body::-webkit-scrollbar-thumb { background: #c5cad3; border-radius: 10px; }

        .uv-msg { max-width: 80%; padding: 10px 14px; border-radius: 14px; font-size: 13.5px; line-height: 1.45; word-wrap: break-word; white-space: pre-wrap; animation: uvFade .25s ease; }
        @keyframes uvFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .uv-msg.bot { background: #103192; color: #fff; align-self: flex-start; border-bottom-left-radius: 4px; }
        .uv-msg.user { background: #e9ecef; color: #1a1a2e; align-self: flex-end; border-bottom-right-radius: 4px; }
        .uv-msg.typing { background: #103192; color: #fff; align-self: flex-start; border-bottom-left-radius: 4px; padding: 12px 16px; }
        .uv-typing-dot { display: inline-block; width: 6px; height: 6px; margin: 0 1px; border-radius: 50%; background: #fff; animation: uvBlink 1.2s infinite; }
        .uv-typing-dot:nth-child(2) { animation-delay: .2s; }
        .uv-typing-dot:nth-child(3) { animation-delay: .4s; }
        @keyframes uvBlink { 0%, 60%, 100% { opacity: .3; } 30% { opacity: 1; } }

        .uv-chat-input {
            display: flex; gap: 8px; padding: 12px;
            border-top: 1px solid #e0e0e0; background: #FFFFFF;
        }
        .uv-chat-input input {
            flex: 1; border: 1px solid #e0e0e0; border-radius: 10px;
            padding: 10px 14px; font-family: 'Outfit', sans-serif; font-size: 13.5px;
            outline: none; background: #F8F9FC; color: #1a1a2e;
            transition: all .2s ease;
        }
        .uv-chat-input input:focus { border-color: #103192; background: #fff; box-shadow: 0 0 0 3px rgba(16,49,146,0.08); }
        .uv-chat-input button {
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff; border: none; border-radius: 10px;
            padding: 0 16px; font-size: 16px; cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .uv-chat-input button:hover:not(:disabled) { transform: scale(1.05); box-shadow: 0 4px 12px rgba(16,49,146,0.25); }
        .uv-chat-input button:disabled { opacity: .5; cursor: not-allowed; }

        .uv-chips { display: flex; flex-direction: column; gap: 6px; align-self: stretch; margin-top: 4px; animation: uvFade .3s ease; }
        .uv-chips-label { font-size: 11px; color: #6c757d; padding: 0 4px 2px; font-weight: 500; }
        .uv-chip {
            background: #fff; border: 1px solid #c7d2fe; color: #103192;
            padding: 9px 14px; border-radius: 12px; cursor: pointer;
            font-family: 'Outfit', sans-serif; font-size: 12.5px; font-weight: 500;
            text-align: left; transition: all .2s ease;
        }
        .uv-chip:hover { background: #103192; color: #fff; border-color: #103192; transform: translateX(3px); box-shadow: 0 3px 10px rgba(16,49,146,0.2); }

        @media (max-width: 480px) {
            .uv-chat-win { width: 100%; height: 100%; bottom: 0; right: 0; border-radius: 0; }
            .uv-chat-fab { bottom: 16px; right: 16px; width: 54px; height: 54px; font-size: 22px; }
        }
    </style>

    <button class="uv-chat-fab" id="uvChatFab" onclick="uvChatOpen()" aria-label="Abrir chat">💬</button>

    <div class="uv-chat-win" id="uvChatWin" role="dialog" aria-label="Asistente Universo Visual">
        <div class="uv-chat-head">
            <h3>Asistente Universo Visual 🤓</h3>
            <button onclick="uvChatClose()" aria-label="Cerrar chat">✕</button>
        </div>
        <div class="uv-chat-body" id="uvChatBody"></div>
        <form class="uv-chat-input" id="uvChatForm" onsubmit="return uvChatSend(event)">
            <input type="text" id="uvChatInput" placeholder="Escribe tu mensaje..." autocomplete="off" maxlength="1000" required>
            <button type="submit" id="uvChatSendBtn">➤</button>
        </form>
    </div>

    <script>
    (function() {
        const TRACKING_CODE = @json($code ?? null);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const MAX_HISTORY = 20;
        const WELCOME = "¡Hola! 👋 Soy Univer, tu asistente de Óptica Universo Visual 🤓✨\n\nCuéntame en qué puedo ayudarte o elige una opción 👇";
        const SUGGESTIONS = [
            '📍 ¿Dónde están ubicados?',
            '🕐 ¿Cuál es el horario de atención?',
            '👓 ¿Qué tipos de lentes manejan?',
            '💳 ¿Qué métodos de pago aceptan?',
            '⏱️ ¿Cuánto tarda mi pedido?',
        ];

        // Historial vivo (en memoria). Se reinicia al cerrar el chat.
        let history = [];

        const fab  = document.getElementById('uvChatFab');
        const win  = document.getElementById('uvChatWin');
        const body = document.getElementById('uvChatBody');
        const input = document.getElementById('uvChatInput');
        const sendBtn = document.getElementById('uvChatSendBtn');

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }
        function formatMarkdown(text) {
            // Escapa primero, luego convierte **texto** en <strong>
            return escapeHtml(text).replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>');
        }
        function appendMessage(role, text) {
            const div = document.createElement('div');
            div.className = 'uv-msg ' + (role === 'bot' ? 'bot' : 'user');
            div.innerHTML = formatMarkdown(text);
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
        }
        function showTyping() {
            const div = document.createElement('div');
            div.className = 'uv-msg typing';
            div.id = 'uvTyping';
            div.innerHTML = '<span class="uv-typing-dot"></span><span class="uv-typing-dot"></span><span class="uv-typing-dot"></span>';
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
        }
        function hideTyping() {
            const t = document.getElementById('uvTyping');
            if (t) t.remove();
        }
        function renderChips() {
            const wrap = document.createElement('div');
            wrap.className = 'uv-chips';
            wrap.id = 'uvChips';
            const lbl = document.createElement('div');
            lbl.className = 'uv-chips-label';
            lbl.textContent = 'Sugerencias rápidas';
            wrap.appendChild(lbl);
            SUGGESTIONS.forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'uv-chip';
                btn.textContent = s;
                btn.onclick = () => sendUserText(s);
                wrap.appendChild(btn);
            });
            body.appendChild(wrap);
            body.scrollTop = body.scrollHeight;
        }
        function hideChips() {
            const c = document.getElementById('uvChips');
            if (c) c.remove();
        }
        function resetChat() {
            history = [];
            body.innerHTML = '';
        }
        function initChat() {
            resetChat();
            appendMessage('bot', WELCOME);
            renderChips();
        }

        window.uvChatOpen = function() {
            if (body.children.length === 0) initChat();
            fab.classList.add('hidden');
            win.classList.add('open');
            setTimeout(() => input.focus(), 250);
        };
        window.uvChatClose = function() {
            win.classList.remove('open');
            fab.classList.remove('hidden');
            // Cada cierre = nueva sesión: borra contexto e historial
            setTimeout(resetChat, 280);
        };

        async function sendUserText(text) {
            text = String(text || '').trim();
            if (!text) return;
            hideChips();
            appendMessage('user', text);
            input.value = '';
            input.disabled = true;
            sendBtn.disabled = true;
            showTyping();

            try {
                const payload = {
                    message: text,
                    history: history.slice(-MAX_HISTORY),
                };
                if (TRACKING_CODE) payload.tracking_code = TRACKING_CODE;

                const res = await fetch('{{ route('chat.public') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify(payload),
                });
                hideTyping();
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                const reply = data.response || 'Lo siento, no pude responder. Intenta de nuevo.';
                appendMessage('bot', reply);
                history.push({ role: 'user', content: text });
                history.push({ role: 'assistant', content: reply });
                if (history.length > MAX_HISTORY) history = history.slice(-MAX_HISTORY);
            } catch (err) {
                hideTyping();
                appendMessage('bot', 'Hubo un problema de conexión. Intenta de nuevo en un momentito 💙');
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            }
        }

        window.uvChatSend = function(e) {
            e.preventDefault();
            sendUserText(input.value);
            return false;
        };
    })();
    </script>
</body>
</html>