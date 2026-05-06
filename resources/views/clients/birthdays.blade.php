@extends('layouts.app')
@section('title', 'Cumpleaños')

@section('content')
    <div class="ph">
        <h2>🎂 Cumpleañeros del Mes</h2>
    </div>

    <p style="color:var(--text-secondary);margin-bottom:18px">
        {{ now()->translatedFormat('F Y') }} — {{ $clients->count() }} cliente(s) cumplen este mes
    </p>

    <div class="card">
        <div class="card-b">
            @forelse($clients as $client)
                <div class="bi">
                    <div class="bi-av">{{ $client->initials }}</div>
                    <div class="bi-info">
                        <h4>{{ $client->full_name }}</h4>
                        <p>
                            {{ $client->birth_date->translatedFormat('d \\d\\e F') }}
                            —
                            @if($client->isBirthdayToday())
                                ¡Hoy! 🎉
                            @else
                                En {{ $client->daysUntilBirthday() }} día(s)
                            @endif
                            • {{ $client->works()->count() }} trabajos
                            @if($client->email)
                                • {{ $client->email }}
                            @endif
                        </p>
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">
                        @if($client->wasGreetedThisYear())
                            <span class="bi-btn" style="background:#16a34a;cursor:default;opacity:0.85">Felicitado</span>
                        @elseif($client->phone && $client->whatsapp_authorized)
                            @php
                                $waPhone = '57' . preg_replace('/[^0-9]/', '', $client->phone);
                                $msgCumple = "Hola " . $client->first_name . ",\n\n"
                                    . "Hoy es un dia muy especial. Desde Optica Universo Visual queremos desearte un muy feliz cumpleanos.\n\n"
                                    . "Que Dios te bendiga grandemente, te llene de salud, amor y muchas bendiciones en este nuevo ano de vida.\n\n"
                                    . "\"Porque yo se los planes que tengo para ustedes, planes de bienestar y no de calamidad, a fin de darles un futuro y una esperanza.\" - Jeremias 29:11\n\n"
                                    . "Para celebrar contigo, te regalamos un 15% de descuento en tu proxima compra.";

                                if ($client->email) {
                                    $msgCumple .= " Revisa tu correo (" . $client->email . "), te enviamos un obsequio especial.";
                                }

                                $msgCumple .= " Este descuento tiene vigencia desde tu cumpleanos hasta 10 dias despues.\n\n"
                                    . "Ven a visitarnos y estrena con estilo. Te esperamos con los brazos abiertos.\n\n"
                                    . "Con carino,\n"
                                    . "Tu familia de Optica Universo Visual\n"
                                    . "Centro Comercial La Isla, Bucaramanga";

                                $waUrl = "https://wa.me/{$waPhone}?text=" . rawurlencode($msgCumple);
                            @endphp
                            <button type="button" class="bi-btn"
                                    onclick="felicitarCumple(this, '{{ route('clients.sendBirthday', $client) }}', '{{ $waUrl }}', {{ $client->email ? 'true' : 'false' }})"
                                    style="min-width:110px">
                                Felicitar
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p style="text-align:center;padding:30px;color:var(--text-muted)">No hay cumpleañeros este mes.</p>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function felicitarCumple(btn, emailUrl, waUrl, tieneEmail) {
        // 1. Abrir WhatsApp INMEDIATAMENTE (acción directa del usuario, no se bloquea)
        window.open(waUrl, '_blank');

        // 2. Marcar como felicitado y enviar email
        btn.innerHTML = 'Enviando...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.style.cursor = 'default';

        fetch(emailUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (data.message) mostrarNotificacion(data.message, 'success');
            marcarFelicitado(btn);
        })
        .catch(() => {
            mostrarNotificacion('Error de conexion', 'error');
            marcarFelicitado(btn);
        });
    }

    function marcarFelicitado(btn) {
        btn.innerHTML = 'Felicitado';
        btn.disabled = true;
        btn.style.opacity = '0.85';
        btn.style.background = '#16a34a';
        btn.style.cursor = 'default';
        btn.onclick = null;
    }

    function mostrarNotificacion(mensaje, tipo) {
        const existing = document.querySelector('.ajax-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'ajax-toast';
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:14px 22px;border-radius:12px;font-family:Outfit,sans-serif;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;box-shadow:0 6px 20px rgba(0,0,0,0.15);animation:slideD .4s ease;max-width:400px;';

        if (tipo === 'success') {
            toast.style.background = '#dcfce7';
            toast.style.border = '1px solid #86efac';
            toast.style.borderLeft = '4px solid #16a34a';
            toast.style.color = '#155724';
        } else {
            toast.style.background = '#fee2e2';
            toast.style.border = '1px solid #fca5a5';
            toast.style.borderLeft = '4px solid #dc2626';
            toast.style.color = '#721c24';
        }
        toast.textContent = mensaje;

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all .3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
</script>
@endsection
