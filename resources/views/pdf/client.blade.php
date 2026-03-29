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
        .brand-sub { font-size: 10px; color: #666; }
        .client-name { font-size: 18px; font-weight: bold; margin-top: 10px; }
        .client-doc { font-size: 11px; color: #888; }

        .section { margin-bottom: 16px; }
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

        .info-table td { border: none; padding: 5px 10px; width: 50%; }
        .info-table { background: #fafbff; border: 1px solid #e0e5f0; border-radius: 4px; }

        .formula-table th { text-align: center; }
        .formula-table td { text-align: center; font-weight: bold; font-size: 13px; }
        .od { color: #0088aa; }
        .oi { color: #7c5bf5; }

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

        .summary-box {
            background: #f8f9ff;
            border: 2px solid #d0d8f0;
            border-radius: 6px;
            padding: 14px;
            margin-top: 10px;
        }
        .summary-box td { border: none; text-align: center; width: 33%; }
        .summary-val { font-size: 18px; font-weight: bold; }
        .summary-lbl { font-size: 9px; color: #888; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #4a6cf7;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .footer strong { color: #4a6cf7; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="brand">{{ $businessName }}</div>
        <div class="brand-sub">{{ $businessAddress }} — Tel: {{ $businessPhone }}</div>
        <div class="client-name">{{ $client->full_name }}</div>
        <div class="client-doc">{{ $client->document_type }} {{ $client->document_number }}</div>
    </div>

    {{-- DATOS PERSONALES --}}
    <div class="section">
        <div class="section-title">Datos Personales</div>
        <table class="info-table">
            <tr>
                <td><strong>Telefono:</strong> {{ $client->phone ?? 'N/A' }}</td>
                <td><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Direccion:</strong> {{ $client->address ?? 'N/A' }}</td>
                <td><strong>Cumpleanos:</strong> {{ $client->birth_date ? $client->birth_date->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @if($client->notes)
                <tr><td colspan="2"><strong>Notas:</strong> {{ $client->notes }}</td></tr>
            @endif
        </table>
    </div>

    {{-- FORMULA --}}
    @php $formula = $client->formulas->first(); @endphp
    @if($formula)
        <div class="section">
            <div class="section-title">Formula Optica Actual</div>
            <table class="formula-table">
                <thead><tr><th></th><th>Esfera</th><th>Cilindro</th><th>Eje</th><th>ADD</th><th>DNP</th></tr></thead>
                <tbody>
                    <tr>
                        <td class="od" style="font-weight:bold">OD</td>
                        <td class="od">{{ $formula->od_sphere !== null ? number_format($formula->od_sphere, 2) : '-' }}</td>
                        <td class="od">{{ $formula->od_cylinder !== null ? number_format($formula->od_cylinder, 2) : '-' }}</td>
                        <td class="od">{{ $formula->od_axis !== null ? $formula->od_axis . 'g' : '-' }}</td>
                        <td class="od">{{ $formula->od_add !== null ? number_format($formula->od_add, 2) : '-' }}</td>
                        <td class="od">{{ $formula->od_dnp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="oi" style="font-weight:bold">OI</td>
                        <td class="oi">{{ $formula->oi_sphere !== null ? number_format($formula->oi_sphere, 2) : '-' }}</td>
                        <td class="oi">{{ $formula->oi_cylinder !== null ? number_format($formula->oi_cylinder, 2) : '-' }}</td>
                        <td class="oi">{{ $formula->oi_axis !== null ? $formula->oi_axis . 'g' : '-' }}</td>
                        <td class="oi">{{ $formula->oi_add !== null ? number_format($formula->oi_add, 2) : '-' }}</td>
                        <td class="oi">{{ $formula->oi_dnp ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size:10px;color:#888;margin-top:6px">Fecha examen: {{ $formula->exam_date ? $formula->exam_date->format('d/m/Y') : 'N/A' }}</div>
        </div>
    @endif

    {{-- HISTORIAL DE TRABAJOS --}}
    <div class="section">
        <div class="section-title">Historial de Trabajos ({{ $client->works->count() }})</div>
        @if($client->works->count() > 0)
            <table>
                <thead>
                    <tr><th>Codigo</th><th>Fecha</th><th>Lente</th><th>Lab</th><th>Estado</th><th style="text-align:right">Total</th><th style="text-align:right">Abonado</th><th style="text-align:right">Saldo</th></tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; $grandPaid = 0; @endphp
                    @foreach($client->works as $work)
                        @php
                            $paid = $work->payments->sum('amount');
                            $pending = $work->price_total - $paid;
                            $grandTotal += $work->price_total;
                            $grandPaid += $paid;
                            $badgeColor = match($work->status) {
                                'registered' => 'blue', 'sent_to_lab', 'in_process' => 'orange',
                                'received' => 'blue', 'ready' => 'green', 'delivered' => 'green', default => 'gray'
                            };
                        @endphp
                        <tr>
                            <td style="font-weight:bold;color:#4a6cf7">{{ $work->tracking_code }}</td>
                            <td>{{ $work->created_at->format('d/m/Y') }}</td>
                            <td>{{ $work->lens_type_name }}</td>
                            <td>{{ $work->laboratory->name }}</td>
                            <td><span class="badge badge-{{ $badgeColor }}">{{ $work->status_name }}</span></td>
                            <td style="text-align:right;font-weight:bold">${{ number_format($work->price_total, 0, ',', '.') }}</td>
                            <td style="text-align:right;color:#28a745">${{ number_format($paid, 0, ',', '.') }}</td>
                            <td style="text-align:right;color:{{ $pending > 0 ? '#dc3545' : '#28a745' }};font-weight:bold">${{ number_format($pending, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-box">
                <table>
                    <tr>
                        <td>
                            <div class="summary-lbl">Total Compras</div>
                            <div class="summary-val" style="color:#4a6cf7">${{ number_format($grandTotal, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="summary-lbl">Total Pagado</div>
                            <div class="summary-val" style="color:#28a745">${{ number_format($grandPaid, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="summary-lbl">Saldo Total</div>
                            <div class="summary-val" style="color:#dc3545">${{ number_format($grandTotal - $grandPaid, 0, ',', '.') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <p style="color:#888;padding:10px">Este cliente no tiene trabajos registrados.</p>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <strong>{{ $businessName }}</strong> — {{ $businessAddress }} — Tel: {{ $businessPhone }}<br>
        Documento generado el {{ now()->format('d/m/Y h:i A') }}
    </div>

</body>
</html>