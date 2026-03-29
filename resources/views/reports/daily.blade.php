{{--
    Vista: Resumen Diario
    Ruta: GET /resumen-diario
    Controlador: ReportController@dailySummary
--}}

@extends('layouts.app')
@section('title', 'Resumen Diario')

@section('content')
    <div class="ph"><h2>📋 Resumen del Día</h2></div>

    <p style="text-align:center;font-size:13px;color:var(--text-secondary);margin-bottom:18px">
        📅 {{ now()->translatedFormat('l, j \\d\\e F \\d\\e Y') }}
    </p>

    {{-- Tarjetas resumen --}}
    <div class="stats" style="grid-template-columns:repeat(3,1fr);margin-bottom:22px">
        <div class="stat s-blue" style="text-align:center;padding:22px">
            <div style="font-size:26px;margin-bottom:6px">📋</div>
            <div class="stat-val">{{ $summary['works_created'] }}</div>
            <div class="stat-label">Ingresados hoy</div>
        </div>
        <div class="stat s-green" style="text-align:center;padding:22px">
            <div style="font-size:26px;margin-bottom:6px">✅</div>
            <div class="stat-val">{{ $summary['works_delivered'] }}</div>
            <div class="stat-label">Entregados hoy</div>
        </div>
        <div class="stat s-purple" style="text-align:center;padding:22px">
            <div style="font-size:26px;margin-bottom:6px">💰</div>
            <div class="stat-val">${{ number_format($summary['today_income'] / 1000, 0, ',', '.') }}K</div>
            <div class="stat-label">Ingresos del día</div>
        </div>
    </div>

    {{-- Pendientes por resolver --}}
    <div class="card">
        <div class="card-h"><h3>⏰ Pendientes por Resolver</h3></div>
        <div class="card-b">
            @forelse($pendingAlerts as $alert)
                <div class="wi">
                    <div class="dot {{ $alert['type'] == 'delayed' ? 'dot-red' : ($alert['type'] == 'pickup' ? 'dot-green' : 'dot-yellow') }}"></div>
                    <div class="wi-info">
                        <h4>{{ $alert['work']->client->full_name }} — {{ $alert['message'] }}</h4>
                        <p>{{ $alert['work']->tracking_code }}</p>
                    </div>
                    @if($alert['type'] == 'pickup' && $alert['work']->client->phone && $alert['work']->client->whatsapp_authorized)
                        <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $alert['work']->client->phone) }}?text={{ urlencode('¡Hola ' . $alert['work']->client->first_name . '! Te recordamos que tus gafas están listas para recoger en Óptica Universo Visual. ¡Te esperamos! 😊') }}"
                           target="_blank" class="btn btn-sm btn-g">💬 Avisar</a>
                    @elseif($alert['type'] == 'payment' && $alert['work']->client->phone && $alert['work']->client->whatsapp_authorized)
                        <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $alert['work']->client->phone) }}?text={{ urlencode('Hola ' . $alert['work']->client->first_name . ', te recordamos que tienes un saldo pendiente de $' . number_format($alert['work']->pending_balance, 0, ',', '.') . ' en tu pedido ' . $alert['work']->tracking_code . '. ¡Quedo atenta!') }}"
                           target="_blank" class="btn btn-sm btn-g">💰 Cobrar</a>
                    @else
                        <a href="{{ route('works.show', $alert['work']) }}" class="btn btn-sm btn-s">Ver →</a>
                    @endif
                </div>
            @empty
                <p style="text-align:center;padding:24px;color:var(--text-muted)">✨ ¡Todo resuelto! Sin pendientes por hoy.</p>
            @endforelse
        </div>
    </div>
@endsection