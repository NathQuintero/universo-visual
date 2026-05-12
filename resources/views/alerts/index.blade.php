{{--
    Vista: Lista completa de alertas
    Ruta: GET /alertas
    Controlador: DashboardController@alerts

    Muestra todas las alertas activas del sistema con botones de acción
    (WhatsApp, ver trabajo, etc.) y detalle expandible al hacer clic.
--}}

@extends('layouts.app')
@section('title', 'Alertas')

@section('styles')
<style>
    .al-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 18px; flex-wrap: wrap; }
    .al-head h2 { font-size: 22px; color: #103192; font-weight: 800; }
    .al-head p { color: var(--text-muted); font-size: 13px; margin-top: 4px; }

    /* Chips de conteo por severidad */
    .al-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
    .al-chip { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--r-md); padding: 12px 16px; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow-sm); min-width: 140px; }
    .al-chip .n { font-family: 'JetBrains Mono'; font-size: 20px; font-weight: 800; }
    .al-chip .l { font-size: 11.5px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .al-chip.danger { border-left: 4px solid var(--red); }
    .al-chip.danger .n { color: var(--red); }
    .al-chip.warning { border-left: 4px solid var(--yellow); }
    .al-chip.warning .n { color: var(--yellow); }
    .al-chip.success { border-left: 4px solid var(--green); }
    .al-chip.success .n { color: var(--green); }
    .al-chip.info { border-left: 4px solid var(--blue); }
    .al-chip.info .n { color: var(--blue); }

    /* Lista de tarjetas de alertas */
    .al-list { display: flex; flex-direction: column; gap: 12px; }

    .al-card {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-left: 4px solid var(--blue);
        border-radius: var(--r-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: border-color .2s ease, transform .15s ease, box-shadow .2s ease;
    }
    .al-card:hover { box-shadow: var(--shadow); }
    .al-card.danger { border-left-color: var(--red); }
    .al-card.warning { border-left-color: var(--yellow); }
    .al-card.success { border-left-color: var(--green); }
    .al-card.info { border-left-color: var(--blue); }

    .al-card-top { display: flex; align-items: flex-start; gap: 14px; padding: 16px 18px; cursor: pointer; user-select: none; }

    .al-icon {
        width: 42px; height: 42px;
        border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 19px; flex-shrink: 0;
    }
    .al-icon.danger  { background: linear-gradient(135deg, #fee2e2, #fecaca); }
    .al-icon.warning { background: linear-gradient(135deg, #fef3c7, #fde68a); }
    .al-icon.success { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
    .al-icon.info    { background: linear-gradient(135deg, #e8edff, #c7d2fe); }

    .al-text { flex: 1; min-width: 0; }
    .al-text h4 { font-size: 14.5px; font-weight: 700; color: var(--text-primary); }
    .al-text p { font-size: 12.5px; color: var(--text-secondary); margin-top: 3px; }

    .al-chev { font-size: 14px; color: var(--text-muted); transition: transform .25s ease; }
    .al-card.open .al-chev { transform: rotate(180deg); }

    /* Panel desplegable de detalle */
    .al-panel {
        display: none;
        padding: 0 18px 16px 74px;
        animation: alSlide .25s ease;
    }
    .al-card.open .al-panel { display: block; }
    @keyframes alSlide { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

    .al-detail-list {
        background: var(--bg-deep);
        border-radius: var(--r);
        padding: 12px 14px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px 18px;
        margin-bottom: 12px;
    }
    .al-detail-list div { font-size: 12.5px; color: var(--text-secondary); padding: 4px 0; border-bottom: 1px dashed #d6dce8; }
    .al-detail-list div:last-child, .al-detail-list div:nth-last-child(2) { border-bottom: none; }
    .al-detail-list strong { color: var(--text-primary); margin-right: 6px; font-weight: 600; }

    /* Botones de acción */
    .al-acts { display: flex; gap: 8px; flex-wrap: wrap; }
    .al-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px;
        border-radius: var(--r);
        font-family: 'Outfit';
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .2s ease;
    }
    .al-btn.primary { background: var(--grad-blue); color: #fff; }
    .al-btn.primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(16,49,146,0.25); }
    .al-btn.whatsapp { background: linear-gradient(135deg, #25d366, #128c7e); color: #fff; }
    .al-btn.whatsapp:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,211,102,0.35); }

    .al-empty {
        background: var(--bg-card);
        border: 1px dashed var(--border);
        border-radius: var(--r-lg);
        padding: 48px 24px;
        text-align: center;
        color: var(--text-muted);
    }
    .al-empty .em { font-size: 40px; margin-bottom: 10px; }
    .al-empty h3 { color: #103192; font-size: 18px; font-weight: 700; margin-bottom: 4px; }

    @media (max-width: 700px) {
        .al-detail-list { grid-template-columns: 1fr; }
        .al-panel { padding-left: 18px; }
    }
</style>
@endsection

@section('content')
    <div class="al-head">
        <div>
            <h2>🔔 Alertas</h2>
            <p>Lista completa de avisos pendientes. Haz clic en cualquier alerta para ver el detalle y las acciones disponibles.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-s">← Volver al inicio</a>
    </div>

    {{-- Chips resumen por severidad --}}
    <div class="al-chips">
        <div class="al-chip danger">
            <span class="n">{{ $groups['danger']->count() }}</span>
            <span class="l">Críticas</span>
        </div>
        <div class="al-chip warning">
            <span class="n">{{ $groups['warning']->count() }}</span>
            <span class="l">Atención</span>
        </div>
        <div class="al-chip success">
            <span class="n">{{ $groups['success']->count() }}</span>
            <span class="l">Para avisar</span>
        </div>
        @if($groups['info']->count() > 0)
        <div class="al-chip info">
            <span class="n">{{ $groups['info']->count() }}</span>
            <span class="l">Info</span>
        </div>
        @endif
    </div>

    @if(empty($alerts))
        <div class="al-empty">
            <div class="em">✨</div>
            <h3>¡Todo al día!</h3>
            <p>No hay alertas pendientes en este momento.</p>
        </div>
    @else
        <div class="al-list">
            @foreach($alerts as $alert)
                <div class="al-card {{ $alert['type'] }}" id="al-{{ $alert['id'] }}">
                    <div class="al-card-top" onclick="alToggle('{{ $alert['id'] }}')">
                        <div class="al-icon {{ $alert['type'] }}">{{ $alert['icon'] }}</div>
                        <div class="al-text">
                            <h4>{{ $alert['title'] }}</h4>
                            <p>{{ $alert['message'] }}</p>
                        </div>
                        <span class="al-chev">▼</span>
                    </div>

                    <div class="al-panel">
                        @if(!empty($alert['details']))
                            <div class="al-detail-list">
                                @foreach($alert['details'] as $label => $value)
                                    <div><strong>{{ $label }}:</strong> {{ $value }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($alert['actions']))
                            <div class="al-acts">
                                @foreach($alert['actions'] as $act)
                                    <a href="{{ $act['url'] }}"
                                       class="al-btn {{ $act['style'] ?? 'primary' }}"
                                       @if(!empty($act['target'])) target="{{ $act['target'] }}" rel="noopener" @endif
                                       onclick="event.stopPropagation()">
                                        <span>{{ $act['icon'] ?? '' }}</span>
                                        <span>{{ $act['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
<script>
    function alToggle(id) {
        const card = document.getElementById('al-' + id);
        if (card) card.classList.toggle('open');
    }
</script>
@endsection
