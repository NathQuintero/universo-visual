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

                    {{-- Header festivo --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#103192,#1a4fd0);padding:32px 40px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:800;">
                                Feliz Cumpleanos!
                            </h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">
                                Optica Universo Visual te celebra
                            </p>
                        </td>
                    </tr>

                    {{-- Saludo personalizado --}}
                    <tr>
                        <td style="padding:32px 40px 0;">
                            <p style="margin:0 0 12px;font-size:18px;font-weight:700;color:#1a1a2e;">
                                Querido(a) {{ $client->first_name }},
                            </p>
                            <p style="margin:0;font-size:14px;color:#4a5568;line-height:1.8;">
                                Hoy es un dia muy especial y desde Optica Universo Visual queremos desearte un muy feliz cumpleanos.
                            </p>
                            <p style="margin:16px 0 0;font-size:14px;color:#4a5568;line-height:1.8;">
                                Que Dios te bendiga grandemente, te llene de salud, amor y muchas bendiciones en este nuevo ano de vida.
                            </p>
                            <p style="margin:16px 0 0;font-size:14px;color:#4a5568;line-height:1.8;font-style:italic;border-left:3px solid #103192;padding-left:16px;">
                                "Porque yo se los planes que tengo para ustedes, planes de bienestar y no de calamidad, a fin de darles un futuro y una esperanza."
                                <br><strong style="color:#103192;">— Jeremias 29:11</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Tarjeta de descuento --}}
                    <tr>
                        <td style="padding:28px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#f0f4ff,#e8edff);border-radius:12px;border:2px solid #c7d2fe;">
                                <tr>
                                    <td style="padding:28px;text-align:center;">
                                        <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:1.5px;">
                                            Tu regalo de cumpleanos
                                        </p>
                                        <p style="margin:0;font-size:48px;font-weight:900;color:#103192;letter-spacing:-2px;">
                                            15% OFF
                                        </p>
                                        <p style="margin:8px 0 0;font-size:14px;font-weight:600;color:#1a1a2e;">
                                            en tu proxima compra
                                        </p>
                                        <hr style="border:none;border-top:1px dashed #c7d2fe;margin:16px 0;">
                                        <p style="margin:0;font-size:12px;color:#6c757d;">
                                            Valido desde tu cumpleanos hasta el
                                            <strong style="color:#103192;">{{ $discountExpiry }}</strong>
                                        </p>
                                        <p style="margin:6px 0 0;font-size:11px;color:#9ca3af;">
                                            Presenta este correo en la tienda para redimir tu descuento
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Invitación --}}
                    <tr>
                        <td style="padding:24px 40px 0;">
                            <p style="margin:0;font-size:14px;color:#4a5568;line-height:1.7;text-align:center;">
                                Ven a visitarnos y estrena con estilo.
                                <br>Te esperamos con los brazos abiertos.
                            </p>
                        </td>
                    </tr>

                    {{-- Nota sobre tarjeta adjunta --}}
                    <tr>
                        <td style="padding:20px 40px 0;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                Adjuntamos una tarjeta especial de cumpleanos para ti.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:28px 40px 32px;text-align:center;">
                            <p style="margin:0;font-size:14px;color:#4a5568;">Con carino,</p>
                            <p style="margin:6px 0 0;font-size:14px;font-weight:700;color:#103192;">
                                Tu familia de Optica Universo Visual
                            </p>
                            <p style="margin:4px 0 0;font-size:12px;color:#9ca3af;">
                                Centro Comercial La Isla, Bucaramanga
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
