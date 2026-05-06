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
                        <td style="background:linear-gradient(135deg,#103192,#1a4fd0);padding:28px 40px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:800;">
                                Óptica Universo Visual
                            </h1>
                            <p style="margin:6px 0 0;color:rgba(255,255,255,0.8);font-size:13px;">
                                Actualización de tu pedido
                            </p>
                        </td>
                    </tr>

                    {{-- Saludo --}}
                    <tr>
                        <td style="padding:32px 40px 0;">
                            <p style="margin:0 0 8px;font-size:17px;font-weight:700;color:#1a1a2e;">
                                Hola {{ $work->client->first_name }},
                            </p>
                            <p style="margin:0;font-size:14px;color:#6c757d;line-height:1.6;">
                                Te informamos que el estado de tu pedido
                                <strong style="color:#103192;">{{ $work->tracking_code }}</strong>
                                ha sido actualizado.
                            </p>
                        </td>
                    </tr>

                    {{-- Estado actual --}}
                    <tr>
                        <td style="padding:24px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fc;border-radius:10px;border:1px solid #e8edff;">
                                <tr>
                                    <td style="padding:24px;text-align:center;">
                                        <p style="margin:0 0 6px;font-size:12px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:1px;">
                                            Nuevo estado
                                        </p>
                                        <p style="margin:0;font-size:24px;font-weight:800;color:#103192;">
                                            {{ $statusEmoji }} {{ $newStatusName }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Mensaje contextual según estado --}}
                    <tr>
                        <td style="padding:24px 40px 0;">
                            <p style="margin:0;font-size:14px;color:#4a5568;line-height:1.7;">
                                @switch($work->status)
                                    @case('registered')
                                        Tu pedido ha sido registrado en nuestro sistema. Pronto lo enviaremos al laboratorio para su elaboración.
                                        @break
                                    @case('sent_to_lab')
                                        Tu pedido ya fue enviado al laboratorio. El proceso de elaboración de tus lentes ha comenzado.
                                        @break
                                    @case('in_process')
                                        Tus lentes están siendo elaborados en el laboratorio. Te avisaremos cuando estén listos.
                                        @break
                                    @case('received')
                                        Hemos recibido tus lentes del laboratorio. Estamos verificando la calidad antes de notificarte.
                                        @break
                                    @case('ready')
                                        ¡Tus gafas están listas para recoger! Puedes pasar por nuestra óptica en horario de atención.
                                        @break
                                    @case('delivered')
                                        Tu pedido ha sido entregado. Esperamos que disfrutes tus nuevas gafas. ¡Gracias por tu confianza!
                                        @break
                                    @case('cancelled')
                                        Tu pedido ha sido cancelado. Si tienes alguna duda, no dudes en contactarnos.
                                        @break
                                @endswitch
                            </p>
                        </td>
                    </tr>

                    {{-- Botón de seguimiento --}}
                    <tr>
                        <td style="padding:28px 40px 0;text-align:center;">
                            <a href="{{ $trackingUrl }}" target="_blank"
                               style="display:inline-block;background:#103192;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:10px;font-size:14px;font-weight:700;">
                                Ver estado de mi pedido
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
                        <td style="padding:28px 40px 32px;text-align:center;">
                            <p style="margin:0;font-size:13px;font-weight:700;color:#103192;">
                                Óptica Universo Visual
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
