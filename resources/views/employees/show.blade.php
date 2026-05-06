{{--
    Vista: Detalle de una Trabajadora
    Ruta: GET /trabajadoras/{employee}   (solo admin)
    Controlador: EmployeeController@show
--}}

@extends('layouts.app')
@section('title', $employee->name)

@section('styles')
<style>
    .ed-head { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--r-lg); padding: 22px; margin-bottom: 18px; display: flex; align-items: center; gap: 18px; box-shadow: var(--shadow-sm); }
    .ed-av { width: 70px; height: 70px; border-radius: 50%; background: var(--grad-blue); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; font-weight: 800; flex-shrink: 0; box-shadow: 0 6px 18px rgba(16,49,146,0.25); }
    .ed-name { font-size: 24px; font-weight: 800; color: #103192; }
    .ed-meta { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    .ed-status { font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin-left: 10px; }
    .ed-status.on { background: #dcfce7; color: #166534; }
    .ed-status.off { background: #f3f4f6; color: #6b7280; }

    .ed-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
    .ed-stat { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--r-lg); padding: 18px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden; }
    .ed-stat::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
    .ed-stat.b1::before { background: var(--blue); }
    .ed-stat.b2::before { background: var(--yellow); }
    .ed-stat.b3::before { background: var(--green); }
    .ed-stat.b4::before { background: var(--purple); }
    .ed-stat .lbl { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; }
    .ed-stat .val { font-size: 26px; font-weight: 800; font-family: 'JetBrains Mono'; margin-top: 6px; color: var(--text-primary); }
    .ed-stat .sub { font-size: 11.5px; color: var(--text-secondary); margin-top: 4px; }

    .ed-tabs { display: flex; gap: 4px; margin-bottom: 14px; border-bottom: 1px solid var(--border); }
    .ed-tab { padding: 10px 18px; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: all .2s ease; background: none; border-top: none; border-left: none; border-right: none; font-family: 'Outfit'; }
    .ed-tab.active { color: #103192; border-bottom-color: #103192; }
    .ed-tab:hover { color: #103192; }

    .ed-pane { display: none; }
    .ed-pane.active { display: block; }

    .ed-empty { text-align: center; padding: 40px 20px; color: var(--text-muted); background: var(--bg-card); border-radius: var(--r-md); border: 1px dashed var(--border); }

    @media (max-width: 1200px) { .ed-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .ed-stats { grid-template-columns: 1fr; } .ed-head { flex-direction: column; text-align: center; } }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>👩‍💼 Perfil de Trabajadora</h2>
        <div class="ph-acts">
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-s">← Volver a trabajadoras</a>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="ed-head">
        <div class="ed-av">{{ $employee->initials ?: '👤' }}</div>
        <div style="flex:1">
            <div style="display:flex;align-items:center;flex-wrap:wrap">
                <span class="ed-name">{{ $employee->name }}</span>
                <span class="ed-status {{ $employee->is_active ? 'on' : 'off' }}">
                    {{ $employee->is_active ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
            <div class="ed-meta">📞 {{ $employee->phone ?? 'Sin teléfono' }} · Trabajadora desde {{ $employee->created_at->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="ed-stats">
        <div class="ed-stat b1">
            <div class="lbl">📋 Trabajos atendidos</div>
            <div class="val">{{ $stats['works_total'] }}</div>
            <div class="sub">{{ $stats['works_this_month'] }} este mes · {{ $stats['works_active'] }} en curso</div>
        </div>
        <div class="ed-stat b2">
            <div class="lbl">💰 Valor facturado total</div>
            <div class="val">${{ number_format($stats['works_value_total'] / 1000, 0, ',', '.') }}K</div>
            <div class="sub">${{ number_format($stats['works_value_this_month'] / 1000, 0, ',', '.') }}K este mes</div>
        </div>
        <div class="ed-stat b3">
            <div class="lbl">✅ Pagos recibidos</div>
            <div class="val">{{ $stats['payments_total_count'] }}</div>
            <div class="sub">${{ number_format($stats['payments_total_amount'] / 1000, 0, ',', '.') }}K total cobrado</div>
        </div>
        <div class="ed-stat b4">
            <div class="lbl">📅 Cobrado este mes</div>
            <div class="val">${{ number_format($stats['payments_this_month_amount'] / 1000, 0, ',', '.') }}K</div>
            <div class="sub">{{ $stats['works_delivered'] }} entregados · {{ $stats['works_cancelled'] }} cancelados</div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="ed-tabs">
        <button type="button" class="ed-tab active" data-tab="works" onclick="edSwitchTab(this)">📋 Trabajos atendidos ({{ $works->count() }})</button>
        <button type="button" class="ed-tab" data-tab="payments" onclick="edSwitchTab(this)">💰 Pagos recibidos ({{ $payments->count() }})</button>
    </div>

    {{-- PANE: TRABAJOS --}}
    <div class="ed-pane active" id="ed-works">
        @if($works->isEmpty())
            <div class="ed-empty">{{ $employee->name }} aún no tiene trabajos registrados a su nombre.</div>
        @else
            <div class="card">
                <div class="card-b" style="padding:0">
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Laboratorio</th>
                                    <th style="text-align:right">Total</th>
                                    <th style="text-align:right">Saldo</th>
                                    <th style="text-align:center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($works as $w)
                                    <tr>
                                        <td style="font-family:'JetBrains Mono';font-weight:700;color:#103192">{{ $w->tracking_code }}</td>
                                        <td>{{ $w->client->full_name }}</td>
                                        <td style="font-size:12px;color:var(--text-secondary)">{{ $w->created_at->format('d/m/Y') }}</td>
                                        <td><span class="badge badge-{{ $w->status_color }}">{{ $w->status_emoji }} {{ $w->status_name }}</span></td>
                                        <td style="font-size:12px">{{ $w->laboratory->name }}</td>
                                        <td style="text-align:right;font-weight:700">${{ number_format($w->price_total, 0, ',', '.') }}</td>
                                        <td style="text-align:right;color:{{ $w->pending_balance > 0 ? 'var(--red)' : 'var(--green)' }};font-weight:600">
                                            ${{ number_format($w->pending_balance, 0, ',', '.') }}
                                        </td>
                                        <td style="text-align:center">
                                            <a href="{{ route('works.show', $w) }}" class="btn btn-xs btn-s">Ver →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- PANE: PAGOS --}}
    <div class="ed-pane" id="ed-payments">
        @if($payments->isEmpty())
            <div class="ed-empty">{{ $employee->name }} aún no ha recibido pagos registrados.</div>
        @else
            <div class="card">
                <div class="card-b" style="padding:0">
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Trabajo</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th style="text-align:right">Monto</th>
                                    <th>Nota</th>
                                    <th style="text-align:center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $p)
                                    <tr>
                                        <td style="font-size:12px;color:var(--text-secondary)">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="font-family:'JetBrains Mono';font-weight:700;color:#103192">{{ $p->work->tracking_code }}</td>
                                        <td>{{ $p->work->client->full_name }}</td>
                                        <td style="font-size:12px">{{ $p->method_name }}</td>
                                        <td style="text-align:right;color:var(--green);font-weight:700">${{ number_format($p->amount, 0, ',', '.') }}</td>
                                        <td style="font-size:12px;color:var(--text-muted)">{{ $p->notes ?? '—' }}</td>
                                        <td style="text-align:center">
                                            <a href="{{ route('works.show', $p->work) }}" class="btn btn-xs btn-s">Ver trabajo →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
function edSwitchTab(btn) {
    document.querySelectorAll('.ed-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.ed-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('ed-' + btn.dataset.tab).classList.add('active');
}
</script>
@endsection
