{{--
    Vista: Dashboard Principal
    Ruta: GET /dashboard
    Controlador: DashboardController@index
    
    Muestra: estadísticas, acciones rápidas, trabajos recientes,
    alertas inteligentes, cumpleañeros, resumen del día.
--}}

@extends('layouts.app')
@section('title', 'Inicio')

@section('content')
    {{-- =============================================
         BANNER DE ALERTA (si hay trabajos demorados)
         ============================================= --}}
    @if($stats['delayed_works'] > 0)
        <div class="alert-banner">
            <span style="font-size:19px">⚠️</span>
            <span class="a-text">
                <strong>{{ $stats['delayed_works'] }} trabajo(s) demorado(s)</strong> 
                — Gafas con más de 5 días en laboratorio sin actualización.
            </span>
            <a href="{{ route('works.index', ['status' => 'in_process']) }}" class="a-btn">Ver trabajos →</a>
        </div>
    @endif

    {{-- =============================================
         TARJETAS DE ESTADÍSTICAS
         ============================================= --}}
    <div class="stats">
        <a href="{{ route('works.index') }}" class="stat s-blue" style="text-decoration:none;color:inherit">
            <div class="stat-top">
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-val">{{ $stats['active_works'] }}</div>
            <div class="stat-label">Trabajos Activos</div>
        </a>
        
        <a href="{{ route('works.index', ['status' => 'ready']) }}" class="stat s-green" style="text-decoration:none;color:inherit">
            <div class="stat-top">
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-val">{{ $stats['ready_works'] }}</div>
            <div class="stat-label">Listos para Entregar</div>
        </a>
        
        <a href="{{ route('works.index', ['status' => 'sent_to_lab']) }}" class="stat s-yellow" style="text-decoration:none;color:inherit">
            <div class="stat-top">
                <div class="stat-icon">🔬</div>
            </div>
            <div class="stat-val">{{ $stats['in_lab_works'] }}</div>
            <div class="stat-label">En Laboratorio</div>
        </a>
        
        <div class="stat s-red">
            <div class="stat-top">
                <div class="stat-icon">⏰</div>
            </div>
            <div class="stat-val">{{ $stats['delayed_works'] }}</div>
            <div class="stat-label">Demorados</div>
        </div>
        
        <div class="stat s-purple">
            <div class="stat-top">
                <div class="stat-icon">💰</div>
            </div>
            <div class="stat-val">${{ number_format($stats['monthly_income'] / 1000, 0, ',', '.') }}K</div>
            <div class="stat-label">Ingresos del Mes</div>
        </div>
    </div>

    {{-- =============================================
         ACCIONES RÁPIDAS
         ============================================= --}}
    <div class="quick-acts">
        <a class="qbtn" href="{{ route('works.create') }}">
            <div class="qbtn-icon">➕</div>
            <div><h4>Nuevo Trabajo</h4><p>Crear orden de pedido</p></div>
        </a>
        <a class="qbtn" href="{{ route('clients.index') }}">
            <div class="qbtn-icon">👤</div>
            <div><h4>Buscar Cliente</h4><p>Por nombre o cédula</p></div>
        </a>
        <a class="qbtn" href="{{ route('clients.create') }}">
            <div class="qbtn-icon">👥</div>
            <div><h4>Nuevo Cliente</h4><p>Registrar paciente</p></div>
        </a>
        <a class="qbtn" href="{{ Auth::user()->isAdmin() ? route('reports.index') : route('works.index') }}">
            <div class="qbtn-icon">📊</div>
            <div><h4>{{ Auth::user()->isAdmin() ? 'Ver Reportes' : 'Ver Trabajos' }}</h4><p>{{ Auth::user()->isAdmin() ? 'Estadísticas del negocio' : 'Todos los trabajos' }}</p></div>
        </a>
    </div>

    {{-- =============================================
         CONTENIDO EN 2 COLUMNAS
         ============================================= --}}
    <div class="cgrid">
        {{-- COLUMNA IZQUIERDA: Trabajos recientes --}}
        <div class="card">
            <div class="card-h">
                <h3>📋 Trabajos Recientes</h3>
                <a href="{{ route('works.index') }}" class="card-act">Ver todos →</a>
            </div>
            <div class="card-b">
                @forelse($recentWorks as $work)
                    <a href="{{ route('works.show', $work) }}" class="wi">
                        <div class="dot dot-{{ $work->status_color }}"></div>
                        <div class="wi-info">
                            <h4>{{ $work->client->full_name }}</h4>
                            <p>{{ $work->tracking_code }} • {{ $work->lens_type_name }} • {{ $work->laboratory->name }}</p>
                        </div>
                        <span class="badge badge-{{ $work->status_color }}">
                            {{ $work->status_emoji }} {{ $work->status_name }}
                        </span>
                        <span class="wi-days {{ $work->is_delayed ? 'delayed' : '' }}">
                            {{ $work->days_elapsed }}d{{ $work->is_delayed ? ' ⚠️' : '' }}
                        </span>
                    </a>
                @empty
                    <p style="color:var(--text-muted);padding:20px 0;text-align:center">
                        No hay trabajos registrados aún.
                    </p>
                @endforelse
            </div>
        </div>

        {{-- COLUMNA DERECHA: Alertas + Cumpleaños + Resumen --}}
        <div style="display:flex;flex-direction:column;gap:16px">
            {{-- Alertas --}}
            <div class="card">
                <div class="card-h">
                    <h3>🔔 Alertas</h3>
                </div>
                <div class="card-b">
                    @forelse($alerts as $alert)
                        <div class="ai">
                            <div class="ai-icon {{ $alert['type'] }}">{{ $alert['icon'] }}</div>
                            <div>
                                <h4>{{ $alert['title'] }}</h4>
                                <p>{{ $alert['message'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p style="color:var(--text-muted);text-align:center;padding:12px 0">
                            ✨ ¡Todo al día! Sin alertas pendientes.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Cumpleaños --}}
            @if($birthdayClients->count() > 0)
                <div class="card">
                    <div class="card-h">
                        <h3>🎂 Cumpleaños esta semana</h3>
                    </div>
                    <div class="card-b">
                        @foreach($birthdayClients->take(3) as $client)
                            <div class="bi">
                                <div class="bi-av">{{ $client->initials }}</div>
                                <div class="bi-info">
                                    <h4>{{ $client->full_name }}</h4>
                                    <p>
                                        {{ $client->birth_date->translatedFormat('d M') }}
                                        —
                                        @if($client->isBirthdayToday())
                                            ¡Hoy! 🎉
                                        @else
                                            En {{ $client->daysUntilBirthday() }} día(s)
                                        @endif
                                    </p>
                                </div>
                                @if($client->phone && $client->whatsapp_authorized)
                                    @php
                                    $msgCumple = "Hola " . $client->first_name . "!\n\n"
                                        . "Hoy es un dia muy especial!! Desde Optica Universo Visual queremos desearte un muy Feliz Cumpleaños! " . "\n\n"
                                        . "Que Dios te bendiga grandemente, te llene de salud, amor y muchas bendiciones en este nuevo año de vida. " . "\n\n"
                                        . "Para celebrar contigo, te regalamos un 15% de descuento en tu proxima compra, valido por dos días desde tu cumpleaños" . "\n\n"
                                        . "Ven a visitarnos y estrena con estilo! Te esperamos con los brazos abiertos." . "\n\n"
                                        . "Con carino,\n"
                                        . "Tu familia de Optica Universo Visual\n"
                                        . "C.C. La Isla, Bucaramanga";
                                    @endphp
                                       target="_blank" class="bi-btn">💬 Felicitar</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Resumen del día --}}
            <div class="card">
                <div class="card-h">
                    <h3>📊 Resumen — Hoy</h3>
                </div>
                <div class="card-b">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;text-align:center">
                        <div>
                            <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono';color:var(--blue)">
                                {{ $todaySummary['works_created'] }}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted)">Ingresados</div>
                        </div>
                        <div>
                            <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono';color:var(--green)">
                                {{ $todaySummary['works_delivered'] }}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted)">Entregados</div>
                        </div>
                        <div>
                            <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono';color:var(--purple)">
                                ${{ number_format($todaySummary['today_income'] / 1000, 0, ',', '.') }}K
                            </div>
                            <div style="font-size:11px;color:var(--text-muted)">Ingresos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection