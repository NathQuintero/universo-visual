{{--
    Vista: Detalle de un Trabajo
    Ruta: GET /trabajos/{work}
    Controlador: WorkController@show
    
    Muestra el detalle completo: timeline, QR, pagos, fórmula,
    historial de estados. Botón "Ver como cliente" para preview.
--}}

@extends('layouts.app')
@section('title', $work->tracking_code)

@section('styles')
<style>
    /* Timeline horizontal */
    .timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 22px 0;
        position: relative;
        margin-bottom: 22px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 50%; left: 32px; right: 32px;
        height: 3px;
        background: var(--border);
        transform: translateY(-50%);
        z-index: 1;
    }
    .ts {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 7px;
        position: relative;
        z-index: 2;
    }
    .ts-icon {
        width: 42px; height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        border: 3px solid var(--border);
        background: var(--bg-deep);
        transition: var(--fast);
    }
    .ts.done .ts-icon {
        background: var(--grad-blue);
        border-color: var(--blue);
        box-shadow: 0 0 12px rgba(74,108,247,0.3);
    }
    .ts.now .ts-icon {
        background: var(--grad-warn);
        border-color: var(--yellow);
        box-shadow: 0 0 12px rgba(255,193,7,0.3);
        animation: nowPulse 2s ease-in-out infinite;
    }
    @keyframes nowPulse {
        0%, 100% { box-shadow: 0 0 12px rgba(255,193,7,0.3); }
        50% { box-shadow: 0 0 22px rgba(255,193,7,0.5); }
    }
    .ts-lbl { font-size: 11px; font-weight: 600; text-align: center; }
    .ts-date { font-size: 10px; color: var(--text-muted); font-family: 'JetBrains Mono'; }
    .ts.pending .ts-lbl { color: var(--text-muted); }

    /* Info grid */
    .igrid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }
    .isec { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 16px; }
    .isec h4 { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
    .irow { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; }
    .irow .lbl { color: var(--text-secondary); }
    .irow .val { font-weight: 600; }

    /* Fórmula óptica */
    .ftable { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 6px; }
    .ftable th { padding: 8px; text-align: center; font-size: 10px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
    .ftable td { padding: 8px; text-align: center; font-family: 'JetBrains Mono'; font-size: 14px; font-weight: 600; }
    .ftable tr.od td { color: var(--cyan); }
    .ftable tr.oi td { color: var(--purple); }

    /* Barra de pagos */
    .paybar {
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        padding: 16px;
        margin-bottom: 20px;
    }
    .pbi { text-align: center; flex: 1; }
    .pb-lbl { font-size: 11px; color: var(--text-muted); }
    .pb-val { font-size: 20px; font-weight: 800; font-family: 'JetBrains Mono'; }
    .pb-val.total { color: var(--blue); }
    .pb-val.paid { color: var(--green); }
    .pb-val.pend { color: var(--red); }
    .divider { width: 1px; height: 36px; background: var(--border); }

    /* QR */
    .qr-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 20px;
    }
    .qr-box {
        width: 110px; height: 110px;
        background: #fff;
        border-radius: var(--r);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .qr-info { flex: 1; }
    .qr-info h4 { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
    .qr-info p { font-size: 12px; color: var(--text-secondary); margin-bottom: 10px; }
    .qr-code-text { font-family: 'JetBrains Mono'; font-size: 18px; font-weight: 700; color: var(--blue); letter-spacing: 1px; }
    .qr-actions { display: flex; gap: 7px; margin-top: 10px; }

    @media (max-width: 768px) {
        .igrid { grid-template-columns: 1fr; }
        .qr-section { flex-direction: column; text-align: center; }
        .qr-actions { justify-content: center; }
    }
</style>
@endsection

@section('content')
    {{-- Header --}}
    <div class="ph">
        <h2>👓 {{ $work->tracking_code }}</h2>
        <div class="ph-acts">
            @if($work->client->phone && $work->client->whatsapp_authorized)
                <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $work->client->phone) }}?text={{ urlencode('¡Hola ' . $work->client->first_name . '! Te escribimos de Óptica Universo Visual sobre tu pedido ' . $work->tracking_code . '.') }}"
                   target="_blank" class="btn btn-sm btn-g">💬 WhatsApp</a>
            @endif
            <a href="{{ route('pdf.work', $work) }}" class="btn btn-sm btn-v">📄 Descargar PDF</a>
            @if($work->client->phone && $work->client->whatsapp_authorized)
                @php
                    $pdfUrl = route('pdf.work.public', $work);
                    $msgPdf = "Hola " . $work->client->first_name . "!\n\n"
                        . "Te compartimos el recibo de tu pedido " . $work->tracking_code . " en Optica Universo Visual.\n\n"
                        . "Puedes verlo y descargarlo aqui:\n"
                        . $pdfUrl . "\n\n"
                        . "Si tienes alguna duda, estamos para ayudarte!";
                @endphp
                <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $work->client->phone) }}?text={{ rawurlencode($msgPdf) }}"
                target="_blank" class="btn btn-sm btn-g">📄 Enviar Recibo por WhatsApp</a>
            @endif
            <a href="{{ route('works.edit', $work) }}" class="btn btn-sm btn-s">✏️ Editar Trabajo</a>
            <a href="{{ route('tracking', $work->tracking_code) }}" target="_blank" class="btn btn-sm btn-s">🔍 Ver como cliente</a>
            <a href="{{ route('works.index') }}" class="btn btn-sm btn-s">← Volver</a>
        </div>
    </div>

    {{-- =============================================
         TIMELINE DE ESTADOS
         ============================================= --}}
    @php
        $statusOrder = ['registered', 'sent_to_lab', 'in_process', 'received', 'ready', 'delivered'];
        $statusEmojis = ['📝', '📦', '🔬', '📬', '✅', '🎉'];
        $statusLabels = ['Creado', 'Enviado', 'En Proceso', 'Recibido', 'Listo', 'Entregado'];
        $currentIndex = array_search($work->status, $statusOrder);
        if ($currentIndex === false) $currentIndex = -1;
    @endphp

    <div class="timeline">
        @foreach($statusOrder as $i => $status)
            @php
                $change = $work->statusChanges->firstWhere('to_status', $status);
                $stateClass = 'pending';
                if ($i < $currentIndex) $stateClass = 'done';
                elseif ($i == $currentIndex) $stateClass = 'now';
            @endphp
            <div class="ts {{ $stateClass }}">
                <div class="ts-icon">{{ $statusEmojis[$i] }}</div>
                <div class="ts-lbl">{{ $statusLabels[$i] }}</div>
                <div class="ts-date">
                    {{ $change ? $change->created_at->format('d/m') : '—' }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- =============================================
         CAMBIAR ESTADO
         ============================================= --}}
    @if(!in_array($work->status, ['delivered']))
        <form action="{{ route('works.updateStatus', $work) }}" method="POST"
              style="display:flex;align-items:center;gap:9px;margin-bottom:20px;padding:12px;background:var(--bg-card);border-radius:var(--r-md);border:1px solid var(--border)">
            @csrf
            @method('PATCH')
            <span style="font-size:13px;font-weight:600">Cambiar estado a:</span>
            <select name="status" class="combo" style="min-width:220px">
                @foreach($statusOrder as $status)
                    <option value="{{ $status }}" {{ $work->status == $status ? 'selected' : '' }}>
                        {{ $statusEmojis[array_search($status, $statusOrder)] }} {{ $statusLabels[array_search($status, $statusOrder)] }}
                    </option>
                @endforeach
                <option value="cancelled">❌ Cancelar</option>
            </select>
            <input name="notes" class="combo" style="flex:1;min-width:auto" placeholder="Observación (opcional)...">
            <button type="submit" class="btn btn-sm btn-p">Actualizar</button>
        </form>
    @endif

    {{-- =============================================
         SECCIÓN QR Y CÓDIGO DE SEGUIMIENTO
         ============================================= --}}
    <div class="qr-section">
        <div class="qr-box">
            {{-- QR simple con SVG --}}
            <svg viewBox="0 0 100 100" width="90" height="90">
                <rect fill="#000" x="5" y="5" width="25" height="25" rx="2"/>
                <rect fill="#000" x="70" y="5" width="25" height="25" rx="2"/>
                <rect fill="#000" x="5" y="70" width="25" height="25" rx="2"/>
                <rect fill="#fff" x="10" y="10" width="15" height="15" rx="1"/>
                <rect fill="#fff" x="75" y="10" width="15" height="15" rx="1"/>
                <rect fill="#fff" x="10" y="75" width="15" height="15" rx="1"/>
                <rect fill="#000" x="13" y="13" width="9" height="9"/>
                <rect fill="#000" x="78" y="13" width="9" height="9"/>
                <rect fill="#000" x="13" y="78" width="9" height="9"/>
                <rect fill="#000" x="38" y="8" width="5" height="5"/>
                <rect fill="#000" x="48" y="12" width="5" height="5"/>
                <rect fill="#000" x="58" y="8" width="5" height="5"/>
                <rect fill="#000" x="42" y="42" width="16" height="16" rx="2"/>
                <rect fill="#fff" x="46" y="46" width="8" height="8" rx="1"/>
                <rect fill="#000" x="38" y="65" width="5" height="5"/>
                <rect fill="#000" x="55" y="72" width="5" height="5"/>
                <rect fill="#000" x="68" y="55" width="5" height="5"/>
                <rect fill="#000" x="80" y="65" width="5" height="5"/>
                <rect fill="#000" x="70" y="80" width="5" height="5"/>
            </svg>
        </div>
        <div class="qr-info">
            <h4>📱 Código de Seguimiento</h4>
            <div class="qr-code-text">{{ $work->tracking_code }}</div>
            <p>El cliente puede escanear este QR o ingresar el código en el portal para ver el estado.</p>
            <div class="qr-actions">
                <a href="{{ route('tracking', $work->tracking_code) }}" target="_blank" class="btn btn-sm btn-p">🔗 Abrir Link</a>
                @if($work->client->phone && $work->client->whatsapp_authorized)
                    <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $work->client->phone) }}?text={{ urlencode('¡Hola ' . $work->client->first_name . '! 👋 Puedes consultar el estado de tus gafas aquí: ' . route('tracking', $work->tracking_code)) }}"
                       target="_blank" class="btn btn-sm btn-g">💬 Enviar por WhatsApp</a>
                @endif
            </div>
        </div>
    </div>

    {{-- =============================================
         BARRA DE PAGOS
         ============================================= --}}
    <div class="paybar">
        <div class="pbi">
            <div class="pb-lbl">💰 Total</div>
            <div class="pb-val total">${{ number_format($work->price_total, 0, ',', '.') }}</div>
        </div>
        <div class="divider"></div>
        <div class="pbi">
            <div class="pb-lbl">✅ Abonado</div>
            <div class="pb-val paid">${{ number_format($work->total_paid, 0, ',', '.') }}</div>
        </div>
        <div class="divider"></div>
        <div class="pbi">
            <div class="pb-lbl">⏳ Saldo</div>
            <div class="pb-val pend">${{ number_format($work->pending_balance, 0, ',', '.') }}</div>
        </div>
        @if($work->pending_balance > 0)
            <div class="divider"></div>
            <button onclick="document.getElementById('paymentForm').style.display='flex'" class="btn btn-sm btn-p">+ Registrar Abono</button>
        @endif
    </div>

    {{-- Formulario de abono (oculto hasta que presionan el botón) --}}
    <form id="paymentForm" action="{{ route('works.storePayment', $work) }}" method="POST"
          style="display:none;align-items:center;gap:9px;margin-bottom:20px;padding:12px;background:var(--bg-card);border-radius:var(--r-md);border:1px solid var(--border)">
        @csrf
        <span style="font-size:13px;font-weight:600">💰 Nuevo abono:</span>
        <input name="amount" type="number" class="combo" style="width:140px;min-width:auto" placeholder="Monto..." required>
        <select name="method" class="combo" style="min-width:140px">
            <option value="cash">💵 Efectivo</option>
            <option value="card">💳 Tarjeta</option>
            <option value="transfer">🏦 Transferencia</option>
            <option value="nequi">📱 Nequi</option>
            <option value="daviplata">📱 Daviplata</option>
            <option value="other">Otro</option>
        </select>
        <input name="notes" class="combo" style="flex:1;min-width:auto" placeholder="Nota (opcional)">
        <button type="submit" class="btn btn-sm btn-g">✅ Registrar</button>
        <button type="button" onclick="this.parentElement.style.display='none'" class="btn btn-sm btn-s">✕</button>
    </form>

    {{-- =============================================
         INFORMACIÓN EN 2 COLUMNAS
         ============================================= --}}
    <div class="igrid">
        {{-- Info del cliente --}}
        <div class="isec">
            <h4>👤 Cliente</h4>
            <div class="irow"><span class="lbl">Nombre</span><span class="val">{{ $work->client->full_name }}</span></div>
            <div class="irow"><span class="lbl">Cédula</span><span class="val">{{ $work->client->document_number }}</span></div>
            <div class="irow"><span class="lbl">Teléfono</span><span class="val">{{ $work->client->phone ?? '—' }}</span></div>
            <div class="irow"><span class="lbl">WhatsApp</span><span class="val" style="color:{{ $work->client->whatsapp_authorized ? 'var(--green)' : 'var(--red)' }}">{{ $work->client->whatsapp_authorized ? '✅ Autorizado' : '❌ No autorizado' }}</span></div>
            <div class="irow"><span class="lbl">Creado por</span><span class="val">{{ $work->user->name }}</span></div>
        </div>

        {{-- Info del lente --}}
        <div class="isec">
            <h4>👓 Especificaciones</h4>
            <div class="irow"><span class="lbl">Montura</span><span class="val">{{ $work->frame_type == 'own' ? 'Propia' : 'Comprada' }}{{ $work->frame_brand ? ' — ' . $work->frame_brand . ' ' . $work->frame_reference : '' }}</span></div>
            <div class="irow"><span class="lbl">Tipo Lente</span><span class="val">{{ $work->lens_type_name }}</span></div>
            <div class="irow"><span class="lbl">Material</span><span class="val">{{ $work->lens_material_name }}</span></div>
            <div class="irow"><span class="lbl">Tratamientos</span><span class="val">{{ $work->treatments_text }}</span></div>
            <div class="irow"><span class="lbl">Laboratorio</span><span class="val">{{ $work->laboratory->name }}</span></div>
            @if($work->estimated_delivery)
                <div class="irow"><span class="lbl">Entrega estimada</span><span class="val">{{ $work->estimated_delivery->format('d/m/Y') }}</span></div>
            @endif
        </div>
    </div>

    {{-- =============================================
         FÓRMULA ÓPTICA
         ============================================= --}}
    <div class="isec" style="margin-bottom:20px">
        <h4>📝 Fórmula Óptica</h4>
        <table class="ftable">
            <thead>
                <tr><th></th><th>Esfera</th><th>Cilindro</th><th>Eje</th><th>ADD</th><th>DNP</th></tr>
            </thead>
            <tbody>
                <tr class="od">
                    <td style="font-weight:700;color:var(--cyan)">OD</td>
                    <td>{{ $work->formula->od_sphere !== null ? number_format($work->formula->od_sphere, 2) : '—' }}</td>
                    <td>{{ $work->formula->od_cylinder !== null ? number_format($work->formula->od_cylinder, 2) : '—' }}</td>
                    <td>{{ $work->formula->od_axis !== null ? $work->formula->od_axis . '°' : '—' }}</td>
                    <td>{{ $work->formula->od_add !== null ? number_format($work->formula->od_add, 2) : '—' }}</td>
                    <td>{{ $work->formula->od_dnp ?? '—' }}</td>
                </tr>
                <tr class="oi">
                    <td style="font-weight:700;color:var(--purple)">OI</td>
                    <td>{{ $work->formula->oi_sphere !== null ? number_format($work->formula->oi_sphere, 2) : '—' }}</td>
                    <td>{{ $work->formula->oi_cylinder !== null ? number_format($work->formula->oi_cylinder, 2) : '—' }}</td>
                    <td>{{ $work->formula->oi_axis !== null ? $work->formula->oi_axis . '°' : '—' }}</td>
                    <td>{{ $work->formula->oi_add !== null ? number_format($work->formula->oi_add, 2) : '—' }}</td>
                    <td>{{ $work->formula->oi_dnp ?? '—' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- =============================================
         OBSERVACIONES
         ============================================= --}}
    @if($work->observations)
        <div class="isec" style="margin-bottom:20px">
            <h4>📝 Observaciones</h4>
            <p style="font-size:13.5px;color:var(--text-secondary);padding-top:4px">{{ $work->observations }}</p>
        </div>
    @endif

    {{-- =============================================
         HISTORIAL DE PAGOS
         ============================================= --}}
    @if($work->payments->count() > 0)
        <div class="isec" style="margin-bottom:20px">
            <h4>💰 Historial de Pagos</h4>
            <table class="ftable" style="text-align:left">
                <thead><tr><th style="text-align:left">Fecha</th><th style="text-align:left">Monto</th><th style="text-align:left">Método</th><th style="text-align:left">Registró</th><th style="text-align:left">Nota</th><th style="text-align:center">Acción</th></tr></thead>
                <tbody>
                    @foreach($work->payments as $payment)
                        <tr>
                            <td style="color:var(--text-secondary);font-size:12px">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td style="color:var(--green);font-weight:700">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td style="font-size:12px">{{ $payment->method_name }}</td>
                            <td style="font-size:12px;color:var(--text-secondary)">{{ $payment->user->name }}</td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $payment->notes ?? '—' }}</td>
                            <td style="text-align:center">
                                <form action="{{ route('works.destroyPayment', [$work, $payment]) }}" method="POST" 
                                      onsubmit="return confirm('¿Seguro que quieres eliminar este abono de ${{ number_format($payment->amount, 0, ',', '.') }}?')"
                                      style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-s" style="color:var(--red)" title="Eliminar abono">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- =============================================
         HISTORIAL DE ESTADOS
         ============================================= --}}
    <div class="isec">
        <h4>📜 Historial de Estados</h4>
        @foreach($work->statusChanges as $change)
            @php
                $emojis = ['registered'=>'📝','sent_to_lab'=>'📦','in_process'=>'🔬','received'=>'📬','ready'=>'✅','delivered'=>'🎉','cancelled'=>'❌'];
            @endphp
            <div class="irow">
                <span class="lbl">{{ $emojis[$change->to_status] ?? '❓' }} {{ $change->created_at->format('d/m/Y H:i') }}</span>
                <span class="val">{{ $change->user->name }}{{ $change->notes ? ' — ' . $change->notes : '' }}</span>
            </div>
        @endforeach
    </div>
@endsection