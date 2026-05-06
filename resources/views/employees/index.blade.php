{{--
    Vista: Trabajadoras (Empleadas físicas)
    Ruta: GET /trabajadoras   (solo admin)
    Controlador: EmployeeController@index
--}}

@extends('layouts.app')
@section('title', 'Trabajadoras')

@section('styles')
<style>
    .emp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .emp { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 20px; transition: var(--fast); }
    .emp.inactive { opacity: 0.55; }
    .emp:hover { border-color: var(--purple); transform: translateY(-2px); }
    .emp-top { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .emp-av { width: 48px; height: 48px; border-radius: 50%; background: var(--grad-blue); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #fff; flex-shrink: 0; }
    .emp-info { flex: 1; min-width: 0; }
    .emp-name { font-size: 15px; font-weight: 700; }
    .emp-phone { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
    .emp-status { font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px; }
    .emp-status.on { background: #dcfce7; color: #166534; }
    .emp-status.off { background: #f3f4f6; color: #6b7280; }
    .emp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 12px; }
    .emp-s { background: var(--bg-deep); border-radius: var(--r); padding: 10px; text-align: center; }
    .emp-s .v { font-size: 18px; font-weight: 800; font-family: 'JetBrains Mono'; }
    .emp-s .l { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
    .emp-acts { display: flex; gap: 6px; }
    .emp-edit { flex: 1; padding: 8px; border-radius: var(--r); background: #f0f4ff; color: var(--blue); border: 1px solid #d6dce8; font-family: 'Outfit'; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s ease; text-align: center; text-decoration: none; }
    .emp-edit:hover { background: var(--blue); color: #fff; border-color: var(--blue); }
    .emp-detail { flex: 1; padding: 8px; border-radius: var(--r); background: linear-gradient(135deg, #103192, #1a4fd0); color: #fff; border: 1px solid var(--blue); font-family: 'Outfit'; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s ease; text-align: center; text-decoration: none; }
    .emp-detail:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,49,146,0.25); }
    .emp-help { background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 1px solid #c7d2fe; border-radius: var(--r-md); padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px; font-size: 13px; color: #3730a3; }
    .emp-help span:first-child { font-size: 22px; }
    @media (max-width: 1200px) { .emp-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .emp-grid { grid-template-columns: 1fr; } }

    /* Modal de edición */
    .emp-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(3px); z-index: 200; align-items: center; justify-content: center; }
    .emp-modal.open { display: flex; }
    .emp-modal-box { background: #fff; border-radius: var(--r-lg); padding: 24px; width: 100%; max-width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: empSlide .25s ease; }
    @keyframes empSlide { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
    .emp-modal h3 { font-size: 17px; color: var(--blue); margin-bottom: 16px; }
    .emp-toggle { display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f9fc; border-radius: var(--r); margin-bottom: 14px; }
    .emp-toggle input { width: 18px; height: 18px; }
    .emp-toggle label { font-size: 13px; font-weight: 600; }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>👩‍💼 Trabajadoras</h2>
        <div class="ph-acts">
            <button onclick="document.getElementById('newEmpForm').style.display='block'" class="btn btn-p">➕ Nueva Trabajadora</button>
        </div>
    </div>

    <div class="emp-help">
        <span>💡</span>
        <span>
            Estas son las vendedoras físicas de la óptica. Cuando registres una venta o un pago,
            podrás seleccionar quién la atendió. <strong>No inician sesión por separado</strong> — todas
            usan la misma cuenta <code>trabajadora@universovisual.com</code>.
        </span>
    </div>

    {{-- Formulario nueva trabajadora --}}
    <div id="newEmpForm" style="display:none;margin-bottom:20px">
        <div class="card">
            <div class="card-h"><h3>➕ Nueva Trabajadora</h3></div>
            <div class="card-b">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="frow">
                        <div class="fg">
                            <label>Nombre *</label>
                            <input name="name" required placeholder="Ej: Sofía" maxlength="100">
                            @error('name')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="fg">
                            <label>Teléfono (opcional)</label>
                            <input name="phone" placeholder="3001234567" maxlength="30">
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
                        <button type="button" onclick="document.getElementById('newEmpForm').style.display='none'" class="btn btn-sm btn-s">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-p">💾 Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tarjetas de trabajadoras --}}
    @if($employees->isEmpty())
        <div class="card"><div class="card-b" style="text-align:center;padding:40px;color:var(--text-muted)">
            👋 Aún no hay trabajadoras. Crea la primera con el botón de arriba.
        </div></div>
    @else
        <div class="emp-grid">
            @foreach($employees as $emp)
                <div class="emp {{ $emp->is_active ? '' : 'inactive' }}">
                    <div class="emp-top">
                        <div class="emp-av">{{ $emp->initials ?: '👤' }}</div>
                        <div class="emp-info">
                            <div class="emp-name">{{ $emp->name }}</div>
                            <div class="emp-phone">📞 {{ $emp->phone ?? 'Sin teléfono' }}</div>
                        </div>
                        <span class="emp-status {{ $emp->is_active ? 'on' : 'off' }}">
                            {{ $emp->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <div class="emp-stats">
                        <div class="emp-s">
                            <div class="v" style="color:var(--blue)">{{ $emp->total_works }}</div>
                            <div class="l">Ventas total</div>
                        </div>
                        <div class="emp-s">
                            <div class="v" style="color:var(--yellow)">{{ $emp->active_works }}</div>
                            <div class="l">En curso</div>
                        </div>
                        <div class="emp-s">
                            <div class="v" style="color:var(--green)">{{ $emp->total_payments }}</div>
                            <div class="l">Pagos recib.</div>
                        </div>
                    </div>
                    <div class="emp-acts">
                        <a class="emp-detail" href="{{ route('employees.show', $emp) }}">📋 Ver detalle</a>
                        <button class="emp-edit"
                                data-id="{{ $emp->id }}"
                                data-name="{{ $emp->name }}"
                                data-phone="{{ $emp->phone }}"
                                data-active="{{ $emp->is_active ? '1' : '0' }}"
                                onclick="empOpenEdit(this)">
                            ✏️ Editar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal de edición --}}
    <div class="emp-modal" id="empModal" onclick="if(event.target===this)empCloseEdit()">
        <div class="emp-modal-box">
            <h3>✏️ Editar Trabajadora</h3>
            <form id="empEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="fg">
                    <label>Nombre *</label>
                    <input name="name" id="empEditName" required maxlength="100">
                </div>
                <div class="fg">
                    <label>Teléfono</label>
                    <input name="phone" id="empEditPhone" maxlength="30">
                </div>
                <div class="emp-toggle">
                    <input type="checkbox" id="empEditActive" name="is_active" value="1">
                    <label for="empEditActive">Activa (puede ser seleccionada al registrar ventas)</label>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end">
                    <button type="button" onclick="empCloseEdit()" class="btn btn-sm btn-s">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-p">💾 Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function empOpenEdit(btn) {
    const id = btn.dataset.id;
    const form = document.getElementById('empEditForm');
    form.action = '{{ url("/trabajadoras") }}/' + id;
    document.getElementById('empEditName').value = btn.dataset.name || '';
    document.getElementById('empEditPhone').value = btn.dataset.phone || '';
    document.getElementById('empEditActive').checked = btn.dataset.active === '1';
    document.getElementById('empModal').classList.add('open');
}
function empCloseEdit() {
    document.getElementById('empModal').classList.remove('open');
}
</script>
@endsection
