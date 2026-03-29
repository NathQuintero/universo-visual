<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 25mm 20mm 20mm 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; line-height: 1.6; }

        /* Header con borde */
        .header {
            border: 2px solid #4a6cf7;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 18px;
        }
        .header-inner { width: 100%; }
        .header-inner td { border: none; vertical-align: top; }
        .brand { font-size: 22px; font-weight: bold; color: #4a6cf7; }
        .brand-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .tracking-label { font-size: 9px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
        .tracking-code { font-size: 20px; font-weight: bold; color: #4a6cf7; letter-spacing: 1px; }

        /* Badge de estado */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 4px;
        }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-blue { background: #d6e4ff; color: #1a3e8a; }
        .badge-orange { background: #fff3cd; color: #856404; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-gray { background: #e9ecef; color: #495057; }

        /* Secciones */
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

        /* Tablas */
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

        /* Tabla info (sin bordes visibles) */
        .info-table td {
            border: none;
            padding: 5px 10px;
            width: 50%;
        }
        .info-table { background: #fafbff; border: 1px solid #e0e5f0; border-radius: 4px; }

        /* Formula */
        .formula-table th { text-align: center; }
        .formula-table td { text-align: center; font-weight: bold; font-size: 13px; }
        .od { color: #0088aa; }
        .oi { color: #7c5bf5; }

        /* Caja de totales */
        .total-box {
            border: 2px solid #4a6cf7;
            border-radius: 6px;
            padding: 14px 16px;
            margin: 12px 0;
            background: #f8f9ff;
        }
        .total-table td { border: none; padding: 5px 10px; font-size: 12px; }
        .total-label { color: #555; }
        .total-value { text-align: right; font-weight: bold; }
        .total-main td { font-size: 16px; font-weight: bold; color: #4a6cf7; border-top: 2px solid #4a6cf7; padding-top: 10px; }
        .paid { color: #28a745; }
        .pending { color: #dc3545; }
        .saldo-row td { font-size: 14px; font-weight: bold; }

        /* Seguimiento */
        .seguimiento-box {
            border: 2px dashed #4a6cf7;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin: 16px 0;
            background: #f8f9ff;
        }
        .seguimiento-code { font-size: 24px; font-weight: bold; color: #4a6cf7; letter-spacing: 3px; margin: 6px 0; }
        .seguimiento-url { font-size: 10px; color: #888; }

        /* Footer */
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

    {{-- ==================== HEADER ==================== --}}
    <div class="header">
        <table class="header-inner">
            <tr>
                <td style="width:55%;border:none">
                    <div class="brand">{{ $businessName }}</div>
                    <div class="brand-sub">{{ $businessAddress }}</div>
                    <div class="brand-sub">Tel: {{ $businessPhone }}</div>
                </td>
                <td style="width:45%;text-align:right;border:none">
                    <div class="tracking-label">Codigo de Seguimiento</div>
                    <div class="tracking-code">{{ $work->tracking_code }}</div>
                    @php
                        $badgeColor = match($work->status) {
                            'registered' => 'blue', 'sent_to_lab' => 'orange', 'in_process' => 'orange',
                            'received' => 'blue', 'ready' => 'green', 'delivered' => 'green', default => 'gray'
                        };
                    @endphp
                    <div class="status-badge badge-{{ $badgeColor }}">{{ $work->status_name }}</div>
                    <div style="font-size:10px;color:#999;margin-top:4px">{{ $work->created_at->format('d/m/Y h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ==================== DATOS DEL CLIENTE ==================== --}}
    <div class="section">
        <div class="section-title">Datos del Cliente</div>
        <table class="info-table">
            <tr>
                <td><strong>Nombre:</strong> {{ $work->client->full_name }}</td>
                <td><strong>{{ $work->client->document_type }}:</strong> {{ $work->client->document_number }}</td>
            </tr>
            <tr>
                <td><strong>Telefono:</strong> {{ $work->client->phone ?? 'N/A' }}</td>
                <td><strong>Email:</strong> {{ $work->client->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- ==================== FORMULA OPTICA ==================== --}}
    <div class="section">
        <div class="section-title">Formula Optica</div>
        <table class="formula-table">
            <thead>
                <tr><th></th><th>Esfera</th><th>Cilindro</th><th>Eje</th><th>ADD</th><th>DNP</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td class="od" style="font-weight:bold">OD</td>
                    <td class="od">{{ $work->formula->od_sphere !== null ? number_format($work->formula->od_sphere, 2) : '-' }}</td>
                    <td class="od">{{ $work->formula->od_cylinder !== null ? number_format($work->formula->od_cylinder, 2) : '-' }}</td>
                    <td class="od">{{ $work->formula->od_axis !== null ? $work->formula->od_axis . 'g' : '-' }}</td>
                    <td class="od">{{ $work->formula->od_add !== null ? number_format($work->formula->od_add, 2) : '-' }}</td>
                    <td class="od">{{ $work->formula->od_dnp ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="oi" style="font-weight:bold">OI</td>
                    <td class="oi">{{ $work->formula->oi_sphere !== null ? number_format($work->formula->oi_sphere, 2) : '-' }}</td>
                    <td class="oi">{{ $work->formula->oi_cylinder !== null ? number_format($work->formula->oi_cylinder, 2) : '-' }}</td>
                    <td class="oi">{{ $work->formula->oi_axis !== null ? $work->formula->oi_axis . 'g' : '-' }}</td>
                    <td class="oi">{{ $work->formula->oi_add !== null ? number_format($work->formula->oi_add, 2) : '-' }}</td>
                    <td class="oi">{{ $work->formula->oi_dnp ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
        @if($work->formula->exam_date)
            <div style="font-size:10px;color:#888;margin-top:6px;padding-left:4px">Fecha examen: {{ $work->formula->exam_date->format('d/m/Y') }}</div>
        @endif
    </div>

    {{-- ==================== ESPECIFICACIONES ==================== --}}
    <div class="section">
        <div class="section-title">Especificaciones del Trabajo</div>
        <table class="info-table">
            <tr>
                <td><strong>Montura:</strong> {{ $work->frame_type == 'own' ? 'Propia' : 'Comprada' }}{{ $work->frame_brand ? ' - ' . $work->frame_brand : '' }}{{ $work->frame_reference ? ' ' . $work->frame_reference : '' }}</td>
                <td><strong>Tipo Lente:</strong> {{ $work->lens_type_name }}</td>
            </tr>
            <tr>
                <td><strong>Material:</strong> {{ $work->lens_material_name }}</td>
                <td><strong>Laboratorio:</strong> {{ $work->laboratory->name }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Tratamientos:</strong> {{ $work->treatments_text }}</td>
            </tr>
            @if($work->observations)
                <tr>
                    <td colspan="2"><strong>Observaciones:</strong> {{ $work->observations }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- ==================== PRECIOS Y PAGOS ==================== --}}
    <div class="section">
        <div class="section-title">Detalle de Precios y Pagos</div>
        <div class="total-box">
            <table class="total-table">
                <tr>
                    <td class="total-label">Lentes</td>
                    <td class="total-value">${{ number_format($work->price_lenses, 0, ',', '.') }}</td>
                </tr>
                @if($work->price_frame > 0)
                <tr>
                    <td class="total-label">Montura</td>
                    <td class="total-value">${{ number_format($work->price_frame, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($work->price_consultation > 0)
                <tr>
                    <td class="total-label">Consulta</td>
                    <td class="total-value">${{ number_format($work->price_consultation, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-main">
                    <td>TOTAL</td>
                    <td style="text-align:right">${{ number_format($work->price_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="total-label">Total Abonado</td>
                    <td class="total-value paid">${{ number_format($work->total_paid, 0, ',', '.') }}</td>
                </tr>
                <tr class="saldo-row">
                    <td>Saldo Pendiente</td>
                    <td class="total-value pending">${{ number_format($work->pending_balance, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if($work->payments->count() > 0)
            <div style="font-size:11px;font-weight:bold;color:#4a6cf7;margin:10px 0 6px">Historial de Abonos</div>
            <table>
                <thead><tr><th>Fecha</th><th>Monto</th><th>Metodo</th><th>Registro</th><th>Nota</th></tr></thead>
                <tbody>
                    @foreach($work->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d/m/Y h:i A') }}</td>
                            <td style="font-weight:bold;color:#28a745">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>{{ $payment->method_name }}</td>
                            <td>{{ $payment->user->name }}</td>
                            <td style="color:#888">{{ $payment->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ==================== SEGUIMIENTO ==================== --}}
    <div class="seguimiento-box">
        <div style="font-size:11px;color:#888">Consulta el estado de tu pedido en:</div>
        <div class="seguimiento-code">{{ $work->tracking_code }}</div>
        <div class="seguimiento-url">{{ route('tracking', $work->tracking_code) }}</div>
    </div>

    {{-- ==================== FOOTER ==================== --}}
    <div class="footer">
        <strong>{{ $businessName }}</strong> — {{ $businessAddress }} — Tel: {{ $businessPhone }}<br>
        Documento generado el {{ now()->format('d/m/Y h:i A') }}
    </div>

</body>
</html>