{{--
    Vista: Crear Nuevo Trabajo
    Ruta: GET /trabajos/crear
    Controlador: WorkController@create
    
    Formulario completo para crear un trabajo:
    cliente, fórmula, montura, lente, tratamientos, precios, etiquetas.
--}}

@extends('layouts.app')
@section('title', 'Nuevo Trabajo')

@section('content')
    <div class="ph">
        <h2>➕ Nuevo Trabajo</h2>
        <div class="ph-acts">
            <a href="{{ route('works.index') }}" class="btn btn-sm btn-s">← Volver</a>
        </div>
    </div>

    <form action="{{ route('works.store') }}" method="POST">
        @csrf

        <div class="card" style="margin-bottom:20px">
            <div class="card-b">
                {{-- =============================================
                     DATOS DEL CLIENTE
                     ============================================= --}}
                <div class="fsec-title">👤 Datos del Cliente</div>
                <div class="frow">
                    <div class="fg" style="position:relative">
                        <label>Cliente *</label>
                        <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id', $selectedClient->id ?? '') }}" required>
                        <input type="text" id="clientSearch" autocomplete="off"
                               placeholder="Escribe nombre o cédula..."
                               value="{{ $selectedClient ? $selectedClient->full_name . ' — CC ' . $selectedClient->document_number : old('client_name', '') }}"
                               style="width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--r);padding:10px 13px;color:var(--text-primary);font-family:'Outfit';font-size:13.5px;outline:none">
                        <div id="clientResults" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:999;background:var(--bg-card);border:1px solid var(--blue);border-radius:0 0 var(--r) var(--r);max-height:220px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,0.4)"></div>
                        @error('client_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>¿Cliente nuevo?</label>
                        <a href="{{ route('clients.create') }}" class="btn btn-sm btn-s" style="margin-top:4px">➕ Crear cliente primero</a>
                    </div>
                </div>

                {{-- Lista de clientes oculta para búsqueda JS --}}
                @php
                    $clientsJson = $clients->map(function($c) {
                        return [
                            'id' => $c->id,
                            'name' => $c->full_name,
                            'doc' => $c->document_number,
                            'phone' => $c->phone ?? '',
                        ];
                    });
                @endphp
                <script>
                    const allClients = {!! json_encode($clientsJson) !!};
                </script>

                {{-- =============================================
                     FÓRMULA ÓPTICA
                     ============================================= --}}
                <div class="fsec-title">📝 Fórmula Óptica</div>
                <p style="font-size:11px;color:var(--text-muted);margin-bottom:10px">OD = Ojo Derecho | OI = Ojo Izquierdo</p>
                
                {{-- OD --}}
                <div style="display:grid;grid-template-columns:50px 1fr 1fr 1fr 1fr 1fr;gap:8px;margin-bottom:10px">
                    <div style="display:flex;align-items:center;font-weight:700;color:var(--cyan);font-size:14px">OD</div>
                    <div class="fg"><label>Esfera</label><input name="od_sphere" type="number" step="0.25" value="{{ old('od_sphere') }}" placeholder="-2.25"></div>
                    <div class="fg"><label>Cilindro</label><input name="od_cylinder" type="number" step="0.25" value="{{ old('od_cylinder') }}" placeholder="-0.75"></div>
                    <div class="fg"><label>Eje</label><input name="od_axis" type="number" step="1" min="0" max="180" value="{{ old('od_axis') }}" placeholder="180"></div>
                    <div class="fg"><label>ADD</label><input name="od_add" type="number" step="0.25" value="{{ old('od_add') }}" placeholder="+2.00"></div>
                    <div class="fg"><label>DNP</label><input name="od_dnp" type="number" step="0.5" value="{{ old('od_dnp') }}" placeholder="32"></div>
                </div>

                {{-- OI --}}
                <div style="display:grid;grid-template-columns:50px 1fr 1fr 1fr 1fr 1fr;gap:8px;margin-bottom:10px">
                    <div style="display:flex;align-items:center;font-weight:700;color:var(--purple);font-size:14px">OI</div>
                    <div class="fg"><label>Esfera</label><input name="oi_sphere" type="number" step="0.25" value="{{ old('oi_sphere') }}" placeholder="-1.75"></div>
                    <div class="fg"><label>Cilindro</label><input name="oi_cylinder" type="number" step="0.25" value="{{ old('oi_cylinder') }}" placeholder="-0.50"></div>
                    <div class="fg"><label>Eje</label><input name="oi_axis" type="number" step="1" min="0" max="180" value="{{ old('oi_axis') }}" placeholder="175"></div>
                    <div class="fg"><label>ADD</label><input name="oi_add" type="number" step="0.25" value="{{ old('oi_add') }}" placeholder="+2.00"></div>
                    <div class="fg"><label>DNP</label><input name="oi_dnp" type="number" step="0.5" value="{{ old('oi_dnp') }}" placeholder="31"></div>
                </div>

                <div class="fg" style="max-width:200px">
                    <label>Fecha del examen</label>
                    <input name="exam_date" type="date" value="{{ old('exam_date', date('Y-m-d')) }}">
                </div>

                {{-- =============================================
                     MONTURA Y LENTE
                     ============================================= --}}
                <div class="fsec-title">👓 Montura y Lente</div>
                <div class="frow">
                    <div class="fg">
                        <label>Montura *</label>
                        <select name="frame_type" required>
                            <option value="own" {{ old('frame_type') == 'own' ? 'selected' : '' }}>Propia del cliente</option>
                            <option value="purchased" {{ old('frame_type') == 'purchased' ? 'selected' : '' }}>Comprada en óptica</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Marca / Referencia</label>
                        <input name="frame_brand" value="{{ old('frame_brand') }}" placeholder="Ray-Ban RB5154">
                    </div>
                </div>

                <div class="frow3">
                    <div class="fg">
                        <label>Tipo de Lente *</label>
                        <select name="lens_type" required>
                            <option value="monofocal" {{ old('lens_type') == 'monofocal' ? 'selected' : '' }}>Monofocal</option>
                            <option value="bifocal" {{ old('lens_type') == 'bifocal' ? 'selected' : '' }}>Bifocal</option>
                            <option value="progressive" {{ old('lens_type') == 'progressive' ? 'selected' : '' }}>Progresivo</option>
                        </select>
                        @error('lens_type') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>Material *</label>
                        <select name="lens_material" required>
                            <option value="cr39" {{ old('lens_material') == 'cr39' ? 'selected' : '' }}>CR-39</option>
                            <option value="polycarbonate" {{ old('lens_material') == 'polycarbonate' ? 'selected' : '' }}>Policarbonato</option>
                            <option value="high_index" {{ old('lens_material') == 'high_index' ? 'selected' : '' }}>Alto Índice</option>
                            <option value="trivex" {{ old('lens_material') == 'trivex' ? 'selected' : '' }}>Trivex</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Laboratorio *</label>
                        <select name="laboratory_id" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->id }}" {{ old('laboratory_id') == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->name }} — {{ $lab->city }}
                                </option>
                            @endforeach
                        </select>
                        @error('laboratory_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Tratamientos --}}
                <div class="fg">
                    <label>Tratamientos</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:5px">
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_antireflective" value="1" {{ old('treatment_antireflective') ? 'checked' : '' }}> Antirreflejo
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_photochromic" value="1" {{ old('treatment_photochromic') ? 'checked' : '' }}> Fotocromático
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_blue_filter" value="1" {{ old('treatment_blue_filter') ? 'checked' : '' }}> Filtro Azul
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_polarized" value="1" {{ old('treatment_polarized') ? 'checked' : '' }}> Polarizado
                        </label>
                    </div>
                </div>

                {{-- =============================================
                     PRECIOS
                     ============================================= --}}
                <div class="fsec-title">💰 Precios</div>
                <div class="frow3">
                    <div class="fg">
                        <label>Lentes ($) *</label>
                        <input name="price_lenses" type="number" step="1" value="{{ old('price_lenses') }}" placeholder="250000" required>
                        @error('price_lenses') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>Montura ($)</label>
                        <input name="price_frame" type="number" step="1" value="{{ old('price_frame', 0) }}" placeholder="100000">
                    </div>
                    <div class="fg">
                        <label>Consulta ($)</label>
                        <input name="price_consultation" type="number" step="1" value="{{ old('price_consultation', 0) }}" placeholder="0">
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Abono inicial ($)</label>
                        <input name="initial_payment" type="number" step="1" value="{{ old('initial_payment') }}" placeholder="200000">
                    </div>
                    <div class="fg">
                        <label>Método de pago</label>
                        <select name="payment_method">
                            <option value="cash">💵 Efectivo</option>
                            <option value="card">💳 Tarjeta</option>
                            <option value="transfer">🏦 Transferencia</option>
                            <option value="nequi">📱 Nequi</option>
                            <option value="daviplata">📱 Daviplata</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                </div>

                {{-- =============================================
                     ETIQUETAS Y EXTRAS
                     ============================================= --}}
                <div class="fsec-title">⭐ Etiquetas</div>
                <div style="display:flex;gap:8px;margin-bottom:16px">
                    <label style="display:flex;align-items:center;gap:5px;background:rgba(239,68,68,0.06);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid rgba(239,68,68,0.15);color:var(--red)">
                        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }}> 🔥 Urgente
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;background:rgba(255,193,7,0.06);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid rgba(255,193,7,0.15);color:var(--yellow)">
                        <input type="checkbox" name="is_vip" value="1" {{ old('is_vip') ? 'checked' : '' }}> ⭐ VIP
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;background:rgba(124,91,245,0.06);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid rgba(124,91,245,0.15);color:var(--purple)">
                        <input type="checkbox" name="is_warranty" value="1" {{ old('is_warranty') ? 'checked' : '' }}> 🔄 Garantía
                    </label>
                </div>

                <div class="fg">
                    <label>Fecha estimada de entrega</label>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input name="estimated_delivery" id="estimatedDate" type="date" 
                               value="{{ old('estimated_delivery', now()->addDays(8)->format('Y-m-d')) }}" 
                               style="max-width:220px" readonly>
                        <button type="button" id="editDateBtn" onclick="enableDateEdit()" 
                                class="btn btn-xs btn-s">✏️ Editar</button>
                        <span id="dateDaysLabel" style="font-size:11px;color:var(--text-muted)"></span>
                    </div>
                </div>

                {{-- =============================================
                     OBSERVACIONES
                     ============================================= --}}
                <div class="fsec-title">📝 Observaciones</div>
                <div class="fg">
                    <textarea name="observations" rows="3" 
                              style="resize:vertical;width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--r);padding:10px 13px;color:var(--text-primary);font-family:'Outfit';font-size:13.5px"
                              placeholder="Notas adicionales del trabajo... ej: cliente solicita que queden bien centrados, montura propia en buen estado, etc.">{{ old('observations') }}</textarea>
                </div>

                {{-- Botones --}}
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
                    <a href="{{ route('works.index') }}" class="btn btn-s">Cancelar</a>
                    <button type="submit" class="btn btn-p">💾 Guardar Trabajo</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
<script>
    /**
     * =============================================
     * BUSCADOR DE CLIENTES EN TIEMPO REAL
     * =============================================
     * A medida que el usuario escribe, filtra la lista
     * de clientes por nombre o cédula y muestra resultados.
     */
    const searchInput = document.getElementById('clientSearch');
    const resultsDiv = document.getElementById('clientResults');
    const hiddenInput = document.getElementById('client_id');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        const filtered = allClients.filter(c => 
            c.name.toLowerCase().includes(query) || 
            c.doc.includes(query) ||
            c.phone.includes(query)
        ).slice(0, 8); // Máximo 8 resultados

        if (filtered.length === 0) {
            resultsDiv.innerHTML = '<div style="padding:12px 14px;color:var(--text-muted);font-size:12px">No se encontraron clientes. <a href="{{ route("clients.create") }}" style="color:var(--blue)">Crear nuevo →</a></div>';
            resultsDiv.style.display = 'block';
            return;
        }

        resultsDiv.innerHTML = filtered.map(c => `
            <div onclick="selectClient(${c.id}, '${c.name.replace(/'/g, "\\'")}', '${c.doc}')" 
                 style="padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--border);transition:.15s ease;font-size:13px"
                 onmouseover="this.style.background='var(--bg-hover)'"
                 onmouseout="this.style.background='transparent'">
                <strong>${c.name}</strong>
                <span style="color:var(--text-muted);font-size:11px;margin-left:8px">CC ${c.doc}</span>
                ${c.phone ? '<span style="color:var(--text-muted);font-size:11px;margin-left:8px">📱 ' + c.phone + '</span>' : ''}
            </div>
        `).join('');
        resultsDiv.style.display = 'block';
    });

    // Seleccionar un cliente de la lista
    function selectClient(id, name, doc) {
        hiddenInput.value = id;
        searchInput.value = name + ' — CC ' + doc;
        resultsDiv.style.display = 'none';
        searchInput.style.borderColor = 'var(--green)';
        setTimeout(() => searchInput.style.borderColor = 'var(--border)', 1500);
    }

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    // Si se borra el texto, limpiar la selección
    searchInput.addEventListener('blur', function() {
        setTimeout(() => {
            if (!hiddenInput.value && searchInput.value) {
                searchInput.value = '';
            }
        }, 200);
    });

    /**
     * =============================================
     * FECHA ESTIMADA DE ENTREGA
     * =============================================
     * Por defecto se calcula hoy + 8 días.
     * El campo está bloqueado hasta que presionen "Editar".
     */
    function enableDateEdit() {
        const dateInput = document.getElementById('estimatedDate');
        const btn = document.getElementById('editDateBtn');
        
        if (dateInput.readOnly) {
            dateInput.readOnly = false;
            dateInput.style.borderColor = 'var(--blue)';
            dateInput.focus();
            btn.innerHTML = '✅ Listo';
            btn.className = 'btn btn-xs btn-e';
        } else {
            dateInput.readOnly = true;
            dateInput.style.borderColor = 'var(--border)';
            btn.innerHTML = '✏️ Editar';
            btn.className = 'btn btn-xs btn-s';
        }
        updateDaysLabel();
    }

    // Mostrar cuántos días faltan
    function updateDaysLabel() {
        const dateInput = document.getElementById('estimatedDate');
        const label = document.getElementById('dateDaysLabel');
        if (dateInput.value) {
            const today = new Date();
            today.setHours(0,0,0,0);
            const target = new Date(dateInput.value + 'T00:00:00');
            const diff = Math.ceil((target - today) / (1000 * 60 * 60 * 24));
            label.textContent = diff > 0 ? `(${diff} días)` : diff === 0 ? '(Hoy)' : `(${Math.abs(diff)} días atrás)`;
        }
    }

    document.getElementById('estimatedDate').addEventListener('change', updateDaysLabel);
    updateDaysLabel(); // Calcular al cargar
</script>
@endsection