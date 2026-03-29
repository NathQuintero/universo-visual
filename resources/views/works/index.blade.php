{{--
    Vista: Listado de Trabajos
    Ruta: GET /trabajos
    Controlador: WorkController@index
    
    Muestra la tabla de todos los trabajos con filtros por estado,
    laboratorio y búsqueda. Diseño tipo tabla (no kanban).
--}}

@extends('layouts.app')
@section('title', 'Trabajos')

@section('content')
    {{-- Header de la página --}}
    <div class="ph">
        <h2>👓 Gestión de Trabajos</h2>
        <div class="ph-acts">
            <a href="{{ route('works.create') }}" class="btn btn-p">➕ Nuevo Trabajo</a>
        </div>
    </div>

    {{-- Filtros: buscador + combos --}}
    <div class="filters-row">
        <form action="{{ route('works.index') }}" method="GET" class="search-box" style="width:300px">
            <span>🔍</span>
            <input name="search" placeholder="Buscar por código, nombre o cédula..." 
                   value="{{ request('search') }}">
        </form>

        <form id="filterForm" action="{{ route('works.index') }}" method="GET" style="display:flex;gap:9px;align-items:center">
            {{-- Mantener búsqueda activa --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <select name="status" class="combo" onchange="document.getElementById('filterForm').submit()">
                <option value="">Todos los estados</option>
                <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>📝 Registrado</option>
                <option value="sent_to_lab" {{ request('status') == 'sent_to_lab' ? 'selected' : '' }}>📦 Enviado al Lab</option>
                <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>🔬 En Proceso</option>
                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>📬 Recibido</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>✅ Listo para Entregar</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>🎉 Entregado</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Cancelado</option>
            </select>

            <select name="laboratory" class="combo" onchange="document.getElementById('filterForm').submit()">
                <option value="">Todos los laboratorios</option>
                @foreach($laboratories as $lab)
                    <option value="{{ $lab->id }}" {{ request('laboratory') == $lab->id ? 'selected' : '' }}>
                        {{ $lab->name }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- Botón limpiar filtros --}}
        @if(request()->hasAny(['search', 'status', 'laboratory']))
            <a href="{{ route('works.index') }}" class="btn btn-sm btn-s">✕ Limpiar</a>
        @endif
    </div>

    {{-- Tabla de trabajos --}}
    <div class="card">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Tipo Lente</th>
                    <th>Laboratorio</th>
                    <th>Estado</th>
                    <th>Días</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($works as $work)
                    <tr onclick="window.location='{{ route('works.show', $work) }}'">
                        {{-- Código de seguimiento --}}
                        <td style="font-family:'JetBrains Mono';color:var(--blue);font-weight:600;font-size:12px">
                            {{ $work->tracking_code }}
                        </td>

                        {{-- Cliente --}}
                        <td>
                            <strong>{{ $work->client->full_name }}</strong><br>
                            <span style="font-size:11px;color:var(--text-muted)">
                                CC {{ $work->client->document_number }}
                            </span>
                        </td>

                        {{-- Tipo de lente + tratamientos --}}
                        <td>
                            {{ $work->lens_type_name }}<br>
                            <span style="font-size:11px;color:var(--text-muted)">
                                {{ $work->treatments_text }}
                            </span>
                        </td>

                        {{-- Laboratorio --}}
                        <td>{{ $work->laboratory->name }}</td>

                        {{-- Estado con badge de color --}}
                        <td>
                            <span class="badge badge-{{ $work->status_color }}">
                                {{ $work->status_emoji }} {{ $work->status_name }}
                            </span>
                            @if($work->is_urgent)
                                <span class="badge badge-red" style="margin-left:4px">🔥</span>
                            @endif
                            @if($work->is_vip)
                                <span class="badge badge-yellow" style="margin-left:4px">⭐</span>
                            @endif
                        </td>

                        {{-- Días transcurridos --}}
                        <td style="font-family:'JetBrains Mono';text-align:center;{{ $work->is_delayed ? 'color:var(--red);font-weight:700' : '' }}">
                            {{ $work->days_elapsed }}<br>
                            <span style="font-size:10px;{{ $work->is_delayed ? '' : 'color:var(--text-muted)' }}">
                                días{{ $work->is_delayed ? ' ⚠️' : '' }}
                            </span>
                        </td>

                        {{-- Botones de acción --}}
                        <td>
                            <div style="display:flex;gap:5px">
                                <a href="{{ route('works.show', $work) }}" 
                                   class="btn btn-xs btn-s" 
                                   onclick="event.stopPropagation()" title="Ver detalle">👁️</a>
                                
                                @if($work->client->phone && $work->client->whatsapp_authorized)
                                    <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $work->client->phone) }}"
                                       target="_blank" class="btn btn-xs btn-s"
                                       onclick="event.stopPropagation()" title="WhatsApp">💬</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">
                            No se encontraron trabajos con esos filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($works->hasPages())
        <div style="margin-top:18px;display:flex;justify-content:center;gap:6px">
            @if($works->onFirstPage())
                <span class="btn btn-sm btn-s" style="opacity:0.5">← Anterior</span>
            @else
                <a href="{{ $works->previousPageUrl() }}" class="btn btn-sm btn-s">← Anterior</a>
            @endif

            <span style="padding:8px 14px;font-size:13px;color:var(--text-secondary)">
                Página {{ $works->currentPage() }} de {{ $works->lastPage() }}
            </span>

            @if($works->hasMorePages())
                <a href="{{ $works->nextPageUrl() }}" class="btn btn-sm btn-s">Siguiente →</a>
            @else
                <span class="btn btn-sm btn-s" style="opacity:0.5">Siguiente →</span>
            @endif
        </div>
    @endif
@endsection