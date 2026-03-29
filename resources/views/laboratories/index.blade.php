{{--
    Vista: Laboratorios Aliados
    Ruta: GET /laboratorios
    Controlador: LaboratoryController@index
--}}

@extends('layouts.app')
@section('title', 'Laboratorios')

@section('styles')
<style>
    .lab-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .lab { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--r-md); padding: 20px; transition: var(--fast); }
    .lab:hover { border-color: var(--purple); transform: translateY(-2px); }
    .lab-top { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .lab-icon { width: 44px; height: 44px; border-radius: var(--r); background: rgba(124,91,245,0.1); display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .lab-name { font-size: 15px; font-weight: 700; }
    .lab-contact { font-size: 11px; color: var(--text-muted); }
    .lab-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .lab-s { background: var(--bg-deep); border-radius: var(--r); padding: 12px; text-align: center; }
    .lab-s .v { font-size: 18px; font-weight: 800; font-family: 'JetBrains Mono'; }
    .lab-s .l { font-size: 10px; color: var(--text-muted); }
    @media (max-width: 1200px) { .lab-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .lab-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
    <div class="ph">
        <h2>🏭 Laboratorios Aliados</h2>
        <div class="ph-acts">
            <button onclick="document.getElementById('newLabForm').style.display='block'" class="btn btn-p">➕ Nuevo Laboratorio</button>
        </div>
    </div>

    {{-- Formulario nuevo laboratorio (oculto) --}}
    <div id="newLabForm" style="display:none;margin-bottom:20px">
        <div class="card">
            <div class="card-h"><h3>➕ Nuevo Laboratorio</h3></div>
            <div class="card-b">
                <form action="{{ route('laboratories.store') }}" method="POST">
                    @csrf
                    <div class="frow3">
                        <div class="fg"><label>Nombre *</label><input name="name" required placeholder="Servioptica"></div>
                        <div class="fg"><label>Contacto</label><input name="contact_name" placeholder="Carlos Mendoza"></div>
                        <div class="fg"><label>Teléfono</label><input name="phone" placeholder="607-123-4567"></div>
                    </div>
                    <div class="frow3">
                        <div class="fg"><label>Email</label><input name="email" type="email" placeholder="pedidos@lab.com"></div>
                        <div class="fg"><label>Ciudad</label><input name="city" placeholder="Bucaramanga"></div>
                        <div class="fg"><label>Dirección</label><input name="address" placeholder="Cra 27 #36-42"></div>
                    </div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
                        <button type="button" onclick="document.getElementById('newLabForm').style.display='none'" class="btn btn-sm btn-s">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-p">💾 Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tarjetas de laboratorios --}}
    <div class="lab-grid">
        @foreach($laboratories as $lab)
            @php
                $avgDays = $lab->averageDeliveryDays();
                $compliance = $lab->complianceRate();
            @endphp
            <div class="lab">
                <div class="lab-top">
                    <div class="lab-icon">🔬</div>
                    <div>
                        <div class="lab-name">{{ $lab->name }}</div>
                        <div class="lab-contact">📞 {{ $lab->phone ?? '—' }} • {{ $lab->city ?? '—' }}</div>
                    </div>
                </div>
                <div class="lab-stats">
                    <div class="lab-s">
                        <div class="v" style="color:var(--blue)">{{ $lab->active_works_count }}</div>
                        <div class="l">Trabajos Activos</div>
                    </div>
                    <div class="lab-s">
                        <div class="v" style="color:var(--green)">{{ $avgDays ? number_format($avgDays, 1) . 'd' : '—' }}</div>
                        <div class="l">Prom. Entrega</div>
                    </div>
                    <div class="lab-s">
                        <div class="v" style="color:var(--red)">{{ $lab->delayed_works_count }}</div>
                        <div class="l">Demorados</div>
                    </div>
                    <div class="lab-s">
                        <div class="v" style="color:var(--yellow)">{{ $compliance }}%</div>
                        <div class="l">Cumplimiento</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection