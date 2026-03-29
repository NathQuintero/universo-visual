{{--
    Vista: Listado de Clientes
    Ruta: GET /clientes
    Controlador: ClientController@index
    
    Tabla expandible: al hacer clic se muestra fórmula e historial.
--}}

@extends('layouts.app')
@section('title', 'Clientes')

@section('styles')
<style>
    .client-expand {
        display: none;
        padding: 18px;
        background: var(--bg-primary);
        border-top: 1px solid var(--border);
        animation: expandIn .25s ease;
    }
    .client-expand.open { display: block; }
    @keyframes expandIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .igrid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
    .isec { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 14px; }
    .isec h4 { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .irow { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
    .irow .lbl { color: var(--text-secondary); }
    .irow .val { font-weight: 600; }
    .ftable { width: 100%; border-collapse: collapse; margin-top: 4px; }
    .ftable th { padding: 7px; text-align: center; font-size: 10px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
    .ftable td { padding: 7px; text-align: center; font-family: 'JetBrains Mono'; font-size: 13px; font-weight: 600; }
    .ftable tr.od td { color: var(--cyan); }
    .ftable tr.oi td { color: var(--purple); }
    @media (max-width: 768px) { .igrid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>👥 Gestión de Clientes</h2>
        <div class="ph-acts">
            <a href="{{ route('clients.create') }}" class="btn btn-p">➕ Nuevo Cliente</a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filters-row">
        <form action="{{ route('clients.index') }}" method="GET" class="search-box" style="width:300px">
            <span>🔍</span>
            <input name="search" placeholder="Buscar por nombre, cédula o teléfono..." value="{{ request('search') }}">
        </form>
        <form action="{{ route('clients.index') }}" method="GET" style="display:flex;gap:9px">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <select name="filter" class="combo" onchange="this.form.submit()">
                <option value="">Todos los clientes</option>
                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Con trabajos activos</option>
                <option value="pending_balance" {{ request('filter') == 'pending_balance' ? 'selected' : '' }}>Con saldo pendiente</option>
                <option value="new" {{ request('filter') == 'new' ? 'selected' : '' }}>Nuevos este mes</option>
            </select>
        </form>
        @if(request()->hasAny(['search', 'filter']))
            <a href="{{ route('clients.index') }}" class="btn btn-sm btn-s">✕ Limpiar</a>
        @endif
    </div>

    {{-- Estadísticas --}}
    <div class="stats" style="grid-template-columns:repeat(4,1fr);margin-bottom:18px">
        <div class="stat s-blue"><div class="stat-val">{{ $stats['total'] }}</div><div class="stat-label">Total Clientes</div></div>
        <div class="stat s-green"><div class="stat-val">{{ $stats['new_this_month'] }}</div><div class="stat-label">Nuevos este Mes</div></div>
        <div class="stat s-yellow"><div class="stat-val">{{ $stats['with_active_works'] }}</div><div class="stat-label">Con Trabajos Activos</div></div>
        <div class="stat s-red"><div class="stat-val">{{ $stats['with_pending_balance'] }}</div><div class="stat-label">Con Saldo Pendiente</div></div>
    </div>

    {{-- Tabla de clientes --}}
    <div class="card">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Trabajos</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    @php
                        $latestFormula = $client->formulas->first();
                        $pendingBalance = $client->total_pending_balance;
                    @endphp

                    {{-- Fila principal (clickeable) --}}
                    <tr onclick="toggleClient('client-{{ $client->id }}')" style="cursor:pointer">
                        <td><strong>{{ $client->full_name }}</strong></td>
                        <td style="font-family:'JetBrains Mono';font-size:12px">{{ $client->document_number }}</td>
                        <td>{{ $client->phone ?? '—' }}</td>
                        <td style="text-align:center;font-weight:700;color:var(--blue)">{{ $client->works_count }}</td>
                        <td style="font-family:'JetBrains Mono';color:{{ $pendingBalance > 0 ? 'var(--red)' : 'var(--green)' }}">
                            ${{ number_format($pendingBalance, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Fila expandible (oculta hasta clic) --}}
                    <tr id="client-{{ $client->id }}" style="display:none">
                        <td colspan="5" style="padding:0">
                            <div class="client-expand open">
                                <div class="igrid">
                                    {{-- Fórmula actual --}}
                                    <div class="isec">
                                        <h4>📝 Fórmula Actual</h4>
                                        @if($latestFormula)
                                            <table class="ftable">
                                                <thead><tr><th></th><th>Esf</th><th>Cil</th><th>Eje</th><th>ADD</th><th>DNP</th></tr></thead>
                                                <tbody>
                                                    <tr class="od">
                                                        <td style="font-weight:700;color:var(--cyan)">OD</td>
                                                        <td>{{ $latestFormula->od_sphere !== null ? number_format($latestFormula->od_sphere, 2) : '—' }}</td>
                                                        <td>{{ $latestFormula->od_cylinder !== null ? number_format($latestFormula->od_cylinder, 2) : '—' }}</td>
                                                        <td>{{ $latestFormula->od_axis !== null ? $latestFormula->od_axis . '°' : '—' }}</td>
                                                        <td>{{ $latestFormula->od_add !== null ? number_format($latestFormula->od_add, 2) : '—' }}</td>
                                                        <td>{{ $latestFormula->od_dnp ?? '—' }}</td>
                                                    </tr>
                                                    <tr class="oi">
                                                        <td style="font-weight:700;color:var(--purple)">OI</td>
                                                        <td>{{ $latestFormula->oi_sphere !== null ? number_format($latestFormula->oi_sphere, 2) : '—' }}</td>
                                                        <td>{{ $latestFormula->oi_cylinder !== null ? number_format($latestFormula->oi_cylinder, 2) : '—' }}</td>
                                                        <td>{{ $latestFormula->oi_axis !== null ? $latestFormula->oi_axis . '°' : '—' }}</td>
                                                        <td>{{ $latestFormula->oi_add !== null ? number_format($latestFormula->oi_add, 2) : '—' }}</td>
                                                        <td>{{ $latestFormula->oi_dnp ?? '—' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <p style="font-size:10px;color:var(--text-muted);margin-top:6px">
                                                Examen: {{ $latestFormula->exam_date ? $latestFormula->exam_date->format('d/m/Y') : '—' }}
                                            </p>
                                        @else
                                            <p style="color:var(--text-muted);font-size:12px">Sin fórmula registrada.</p>
                                        @endif
                                    </div>

                                    {{-- Información del cliente --}}
                                    <div class="isec">
                                        <h4>📊 Información</h4>
                                        <div class="irow"><span class="lbl">Email</span><span class="val">{{ $client->email ?? '—' }}</span></div>
                                        <div class="irow"><span class="lbl">Dirección</span><span class="val">{{ $client->address ?? '—' }}</span></div>
                                        <div class="irow">
                                            <span class="lbl">Cumpleaños</span>
                                            <span class="val">
                                                {{ $client->birth_date ? '🎂 ' . $client->birth_date->translatedFormat('d \\d\\e F') : '—' }}
                                            </span>
                                        </div>
                                        <div class="irow">
                                            <span class="lbl">WhatsApp</span>
                                            <span class="val" style="color:{{ $client->whatsapp_authorized ? 'var(--green)' : 'var(--red)' }}">
                                                {{ $client->whatsapp_authorized ? '✅ Autorizado' : '❌ No autorizado' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Historial de trabajos --}}
                                @if($client->works->count() > 0)
                                    <div class="isec" style="margin-bottom:14px">
                                        <h4>📋 Historial de Trabajos</h4>
                                        <table class="tbl" style="font-size:12px">
                                            <thead><tr><th>Código</th><th>Fecha</th><th>Tipo</th><th>Lab</th><th>Estado</th><th>Total</th></tr></thead>
                                            <tbody>
                                                @foreach($client->works as $work)
                                                    <tr onclick="event.stopPropagation();window.location='{{ route('works.show', $work) }}'">
                                                        <td style="font-family:'JetBrains Mono';color:var(--blue);font-size:11px">{{ $work->tracking_code }}</td>
                                                        <td style="font-size:11px">{{ $work->created_at->format('d/m/Y') }}</td>
                                                        <td>{{ $work->lens_type_name }}</td>
                                                        <td>{{ $work->laboratory->name }}</td>
                                                        <td><span class="badge badge-{{ $work->status_color }}">{{ $work->status_emoji }} {{ $work->status_name }}</span></td>
                                                        <td style="font-family:'JetBrains Mono'">${{ number_format($work->price_total, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                {{-- Botones de acción --}}
                                <div style="display:flex;gap:8px">
                                    <a href="{{ route('works.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-p" onclick="event.stopPropagation()">➕ Nuevo Trabajo</a>
                                    @if($client->phone && $client->whatsapp_authorized)
                                        <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $client->phone) }}" target="_blank" class="btn btn-sm btn-g" onclick="event.stopPropagation()">💬 WhatsApp</a>
                                    @endif
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-s" onclick="event.stopPropagation()">✏️ Editar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted)">
                            No se encontraron clientes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($clients->hasPages())
        <div style="margin-top:18px;display:flex;justify-content:center;gap:6px">
            @if($clients->onFirstPage())
                <span class="btn btn-sm btn-s" style="opacity:0.5">← Anterior</span>
            @else
                <a href="{{ $clients->previousPageUrl() }}" class="btn btn-sm btn-s">← Anterior</a>
            @endif
            <span style="padding:8px 14px;font-size:13px;color:var(--text-secondary)">
                Página {{ $clients->currentPage() }} de {{ $clients->lastPage() }}
            </span>
            @if($clients->hasMorePages())
                <a href="{{ $clients->nextPageUrl() }}" class="btn btn-sm btn-s">Siguiente →</a>
            @else
                <span class="btn btn-sm btn-s" style="opacity:0.5">Siguiente →</span>
            @endif
        </div>
    @endif
@endsection

@section('scripts')
<script>
    /**
     * Expandir/colapsar la fila de un cliente
     * Muestra fórmula, historial y botones de acción
     */
    function toggleClient(id) {
        const row = document.getElementById(id);
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
</script>
@endsection