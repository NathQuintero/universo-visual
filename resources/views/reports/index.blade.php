{{--
    Vista: Reportes y Estadísticas
    Ruta: GET /reportes
    Controlador: ReportController@index
    Solo accesible por admin.
--}}

@extends('layouts.app')
@section('title', 'Reportes')

@section('styles')
<style>
    .r-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
    .rkpi { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 20px; }
    .rkpi .icon { font-size: 26px; margin-bottom: 8px; }
    .rkpi .val { font-size: 24px; font-weight: 800; font-family: 'JetBrains Mono'; }
    .rkpi .lbl { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
    .charts { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 22px; }
    .chart-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 20px; }
    .chart-card h3 { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
    .bar-chart { display: flex; align-items: flex-end; gap: 8px; height: 130px; padding-top: 10px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .bar { border-radius: 3px 3px 0 0; width: 100%; min-height: 4px; }
    .bar-label { font-size: 9px; color: var(--text-muted); font-family: 'JetBrains Mono'; }
    .bar-val { font-size: 10px; color: var(--text-secondary); font-weight: 600; font-family: 'JetBrains Mono'; }
    @media (max-width: 1200px) { .r-kpis { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .charts { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>📊 Reportes y Estadísticas</h2>
    </div>

    {{-- Filtros de fecha --}}
    <form action="{{ route('reports.index') }}" method="GET" class="filters-row">
        <input type="date" name="start_date" class="combo" value="{{ $startDate->format('Y-m-d') }}">
        <span style="color:var(--text-muted)">→</span>
        <input type="date" name="end_date" class="combo" value="{{ $endDate->format('Y-m-d') }}">
        <select name="laboratory_id" class="combo">
            <option value="">Todos los laboratorios</option>
            @foreach($allLaboratories as $lab)
                <option value="{{ $lab->id }}" {{ request('laboratory_id') == $lab->id ? 'selected' : '' }}>{{ $lab->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-p">🔍 Generar Reporte</button>
        <a href="{{ route('pdf.report', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn btn-v">📄 Descargar PDF</a>
    </form>

    {{-- KPIs --}}
    <div class="r-kpis">
        <div class="rkpi">
            <div class="icon">💰</div>
            <div class="val" style="color:var(--blue)">${{ number_format($kpis['total_income'] / 1000, 0, ',', '.') }}K</div>
            <div class="lbl">Ingresos del Período</div>
        </div>
        <div class="rkpi">
            <div class="icon">👥</div>
            <div class="val" style="color:var(--purple)">{{ $kpis['clients_served'] }}</div>
            <div class="lbl">Clientes Atendidos</div>
        </div>
        <div class="rkpi">
            <div class="icon">🎯</div>
            <div class="val" style="color:var(--cyan)">${{ number_format($kpis['avg_ticket'] / 1000, 0, ',', '.') }}K</div>
            <div class="lbl">Ticket Promedio</div>
        </div>
        <div class="rkpi">
            <div class="icon">⚡</div>
            <div class="val" style="color:var(--green)">{{ number_format($kpis['avg_delivery_days'], 1) }}d</div>
            <div class="lbl">Tiempo Prom. Entrega</div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="charts">
        {{-- Ingresos mensuales --}}
        <div class="chart-card">
            <h3>📈 Ingresos Mensuales</h3>
            @php $maxIncome = max(array_column($monthlyIncome, 'amount')) ?: 1; @endphp
            <div class="bar-chart">
                @foreach($monthlyIncome as $month)
                    @php $height = ($month['amount'] / $maxIncome) * 100; @endphp
                    <div class="bar-col">
                        <div class="bar-val">${{ number_format($month['amount'] / 1000, 0) }}K</div>
                        <div class="bar" style="height:{{ max($height, 4) }}%;background:var(--grad-blue);{{ $loop->last ? 'box-shadow:0 0 15px rgba(74,108,247,0.25)' : 'opacity:0.6' }}"></div>
                        <div class="bar-label">{{ $month['month'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Distribución por laboratorio --}}
        <div class="chart-card">
            <h3>🥧 Distribución por Laboratorio</h3>
            @php
                $totalLab = $labDistribution->sum('count') ?: 1;
                $colors = ['var(--blue)', 'var(--purple)', 'var(--cyan)', 'var(--yellow)'];
                $percents = $labDistribution->map(fn($l) => round(($l['count'] / $totalLab) * 100));
                $conicParts = [];
                $acc = 0;
                foreach ($labDistribution as $i => $lab) {
                    $pct = round(($lab['count'] / $totalLab) * 100);
                    $conicParts[] = ($colors[$i] ?? 'var(--text-muted)') . ' ' . $acc . '% ' . ($acc + $pct) . '%';
                    $acc += $pct;
                }
                $conicGradient = implode(', ', $conicParts);
            @endphp
            <div style="display:flex;gap:18px;align-items:center;margin-top:10px">
                <div style="width:110px;height:110px;border-radius:50%;background:conic-gradient({{ $conicGradient }});flex-shrink:0"></div>
                <div style="flex:1">
                    @foreach($labDistribution as $i => $lab)
                        <div style="display:flex;align-items:center;justify-content:space-between;margin:6px 0;font-size:12px;color:var(--text-secondary)">
                            <div style="display:flex;align-items:center;gap:6px">
                                <span style="width:9px;height:9px;background:{{ $colors[$i] ?? 'var(--text-muted)' }};border-radius:50%;display:inline-block"></span>
                                {{ $lab['name'] }}
                            </div>
                            <strong>{{ round(($lab['count'] / $totalLab) * 100) }}%</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas: Top clientes y Rendimiento labs --}}
    <div class="charts">
        <div class="chart-card">
            <h3>🏆 Top 5 Clientes</h3>
            <table class="tbl">
                <thead><tr><th>#</th><th>Cliente</th><th>Trabajos</th><th>Total Compras</th></tr></thead>
                <tbody>
                    @foreach($topClients as $i => $client)
                        <tr>
                            <td style="color:{{ $i == 0 ? 'var(--yellow)' : 'var(--text-muted)' }};font-weight:700">{{ $i + 1 }}</td>
                            <td>{{ $client->full_name }}</td>
                            <td style="font-family:'JetBrains Mono';font-weight:700;text-align:center">{{ $client->works_count }}</td>
                            <td style="font-family:'JetBrains Mono'">${{ number_format($client->total_spent, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="chart-card">
            <h3>🏭 Rendimiento de Laboratorios</h3>
            <table class="tbl">
                <thead><tr><th>Laboratorio</th><th>Trabajos</th><th>Prom. Entrega</th><th>Cumplimiento</th></tr></thead>
                <tbody>
                    @foreach($laboratories as $lab)
                        <tr>
                            <td>{{ $lab['name'] }}</td>
                            <td style="font-family:'JetBrains Mono';font-weight:700;text-align:center">{{ $lab['total_works'] }}</td>
                            <td style="font-family:'JetBrains Mono';color:var(--green)">{{ $lab['avg_days'] }} días</td>
                            <td><span class="badge badge-green">{{ $lab['compliance'] }}%</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection