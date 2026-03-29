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
                        </p>
                    </div>
                    @if($client->phone && $client->whatsapp_authorized)
                        @php
                        $msgCumple = "Hola " . $client->first_name . "!\n\n"
                            . "Hoy es un dia muy especial!! Desde Optica Universo Visual queremos desearte un muy Feliz Cumpleaños! " . "\n\n"
                            . "Que Dios te bendiga grandemente, te llene de salud, amor y muchas bendiciones en este nuevo año de vida. " . "\n\n"
                            . "Para celebrar contigo, te regalamos un 15% de descuento en tu proxima compra, valido por dos días desde tu cumpleaños" . "\n\n"
                            . "Ven a visitarnos y estrena con estilo! Te esperamos con los brazos abiertos." . "\n\n"
                            . "Con carino,\n"
                            . "Tu familia de Optica Universo Visual\n"
                            . "C.C. La Isla, Bucaramanga";
                        @endphp
                        <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $client->phone) }}?text={{ rawurlencode($msgCumple) }}"
                           target="_blank" class="bi-btn">💬 Felicitar + 15%</a>
                    @endif
                </div>
            @empty
                <p style="text-align:center;padding:30px;color:var(--text-muted)">No hay cumpleañeros este mes.</p>
            @endforelse
        </div>
    </div>
@endsection