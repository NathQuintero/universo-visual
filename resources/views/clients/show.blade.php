{{--
    Vista: Ficha completa del Cliente
    Ruta: GET /clientes/{client}
    Controlador: ClientController@show
    
    Muestra datos personales, fórmula actual, historial de fórmulas,
    historial de trabajos completo.
--}}

@extends('layouts.app')
@section('title', $client->full_name)

@section('styles')
<style>
    .igrid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }
    .isec { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 16px; }
    .isec h4 { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
    .irow { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; }
    .irow .lbl { color: var(--text-secondary); }
    .irow .val { font-weight: 600; }
    .ftable { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .ftable th { padding: 8px; text-align: center; font-size: 10px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
    .ftable td { padding: 8px; text-align: center; font-family: 'JetBrains Mono'; font-size: 14px; font-weight: 600; }
    .ftable tr.od td { color: var(--cyan); }
    .ftable tr.oi td { color: var(--purple); }
    @media (max-width: 768px) { .igrid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>👤 {{ $client->full_name }}</h2>
        <div class="ph-acts">
            <a href="{{ route('works.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-p">➕ Nuevo Trabajo</a>
            @if($client->phone && $client->whatsapp_authorized)
                <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $client->phone) }}" target="_blank" class="btn btn-sm btn-g">💬 WhatsApp</a>
            @endif
            <a href="{{ route('pdf.client', $client) }}" class="btn btn-sm btn-v">📄 Descargar PDF</a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-s">✏️ Editar</a>
            <a href="{{ route('clients.index') }}" class="btn btn-sm btn-s">← Volver</a>
        </div>
    </div>

    {{-- Datos personales + Fórmula actual --}}
    <div class="igrid">
        <div class="isec">
            <h4>📊 Datos Personales</h4>
            <div class="irow"><span class="lbl">Documento</span><span class="val">{{ $client->document_type }} {{ $client->document_number }}</span></div>
            <div class="irow"><span class="lbl">Teléfono</span><span class="val">{{ $client->phone ?? '—' }}</span></div>
            <div class="irow"><span class="lbl">Email</span><span class="val">{{ $client->email ?? '—' }}</span></div>
            <div class="irow"><span class="lbl">Dirección</span><span class="val">{{ $client->address ?? '—' }}</span></div>
            <div class="irow">
                <span class="lbl">Cumpleaños</span>
                <span class="val">{{ $client->birth_date ? '🎂 ' . $client->birth_date->translatedFormat('d \\d\\e F') : '—' }}</span>
            </div>
            <div class="irow">
                <span class="lbl">WhatsApp</span>
                <span class="val" style="color:{{ $client->whatsapp_authorized ? 'var(--green)' : 'var(--red)' }}">
                    {{ $client->whatsapp_authorized ? '✅ Autorizado' : '❌ No autorizado' }}
                </span>
            </div>
            @if($client->notes)
                <div class="irow"><span class="lbl">Notas</span><span class="val">{{ $client->notes }}</span></div>
            @endif
        </div>

        <div class="isec">
            <h4>📝 Fórmula Actual</h4>
            @php $formula = $client->formulas->first(); @endphp
            @if($formula)
                <table class="ftable">
                    <thead><tr><th></th><th>Esfera</th><th>Cilindro</th><th>Eje</th><th>ADD</th><th>DNP</th></tr></thead>
                    <tbody>
                        <tr class="od">
                            <td style="font-weight:700;color:var(--cyan)">OD</td>
                            <td>{{ $formula->od_sphere !== null ? number_format($formula->od_sphere, 2) : '—' }}</td>
                            <td>{{ $formula->od_cylinder !== null ? number_format($formula->od_cylinder, 2) : '—' }}</td>
                            <td>{{ $formula->od_axis !== null ? $formula->od_axis . '°' : '—' }}</td>
                            <td>{{ $formula->od_add !== null ? number_format($formula->od_add, 2) : '—' }}</td>
                            <td>{{ $formula->od_dnp ?? '—' }}</td>
                        </tr>
                        <tr class="oi">
                            <td style="font-weight:700;color:var(--purple)">OI</td>
                            <td>{{ $formula->oi_sphere !== null ? number_format($formula->oi_sphere, 2) : '—' }}</td>
                            <td>{{ $formula->oi_cylinder !== null ? number_format($formula->oi_cylinder, 2) : '—' }}</td>
                            <td>{{ $formula->oi_axis !== null ? $formula->oi_axis . '°' : '—' }}</td>
                            <td>{{ $formula->oi_add !== null ? number_format($formula->oi_add, 2) : '—' }}</td>
                            <td>{{ $formula->oi_dnp ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size:11px;color:var(--text-muted);margin-top:8px">
                    Fecha examen: {{ $formula->exam_date ? $formula->exam_date->format('d/m/Y') : '—' }}
                    @if($formula->notes) — {{ $formula->notes }} @endif
                </p>
            @else
                <p style="color:var(--text-muted)">Sin fórmula registrada.</p>
            @endif
        </div>
    </div>

    {{-- Historial de fórmulas (evolución visual) --}}
    @if($client->formulas->count() > 1)
        <div class="isec" style="margin-bottom:20px">
            <h4>📈 Evolución Visual ({{ $client->formulas->count() }} fórmulas)</h4>
            <table class="ftable" style="text-align:left">
                <thead>
                    <tr>
                        <th style="text-align:left">Fecha</th>
                        <th>OD Esf</th><th>OD Cil</th><th>OD Eje</th><th>OD ADD</th>
                        <th>OI Esf</th><th>OI Cil</th><th>OI Eje</th><th>OI ADD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->formulas as $f)
                        <tr>
                            <td style="color:var(--text-secondary);font-size:12px;text-align:left">
                                {{ $f->exam_date ? $f->exam_date->format('d/m/Y') : '—' }}
                            </td>
                            <td style="color:var(--cyan)">{{ $f->od_sphere !== null ? number_format($f->od_sphere, 2) : '—' }}</td>
                            <td style="color:var(--cyan)">{{ $f->od_cylinder !== null ? number_format($f->od_cylinder, 2) : '—' }}</td>
                            <td style="color:var(--cyan)">{{ $f->od_axis !== null ? $f->od_axis . '°' : '—' }}</td>
                            <td style="color:var(--cyan)">{{ $f->od_add !== null ? number_format($f->od_add, 2) : '—' }}</td>
                            <td style="color:var(--purple)">{{ $f->oi_sphere !== null ? number_format($f->oi_sphere, 2) : '—' }}</td>
                            <td style="color:var(--purple)">{{ $f->oi_cylinder !== null ? number_format($f->oi_cylinder, 2) : '—' }}</td>
                            <td style="color:var(--purple)">{{ $f->oi_axis !== null ? $f->oi_axis . '°' : '—' }}</td>
                            <td style="color:var(--purple)">{{ $f->oi_add !== null ? number_format($f->oi_add, 2) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Historial de trabajos --}}
    <div class="isec">
        <h4>📋 Historial de Trabajos ({{ $client->works->count() }})</h4>
        @if($client->works->count() > 0)
            <table class="tbl" style="font-size:13px">
                <thead><tr><th>Código</th><th>Fecha</th><th>Tipo Lente</th><th>Laboratorio</th><th>Estado</th><th>Total</th><th>Saldo</th></tr></thead>
                <tbody>
                    @foreach($client->works as $work)
                        <tr onclick="window.location='{{ route('works.show', $work) }}'">
                            <td style="font-family:'JetBrains Mono';color:var(--blue);font-size:12px">{{ $work->tracking_code }}</td>
                            <td style="font-size:12px">{{ $work->created_at->format('d/m/Y') }}</td>
                            <td>{{ $work->lens_type_name }}</td>
                            <td>{{ $work->laboratory->name }}</td>
                            <td><span class="badge badge-{{ $work->status_color }}">{{ $work->status_emoji }} {{ $work->status_name }}</span></td>
                            <td style="font-family:'JetBrains Mono'">${{ number_format($work->price_total, 0, ',', '.') }}</td>
                            <td style="font-family:'JetBrains Mono';color:{{ $work->pending_balance > 0 ? 'var(--red)' : 'var(--green)' }}">
                                ${{ number_format($work->pending_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center;padding:20px;color:var(--text-muted)">Este cliente no tiene trabajos aún.</p>
        @endif
    </div>
@endsection