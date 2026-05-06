{{--
    Vista: Detalle de un Laboratorio + control de pagos
    Ruta: GET /laboratorios/{laboratory}
    Controlador: LaboratoryController@show

    Permite ver todos los lentes que el laboratorio nos ha entregado y
    saber cuáles están próximos a cumplir 15 días sin pagar (alarma) y
    cuáles ya superaron los 15 / 30 días (urgentes).
--}}

@extends('layouts.app')
@section('title', $laboratory->name)

@section('styles')
<style>
    .lh-head { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--r-lg); padding: 22px; margin-bottom: 18px; display: flex; align-items: center; gap: 18px; box-shadow: var(--shadow-sm); }
    .lh-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #1a4fd0, #103192); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 30px; flex-shrink: 0; box-shadow: 0 6px 18px rgba(16,49,146,0.25); }
    .lh-name { font-size: 22px; font-weight: 800; color: #103192; }
    .lh-meta { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

    .lh-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 22px; }
    .lh-stat { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--r-lg); padding: 16px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden; cursor: pointer; transition: all .2s ease; }
    .lh-stat::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
    .lh-stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .lh-stat.s-red::before { background: var(--red); }
    .lh-stat.s-orange::before { background: var(--orange); }
    .lh-stat.s-yellow::before { background: var(--yellow); }
    .lh-stat.s-blue::before { background: var(--blue); }
    .lh-stat.s-green::before { background: var(--green); }
    .lh-stat .lbl { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; }
    .lh-stat .val { font-size: 26px; font-weight: 800; font-family: 'JetBrains Mono'; margin-top: 6px; color: var(--text-primary); }
    .lh-stat .sub { font-size: 11px; color: var(--text-secondary); margin-top: 4px; }

    /* Banner de urgencia arriba */
    .lh-banner { padding: 14px 18px; border-radius: var(--r-md); margin-bottom: 18px; display: flex; align-items: center; gap: 12px; font-size: 13.5px; font-weight: 600; }
    .lh-banner.crit { background: linear-gradient(135deg, #fee2e2, #fecaca); border-left: 4px solid var(--red); color: #991b1b; }
    .lh-banner.warn { background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 4px solid var(--yellow); color: #92400e; }
    .lh-banner.ok { background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-left: 4px solid var(--green); color: #166534; }
    .lh-banner span:first-child { font-size: 22px; }

    /* Sección de grupo */
    .lh-group { margin-bottom: 24px; }
    .lh-group-title { font-size: 14px; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: var(--r-md); }
    .lh-group-title.g-red { background: rgba(220,38,38,0.08); color: #991b1b; border: 1px solid rgba(220,38,38,0.2); }
    .lh-group-title.g-orange { background: rgba(234,88,12,0.08); color: #9a3412; border: 1px solid rgba(234,88,12,0.2); }
    .lh-group-title.g-yellow { background: rgba(217,119,6,0.08); color: #92400e; border: 1px solid rgba(217,119,6,0.2); }
    .lh-group-title.g-blue { background: rgba(16,49,146,0.06); color: #103192; border: 1px solid rgba(16,49,146,0.15); }
    .lh-group-title.g-green { background: rgba(22,163,74,0.06); color: #166534; border: 1px solid rgba(22,163,74,0.2); }
    .lh-group-title.g-gray { background: rgba(107,114,128,0.06); color: #4b5563; border: 1px solid rgba(107,114,128,0.15); }
    .lh-group-count { margin-left: auto; background: rgba(0,0,0,0.06); padding: 2px 10px; border-radius: 10px; font-family: 'JetBrains Mono'; font-size: 12px; }

    .lh-empty { text-align: center; padding: 30px; color: var(--text-muted); font-size: 13px; }

    /* Botones de acción dentro de la tabla */
    .pay-btn { padding: 6px 12px; border-radius: 6px; background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; font-family: 'Outfit'; font-size: 11.5px; font-weight: 600; cursor: pointer; transition: all .2s ease; }
    .pay-btn:hover { transform: scale(1.04); box-shadow: 0 3px 10px rgba(22,163,74,0.3); }
    .unpay-btn { padding: 5px 10px; border-radius: 6px; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; font-family: 'Outfit'; font-size: 11px; cursor: pointer; transition: all .2s ease; }
    .unpay-btn:hover { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

    .days-pill { display: inline-block; padding: 3px 10px; border-radius: 10px; font-family: 'JetBrains Mono'; font-size: 11.5px; font-weight: 700; }
    .days-pill.over { background: #fee2e2; color: #991b1b; }
    .days-pill.due  { background: #fef3c7; color: #92400e; }
    .days-pill.soon { background: #ffedd5; color: #9a3412; }
    .days-pill.ok   { background: #f3f4f6; color: #4b5563; }
    .days-pill.paid { background: #dcfce7; color: #166534; }

    @media (max-width: 1200px) { .lh-stats { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px)  { .lh-stats { grid-template-columns: repeat(2, 1fr); } .lh-head { flex-direction: column; text-align: center; } }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>🔬 {{ $laboratory->name }}</h2>
        <div class="ph-acts">
            <a href="{{ route('laboratories.index') }}" class="btn btn-sm btn-s">← Volver a laboratorios</a>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="lh-head">
        <div class="lh-icon">🏭</div>
        <div style="flex:1">
            <div class="lh-name">{{ $laboratory->name }}</div>
            <div class="lh-meta">
                📞 {{ $laboratory->phone ?? '—' }}
                @if($laboratory->contact_name) · 👤 {{ $laboratory->contact_name }} @endif
                · 📍 {{ $laboratory->city ?? '—' }}
                @if($laboratory->address) · {{ $laboratory->address }} @endif
            </div>
        </div>
    </div>

    {{-- BANNER DE URGENCIA --}}
    @if($stats['overdue_count'] > 0)
        <div class="lh-banner crit">
            <span>🔥</span>
            <span>
                ¡Atención! Hay {{ $stats['overdue_count'] }} lente(s) con más de 30 días sin pagar al lab —
                fuera del plazo que da el laboratorio. Total atrasado: <strong>${{ number_format($stats['overdue_amount'], 0, ',', '.') }}</strong>.
            </span>
        </div>
    @elseif($stats['due_count'] > 0)
        <div class="lh-banner warn">
            <span>⏰</span>
            <span>
                {{ $stats['due_count'] }} lente(s) ya pasaron los 15 días. Es momento de coordinar pago al laboratorio.
            </span>
        </div>
    @elseif($stats['due_soon_count'] > 0)
        <div class="lh-banner warn">
            <span>📅</span>
            <span>
                {{ $stats['due_soon_count'] }} lente(s) están próximos a cumplir 15 días. Prepara el pago.
            </span>
        </div>
    @else
        <div class="lh-banner ok">
            <span>✅</span>
            <span>Sin pagos urgentes con este laboratorio.</span>
        </div>
    @endif

    {{-- STATS --}}
    <div class="lh-stats">
        <div class="lh-stat s-red" onclick="lhJump('over')">
            <div class="lbl">🔥 Pasaron 30 días</div>
            <div class="val">{{ $stats['overdue_count'] }}</div>
            <div class="sub">Plazo del lab vencido</div>
        </div>
        <div class="lh-stat s-orange" onclick="lhJump('due')">
            <div class="lbl">⏰ +15 días sin pagar</div>
            <div class="val">{{ $stats['due_count'] }}</div>
            <div class="sub">Pagar lo antes posible</div>
        </div>
        <div class="lh-stat s-yellow" onclick="lhJump('soon')">
            <div class="lbl">📅 Por vencer (12-14 días)</div>
            <div class="val">{{ $stats['due_soon_count'] }}</div>
            <div class="sub">Preparar pago</div>
        </div>
        <div class="lh-stat s-blue">
            <div class="lbl">💰 Total adeudado</div>
            <div class="val">${{ number_format($stats['unpaid_amount'] / 1000, 0, ',', '.') }}K</div>
            <div class="sub">Suma de lentes sin pagar</div>
        </div>
        <div class="lh-stat s-green" onclick="lhJump('paid')">
            <div class="lbl">✅ Ya pagados</div>
            <div class="val">{{ $stats['paid_count'] }}</div>
            <div class="sub">${{ number_format($stats['paid_total_amount'] / 1000, 0, ',', '.') }}K total</div>
        </div>
    </div>

    {{-- GRUPO: OVERDUE (>30 días) --}}
    @if($groups['overdue']->count() > 0)
        <div class="lh-group" id="g-over">
            <div class="lh-group-title g-red">
                <span>🔥</span> URGENTE — Pasaron 30 días, fuera del plazo del lab
                <span class="lh-group-count">{{ $groups['overdue']->count() }}</span>
            </div>
            @include('laboratories._payment_table', ['works' => $groups['overdue'], 'laboratory' => $laboratory, 'pillClass' => 'over'])
        </div>
    @endif

    {{-- GRUPO: DUE (15-29 días) --}}
    @if($groups['due']->count() > 0)
        <div class="lh-group" id="g-due">
            <div class="lh-group-title g-orange">
                <span>⏰</span> Pasaron los 15 días — pagar lo antes posible
                <span class="lh-group-count">{{ $groups['due']->count() }}</span>
            </div>
            @include('laboratories._payment_table', ['works' => $groups['due'], 'laboratory' => $laboratory, 'pillClass' => 'due'])
        </div>
    @endif

    {{-- GRUPO: DUE SOON (12-14 días) --}}
    @if($groups['due_soon']->count() > 0)
        <div class="lh-group" id="g-soon">
            <div class="lh-group-title g-yellow">
                <span>📅</span> Próximos a cumplir 15 días
                <span class="lh-group-count">{{ $groups['due_soon']->count() }}</span>
            </div>
            @include('laboratories._payment_table', ['works' => $groups['due_soon'], 'laboratory' => $laboratory, 'pillClass' => 'soon'])
        </div>
    @endif

    {{-- GRUPO: PENDING (<12 días) --}}
    @if($groups['pending']->count() > 0)
        <div class="lh-group">
            <div class="lh-group-title g-blue">
                <span>📦</span> Recibidos del lab (en plazo cómodo)
                <span class="lh-group-count">{{ $groups['pending']->count() }}</span>
            </div>
            @include('laboratories._payment_table', ['works' => $groups['pending'], 'laboratory' => $laboratory, 'pillClass' => 'ok'])
        </div>
    @endif

    {{-- GRUPO: NOT RECEIVED --}}
    @if($groups['not_received']->count() > 0)
        <div class="lh-group">
            <div class="lh-group-title g-gray">
                <span>🔬</span> Aún no entregados por el lab (sin deuda todavía)
                <span class="lh-group-count">{{ $groups['not_received']->count() }}</span>
            </div>
            @include('laboratories._payment_table', ['works' => $groups['not_received'], 'laboratory' => $laboratory, 'pillClass' => 'ok'])
        </div>
    @endif

    {{-- GRUPO: PAID --}}
    @if($groups['paid']->count() > 0)
        <div class="lh-group" id="g-paid">
            <div class="lh-group-title g-green">
                <span>✅</span> Ya pagados
                <span class="lh-group-count">{{ $groups['paid']->count() }}</span>
            </div>
            @include('laboratories._payment_table', ['works' => $groups['paid'], 'laboratory' => $laboratory, 'pillClass' => 'paid', 'showUnpay' => true])
        </div>
    @endif

    @if($stats['total_works'] === 0)
        <div class="card"><div class="card-b lh-empty">
            Este laboratorio aún no tiene trabajos asignados.
        </div></div>
    @endif
@endsection

@section('scripts')
<script>
function lhJump(key) {
    const el = document.getElementById('g-' + key);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>
@endsection
