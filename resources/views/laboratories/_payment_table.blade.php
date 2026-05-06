{{--
    Partial: Tabla de trabajos para una sección del detalle del laboratorio
    Variables esperadas:
      - $works       Collection de trabajos
      - $laboratory  Modelo Laboratory (para construir las rutas de pago)
      - $pillClass   Clase CSS del badge de días: over | due | soon | ok | paid
      - $showUnpay   bool — mostrar botón para deshacer pago (en sección "ya pagados")
--}}

@php $showUnpay = $showUnpay ?? false; @endphp

<div class="card">
    <div class="card-b" style="padding:0">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Recibido del lab</th>
                        <th style="text-align:center">Días</th>
                        <th style="text-align:right">Costo lab</th>
                        <th>Estado trabajo</th>
                        <th style="text-align:center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($works as $w)
                        @php
                            $received = $w->lab_received_at;
                            $days = $w->days_owed_to_lab;
                        @endphp
                        <tr>
                            <td style="font-family:'JetBrains Mono';font-weight:700;color:#103192">
                                <a href="{{ route('works.show', $w) }}" style="color:inherit;text-decoration:none">{{ $w->tracking_code }}</a>
                            </td>
                            <td>{{ $w->client->full_name }}</td>
                            <td style="font-size:12px;color:var(--text-secondary)">
                                {{ $received ? $received->format('d/m/Y') : '— sin recibir —' }}
                            </td>
                            <td style="text-align:center">
                                @if($w->is_lab_paid)
                                    <span class="days-pill paid">Pagado</span>
                                @elseif($days === null)
                                    <span class="days-pill ok">—</span>
                                @else
                                    <span class="days-pill {{ $pillClass }}">
                                        {{ $days }} {{ $days === 1 ? 'día' : 'días' }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:700;color:{{ $w->lab_cost > 0 ? 'var(--text-primary)' : 'var(--text-muted)' }}">
                                @if($w->lab_cost > 0)
                                    ${{ number_format($w->lab_cost, 0, ',', '.') }}
                                @else
                                    <span style="font-size:11px">sin definir</span>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $w->status_color }}">{{ $w->status_emoji }} {{ $w->status_name }}</span></td>
                            <td style="text-align:center">
                                @if($showUnpay)
                                    <form action="{{ route('laboratories.unmarkPaid', [$laboratory, $w]) }}" method="POST"
                                          onsubmit="return confirm('¿Deshacer el pago al lab para {{ $w->tracking_code }}?')"
                                          style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="unpay-btn" title="Deshacer pago">↩️ Deshacer</button>
                                    </form>
                                @elseif($w->lab_received_at)
                                    <form action="{{ route('laboratories.markPaid', [$laboratory, $w]) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" class="pay-btn">💵 Marcar pagado</button>
                                    </form>
                                @else
                                    <span style="font-size:11px;color:var(--text-muted)">aún no recibido</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
