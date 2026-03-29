<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 25mm 20mm 20mm 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; line-height: 1.6; }

        .header {
            border: 2px solid #4a6cf7;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 18px;
        }
        .brand { font-size: 22px; font-weight: bold; color: #4a6cf7; }
        .report-title { font-size: 16px; font-weight: bold; margin-top: 6px; }
        .report-dates { font-size: 11px; color: #666; }

        .section { margin-bottom: 18px; }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #fff;
            background: #4a6cf7;
            padding: 6px 12px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f0f3ff;
            color: #4a6cf7;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #d0d8f0;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e8ecf4;
            font-size: 11px;
        }

        .kpi-table { margin-bottom: 18px; }
        .kpi-table td { border: none; width: 25%; text-align: center; padding: 8px 6px; }
        .kpi-box {
            background: #f8f9ff;
            border: 2px solid #d0d8f0;
            border-radius: 6px;
            padding: 16px 10px;
        }
        .kpi-val { font-size: 22px; font-weight: bold; color: #4a6cf7; }
        .kpi-lbl { font-size: 9px; color: #888; margin-top: 4px; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-blue { background: #d6e4ff; color: #1a3e8a; }
        .badge-orange { background: #fff3cd; color: #856404; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-gray { background: #e9ecef; color: #495057; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #4a6cf7;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .footer strong { color: #4a6cf7; }

        .gold { color: #d4a017; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="brand">{{ $businessName }}</div>
        <div class="report-title">Reporte de Estadisticas</div>
        <div class="report-dates">Periodo: {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}</div>
    </div>

    {{-- KPIs --}}
    <table class="kpi-table">
        <tr>
            <td><div class="kpi-box"><div class="kpi-val">${{ number_format($kpis['total_income'], 0, ',', '.') }}</div><div class="kpi-lbl">Ingresos del Periodo</div></div></td>
            <td><div class="kpi-box"><div class="kpi-val">{{ $kpis['clients_served'] }}</div><div class="kpi-lbl">Clientes Atendidos</div></div></td>
            <td><div class="kpi-box"><div class="kpi-val">{{ $kpis['works_created'] }}</div><div class="kpi-lbl">Trabajos Creados</div></div></td>
            <td><div class="kpi-box"><div class="kpi-val">{{ $kpis['works_delivered'] }}</div><div class="kpi-lbl">Trabajos Entregados</div></div></td>
        </tr>
    </table>

    {{-- TOP CLIENTES --}}
    <div class="section">
        <div class="section-title">Top 10 Clientes por Compras</div>
        <table>
            <thead><tr><th style="width:30px;text-align:center">#</th><th>Cliente</th><th>Cedula</th><th style="text-align:center">Trabajos</th><th style="text-align:right">Total Compras</th></tr></thead>
            <tbody>
                @foreach($topClients as $i => $client)
                    <tr>
                        <td style="text-align:center;font-weight:bold;{{ $i == 0 ? 'color:#d4a017' : 'color:#888' }}">{{ $i + 1 }}</td>
                        <td style="font-weight:bold">{{ $client->full_name }}</td>
                        <td>{{ $client->document_number }}</td>
                        <td style="text-align:center;font-weight:bold">{{ $client->works_count }}</td>
                        <td style="text-align:right;font-weight:bold;color:#4a6cf7">${{ number_format($client->total_spent, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- LABORATORIOS --}}
    <div class="section">
        <div class="section-title">Rendimiento de Laboratorios</div>
        <table>
            <thead><tr><th>Laboratorio</th><th style="text-align:center">Trabajos</th><th style="text-align:center">Prom. Entrega</th><th style="text-align:center">Cumplimiento</th></tr></thead>
            <tbody>
                @foreach($laboratories as $lab)
                    <tr>
                        <td style="font-weight:bold">{{ $lab['name'] }}</td>
                        <td style="text-align:center">{{ $lab['total_works'] }}</td>
                        <td style="text-align:center">{{ $lab['avg_days'] }} dias</td>
                        <td style="text-align:center"><span class="badge badge-green">{{ $lab['compliance'] }}%</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TRABAJOS DEL PERIODO --}}
    <div class="section">
        <div class="section-title">Trabajos del Periodo ({{ $works->count() }})</div>
        <table>
            <thead><tr><th>Codigo</th><th>Fecha</th><th>Cliente</th><th>Lente</th><th>Lab</th><th>Estado</th><th style="text-align:right">Total</th></tr></thead>
            <tbody>
                @foreach($works as $work)
                    @php
                        $badgeColor = match($work->status) {
                            'registered' => 'blue', 'sent_to_lab', 'in_process' => 'orange',
                            'received' => 'blue', 'ready' => 'green', 'delivered' => 'green', default => 'gray'
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:bold;color:#4a6cf7">{{ $work->tracking_code }}</td>
                        <td>{{ $work->created_at->format('d/m/Y') }}</td>
                        <td>{{ $work->client->full_name }}</td>
                        <td>{{ $work->lens_type_name }}</td>
                        <td>{{ $work->laboratory->name }}</td>
                        <td><span class="badge badge-{{ $badgeColor }}">{{ $work->status_name }}</span></td>
                        <td style="text-align:right;font-weight:bold">${{ number_format($work->price_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <strong>{{ $businessName }}</strong> — Reporte generado el {{ now()->format('d/m/Y h:i A') }}
    </div>

</body>
</html>