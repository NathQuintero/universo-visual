<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f5;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(16,49,146,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#103192,#1a4fd0);padding:32px 40px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:800;letter-spacing:-0.5px;">
                                Óptica Universo Visual
                            </h1>
                            <p style="margin:6px 0 0;color:rgba(255,255,255,0.8);font-size:13px;">
                                Tu recibo de pedido
                            </p>
                        </td>
                    </tr>

                    {{-- Saludo --}}
                    <tr>
                        <td style="padding:32px 40px 0;">
                            <p style="margin:0 0 8px;font-size:18px;font-weight:700;color:#1a1a2e;">
                                Hola {{ $work->client->first_name }},
                            </p>
                            <p style="margin:0;font-size:14px;color:#6c757d;line-height:1.6;">
                                Aquí tienes el recibo de tu pedido con código de seguimiento
                                <strong style="color:#103192;">{{ $work->tracking_code }}</strong>.
                                Encontrarás el PDF adjunto en este correo.
                            </p>
                        </td>
                    </tr>

                    {{-- Resumen del pedido --}}
                    <tr>
                        <td style="padding:24px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fc;border-radius:10px;border:1px solid #e8edff;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <p style="margin:0 0 14px;font-size:13px;font-weight:700;color:#103192;text-transform:uppercase;letter-spacing:1px;">
                                            Resumen del pedido
                                        </p>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:13px;color:#6c757d;">Tipo de lente</td>
                                                <td style="padding:6px 0;font-size:13px;color:#1a1a2e;font-weight:600;text-align:right;">{{ $work->lens_type_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:13px;color:#6c757d;">Montura</td>
                                                <td style="padding:6px 0;font-size:13px;color:#1a1a2e;font-weight:600;text-align:right;">{{ $work->frame_type == 'own' ? 'Propia' : 'Comprada' }}{{ $work->frame_brand ? ' — ' . $work->frame_brand : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:13px;color:#6c757d;">Laboratorio</td>
                                                <td style="padding:6px 0;font-size:13px;color:#1a1a2e;font-weight:600;text-align:right;">{{ $work->laboratory->name }}</td>
                                            </tr>
                                            @if($work->treatments_text)
                                            <tr>
                                                <td style="padding:6px 0;font-size:13px;color:#6c757d;">Tratamientos</td>
                                                <td style="padding:6px 0;font-size:13px;color:#1a1a2e;font-weight:600;text-align:right;">{{ $work->treatments_text }}</td>
                                            </tr>
                                            @endif
                                        </table>

                                        {{-- Línea separadora --}}
                                        <hr style="border:none;border-top:1px solid #e0e0e0;margin:14px 0;">

                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;font-weight:700;color:#1a1a2e;">Total</td>
                                                <td style="padding:6px 0;font-size:14px;font-weight:700;color:#103192;text-align:right;">${{ number_format($work->price_total, 0, ',', '.') }}</td>
                                            </tr>
                                            @if($work->pending_balance > 0)
                                            <tr>
                                                <td style="padding:6px 0;font-size:13px;color:#dc2626;font-weight:600;">Saldo pendiente</td>
                                                <td style="padding:6px 0;font-size:13px;color:#dc2626;font-weight:700;text-align:right;">${{ number_format($work->pending_balance, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Botón de seguimiento --}}
                    <tr>
                        <td style="padding:28px 40px 0;text-align:center;">
                            <a href="{{ $trackingUrl }}" target="_blank"
                               style="display:inline-block;background:#103192;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:10px;font-size:14px;font-weight:700;">
                                Consultar estado de mi pedido
                            </a>
                        </td>
                    </tr>

                    {{-- Contacto --}}
                    <tr>
                        <td style="padding:28px 40px 0;">
                            <p style="margin:0;font-size:13px;color:#6c757d;text-align:center;line-height:1.6;">
                                Si tienes alguna pregunta, no dudes en contactarnos por WhatsApp o visitarnos en la tienda.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:28px 40px 32px;text-align:center;border-top:1px solid #edf0f7;margin-top:24px;">
                            <p style="margin:0;font-size:13px;font-weight:700;color:#103192;">
                                {{ $businessName }}
                            </p>
                            <p style="margin:4px 0 0;font-size:12px;color:#9ca3af;">
                                {{ $businessAddress }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
