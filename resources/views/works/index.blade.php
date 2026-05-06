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
            @if(Auth::user()->isAdmin())
                <a href="{{ route('works.export', request()->query()) }}" class="btn btn-g btn-sm">📥 Exportar Excel</a>
                <button type="button" class="btn btn-p btn-sm" onclick="document.getElementById('importModal').style.display='flex'">📤 Importar Excel</button>
            @endif
            <a href="{{ route('works.create') }}" class="btn btn-p">➕ Nuevo Trabajo</a>
        </div>
    </div>

    {{-- Filtros: buscador + combos --}}
    <div class="filters-row">
        <form action="{{ route('works.index') }}" method="GET" class="search-box" style="width:100%;max-width:300px">
            <span>🔍</span>
            <input name="search" placeholder="Buscar por código, nombre o cédula..."
                   value="{{ request('search') }}">
        </form>

        <form id="filterForm" action="{{ route('works.index') }}" method="GET" style="display:flex;gap:9px;align-items:center;flex-wrap:wrap">
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
        <div class="table-wrap">
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
    </div>

    {{-- Paginación --}}
    @if($works->hasPages())
        <div style="margin-top:18px;display:flex;justify-content:center;gap:6px;flex-wrap:wrap">
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

    {{-- Modal de importación --}}
    @if(Auth::user()->isAdmin())
    <div id="importModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:20px">
        <div style="background:#fff;border-radius:16px;max-width:680px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 12px 40px rgba(16,49,146,0.2)">
            {{-- Header del modal --}}
            <div style="padding:24px 28px;border-bottom:1px solid #edf0f7;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(135deg,#f8f9ff,#f0f4ff);border-radius:16px 16px 0 0">
                <h3 style="font-size:18px;font-weight:700;color:#103192;margin:0">📤 Importar Trabajos desde Excel</h3>
                <button onclick="cerrarModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af;padding:4px">✕</button>
            </div>

            {{-- Cuerpo del modal --}}
            <div style="padding:28px" id="importBody">
                <p style="color:#4a5568;font-size:14px;line-height:1.7;margin-bottom:20px">
                    Sube el archivo Excel exportado con los trabajos nuevos. El sistema detectará automáticamente cuáles ya existen y solo importará los nuevos.
                </p>

                {{-- Zona de archivo --}}
                <div id="dropZone" style="border:2px dashed #c7d2fe;border-radius:12px;padding:32px;text-align:center;cursor:pointer;transition:all 0.3s;background:#fafbff"
                     onclick="document.getElementById('importFile').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#103192';this.style.background='#f0f4ff'"
                     ondragleave="this.style.borderColor='#c7d2fe';this.style.background='#fafbff'"
                     ondrop="event.preventDefault();this.style.borderColor='#c7d2fe';document.getElementById('importFile').files=event.dataTransfer.files;mostrarArchivo()">
                    <p style="font-size:32px;margin-bottom:8px">📁</p>
                    <p style="font-size:14px;color:#103192;font-weight:600" id="fileName">Arrastra tu archivo .xlsx aquí o haz clic para seleccionar</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:4px">Solo archivos Excel (.xlsx)</p>
                </div>
                <input type="file" id="importFile" accept=".xlsx,.xls" style="display:none" onchange="mostrarArchivo()">

                {{-- Botón analizar --}}
                <button type="button" id="btnAnalizar" onclick="analizarArchivo()" class="btn btn-p" style="width:100%;margin-top:16px;justify-content:center;display:none">
                    Analizar archivo
                </button>

                {{-- Resultados del análisis --}}
                <div id="analysisResult" style="display:none;margin-top:20px"></div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
    function cerrarModal() {
        document.getElementById('importModal').style.display = 'none';
        document.getElementById('importBody').querySelector('#analysisResult').style.display = 'none';
        document.getElementById('importBody').querySelector('#analysisResult').innerHTML = '';
        document.getElementById('importFile').value = '';
        document.getElementById('fileName').textContent = 'Arrastra tu archivo .xlsx aquí o haz clic para seleccionar';
        document.getElementById('btnAnalizar').style.display = 'none';
    }

    function mostrarArchivo() {
        const file = document.getElementById('importFile').files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('btnAnalizar').style.display = 'flex';
            document.getElementById('analysisResult').style.display = 'none';
        }
    }

    function analizarArchivo() {
        const file = document.getElementById('importFile').files[0];
        if (!file) return;

        const btn = document.getElementById('btnAnalizar');
        btn.innerHTML = 'Analizando...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('file', file);

        fetch('{{ route("works.analyzeImport") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            btn.innerHTML = 'Analizar archivo';
            btn.disabled = false;

            if (!ok || !data.success) {
                mostrarError(data.message || 'Error al analizar');
                return;
            }

            mostrarAnalisis(data.data);
        })
        .catch(() => {
            btn.innerHTML = 'Analizar archivo';
            btn.disabled = false;
            mostrarError('Error de conexión');
        });
    }

    function mostrarError(msg) {
        const div = document.getElementById('analysisResult');
        div.style.display = 'block';
        div.innerHTML = `<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:14px;color:#991b1b;font-size:13px">${msg}</div>`;
    }

    function mostrarAnalisis(d) {
        const div = document.getElementById('analysisResult');
        div.style.display = 'block';

        let html = `<div style="background:#f5f7fc;border-radius:12px;padding:20px;border:1px solid #e8edff">`;
        html += `<h4 style="font-size:15px;font-weight:700;color:#103192;margin-bottom:14px">Resultado del análisis</h4>`;
        html += `<p style="font-size:13px;color:#4a5568;margin-bottom:8px">Se encontraron <strong>${d.total_rows}</strong> filas en el archivo</p>`;

        if (d.existing_count > 0) {
            html += `<p style="font-size:13px;color:#16a34a;margin-bottom:4px">✅ <strong>${d.existing_count}</strong> trabajo(s) ya existen (se omitirán)</p>`;
            html += `<div style="font-size:11px;color:#6c757d;margin-left:20px;margin-bottom:8px">${d.existing.join(', ')}</div>`;
        }

        if (d.new_works_count > 0) {
            html += `<p style="font-size:13px;color:#103192;font-weight:600;margin-bottom:4px">🆕 <strong>${d.new_works_count}</strong> trabajo(s) nuevo(s) para importar:</p>`;
            html += `<div style="margin-left:20px;margin-bottom:8px">`;
            d.new_works.forEach(w => {
                html += `<div style="font-size:12px;color:#4a5568;padding:3px 0">• ${w.client} — ${w.lens} — $${Number(w.total).toLocaleString()}</div>`;
            });
            html += `</div>`;
        }

        if (d.new_clients_count > 0) {
            html += `<p style="font-size:13px;color:#d97706;margin-bottom:4px">👤 <strong>${d.new_clients_count}</strong> cliente(s) nuevo(s) se crearán:</p>`;
            html += `<div style="font-size:11px;color:#6c757d;margin-left:20px;margin-bottom:8px">${d.new_clients.join(', ')}</div>`;
        }

        if (d.existing_clients_count > 0) {
            html += `<p style="font-size:13px;color:#6c757d;margin-bottom:4px">👤 <strong>${d.existing_clients_count}</strong> cliente(s) existente(s) se reutilizarán</p>`;
        }

        if (d.errors_count > 0) {
            html += `<p style="font-size:13px;color:#dc2626;margin-top:8px;margin-bottom:4px">⚠️ <strong>${d.errors_count}</strong> fila(s) con errores:</p>`;
            html += `<div style="margin-left:20px;margin-bottom:8px">`;
            d.errors.forEach(e => {
                html += `<div style="font-size:11px;color:#dc2626;padding:2px 0">• ${e}</div>`;
            });
            html += `</div>`;
        }

        html += `</div>`;

        if (d.new_works_count > 0) {
            html += `<div style="display:flex;gap:10px;margin-top:16px">`;
            html += `<button type="button" onclick="confirmarImportacion()" class="btn btn-p" style="flex:1;justify-content:center" id="btnConfirmar">✅ Confirmar importación</button>`;
            html += `<button type="button" onclick="cerrarModal()" class="btn btn-s" style="flex:1;justify-content:center">❌ Cancelar</button>`;
            html += `</div>`;
        } else {
            html += `<p style="text-align:center;color:#6c757d;font-size:13px;margin-top:16px">No hay trabajos nuevos para importar.</p>`;
        }

        div.innerHTML = html;
    }

    function confirmarImportacion() {
        const file = document.getElementById('importFile').files[0];
        if (!file) return;

        const btn = document.getElementById('btnConfirmar');
        btn.innerHTML = 'Importando...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('file', file);

        fetch('{{ route("works.import") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                const div = document.getElementById('analysisResult');
                div.innerHTML = `
                    <div style="background:#dcfce7;border:1px solid #86efac;border-left:4px solid #16a34a;border-radius:10px;padding:20px;text-align:center">
                        <p style="font-size:20px;margin-bottom:8px">✅</p>
                        <p style="font-size:15px;font-weight:700;color:#155724">${data.message}</p>
                    </div>
                    <button type="button" onclick="window.location.reload()" class="btn btn-p" style="width:100%;margin-top:14px;justify-content:center">Cerrar y actualizar</button>
                `;
            } else {
                mostrarError(data.message || 'Error al importar');
                btn.innerHTML = '✅ Confirmar importación';
                btn.disabled = false;
            }
        })
        .catch(() => {
            mostrarError('Error de conexión');
            btn.innerHTML = '✅ Confirmar importación';
            btn.disabled = false;
        });
    }
</script>
@endsection