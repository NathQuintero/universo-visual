<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Universo Visual') — Sistema de Gestión</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /**
         * =============================================
         * ESTILOS GLOBALES — UNIVERSO VISUAL
         * =============================================
         * Tema oscuro con azules y morados.
         * Fuente principal: Outfit (legible, moderna)
         * Fuente monospace: JetBrains Mono (códigos, números)
         * Tamaño base: 14px (ligeramente más grande para mejor lectura)
         */
        :root {
            /* Fondos */
            --bg-deep: #080c18;
            --bg-primary: #0c1122;
            --bg-card: #111827;
            --bg-hover: #162036;
            --bg-elevated: #1a2540;
            --bg-input: #0e1529;
            
            /* Bordes */
            --border: #1e2a4a;
            --border-light: #263354;
            
            /* Textos */
            --text-primary: #e4e9f2;
            --text-secondary: #8490ab;
            --text-muted: #556078;
            
            /* Colores de acento */
            --blue: #4a6cf7;
            --purple: #7c5bf5;
            --cyan: #00d4ff;
            --green: #00e676;
            --yellow: #ffc107;
            --orange: #ff9100;
            --red: #ef4444;
            --pink: #ec4899;
            
            /* Gradientes */
            --grad-blue: linear-gradient(135deg, #4a6cf7, #7c5bf5);
            --grad-green: linear-gradient(135deg, #00e676, #00bfa5);
            --grad-warn: linear-gradient(135deg, #ffc107, #ff9100);
            --grad-red: linear-gradient(135deg, #ef4444, #ec4899);
            
            /* Bordes redondeados (rectos, profesional) */
            --r: 6px;
            --r-md: 8px;
            --r-lg: 10px;
            --r-full: 50px;
            
            /* Transiciones */
            --fast: .18s ease;
            --smooth: .3s ease;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-deep);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            font-size: 14px;          /* BASE MÁS GRANDE */
            line-height: 1.5;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-deep); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        /* =============================================
           SIDEBAR — Navegación lateral
           ============================================= */
        .sidebar {
            position: fixed;
            left: 0; top: 0;
            width: 250px;
            height: 100vh;
            background: var(--bg-primary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: var(--smooth);
        }

        .sb-brand {
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 11px;
            border-bottom: 1px solid var(--border);
        }

        .sb-logo {
            width: 42px; height: 42px;
            background: var(--grad-blue);
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 0 20px rgba(74,108,247,0.2);
        }

        .sb-brand h2 {
            font-size: 15px;
            font-weight: 700;
            background: var(--grad-blue);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sb-brand span {
            font-size: 11px;
            color: var(--text-muted);
        }

        .sb-nav {
            flex: 1;
            padding: 14px 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow-y: auto;
        }

        .sb-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 16px 14px 6px;
        }

        .sb-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            border-radius: var(--r);
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-secondary);
            transition: var(--fast);
            position: relative;
            text-decoration: none;
        }

        .sb-item:hover {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .sb-item.active {
            background: rgba(74,108,247,0.1);
            color: var(--blue);
            font-weight: 600;
        }

        .sb-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 22px;
            background: var(--blue);
            border-radius: 0 3px 3px 0;
        }

        .sb-icon { font-size: 17px; width: 22px; text-align: center; }

        .sb-badge {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: var(--r-full);
        }

        .sb-footer {
            padding: 14px;
            border-top: 1px solid var(--border);
        }

        .sb-user {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px;
            border-radius: var(--r);
        }

        .sb-avatar {
            width: 36px; height: 36px;
            border-radius: var(--r);
            background: var(--grad-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .sb-user h4 { font-size: 13px; font-weight: 600; }
        .sb-user span { font-size: 11px; color: var(--text-muted); }

        /* =============================================
           MAIN — Contenido principal
           ============================================= */
        .main { margin-left: 250px; min-height: 100vh; }

        /* =============================================
           TOPBAR — Barra superior sticky
           ============================================= */
        .topbar {
            position: sticky;
            top: 0; z-index: 50;
            padding: 14px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(8,12,24,0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }

        .tb-greeting h1 { font-size: 20px; font-weight: 700; }
        .tb-greeting p { font-size: 12.5px; color: var(--text-secondary); }

        .tb-right { display: flex; align-items: center; gap: 9px; }

        .tb-btn {
            width: 38px; height: 38px;
            border-radius: var(--r);
            border: 1px solid var(--border);
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: var(--fast);
            position: relative;
            color: var(--text-primary);
            text-decoration: none;
        }

        .tb-btn:hover { border-color: var(--blue); background: var(--bg-hover); }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 7px; height: 7px;
            background: var(--red);
            border-radius: 50%;
            border: 2px solid var(--bg-card);
        }

        /* =============================================
           PAGE — Contenido de cada página
           ============================================= */
        .page-content { padding: 22px 26px; }

        /* Page header */
        .ph {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .ph h2 {
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .ph-acts { display: flex; gap: 8px; }

        /* =============================================
           TARJETAS DE ESTADÍSTICAS
           ============================================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            padding: 18px;
            transition: var(--fast);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .stat::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
        }

        .stat.s-blue::before { background: var(--blue); }
        .stat.s-green::before { background: var(--green); }
        .stat.s-yellow::before { background: var(--yellow); }
        .stat.s-red::before { background: var(--red); }
        .stat.s-purple::before { background: var(--purple); }

        .stat:hover {
            border-color: var(--blue);
            transform: translateY(-2px);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .stat-icon {
            width: 36px; height: 36px;
            border-radius: var(--r);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .s-blue .stat-icon { background: rgba(74,108,247,0.12); }
        .s-green .stat-icon { background: rgba(0,230,118,0.1); }
        .s-yellow .stat-icon { background: rgba(255,193,7,0.1); }
        .s-red .stat-icon { background: rgba(239,68,68,0.1); }
        .s-purple .stat-icon { background: rgba(124,91,245,0.1); }

        .stat-change {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: var(--r);
        }

        .stat-change.up { color: var(--green); background: rgba(0,230,118,0.1); }
        .stat-change.down { color: var(--red); background: rgba(239,68,68,0.1); }

        .stat-val {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            font-family: 'JetBrains Mono', monospace;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        /* =============================================
           BOTONES
           ============================================= */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--r);
            font-family: 'Outfit';
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--fast);
            border: none;
            color: #fff;
            text-decoration: none;
        }

        .btn-p { background: var(--grad-blue); box-shadow: 0 2px 10px rgba(74,108,247,0.25); }
        .btn-p:hover { transform: translateY(-1px); box-shadow: 0 4px 18px rgba(74,108,247,0.35); }

        .btn-s { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border); }
        .btn-s:hover { border-color: var(--blue); }

        .btn-g { background: var(--grad-green); color: var(--bg-deep); }
        .btn-g:hover { box-shadow: 0 4px 18px rgba(0,230,118,0.25); }

        .btn-v { background: linear-gradient(135deg, #ec4899, #7c5bf5); }
        .btn-v:hover { box-shadow: 0 4px 18px rgba(236,72,153,0.3); }

        .btn-e { background: var(--bg-card); color: var(--green); border: 1px solid rgba(0,230,118,0.2); }
        .btn-e:hover { background: rgba(0,230,118,0.06); }

        .btn-danger { background: var(--grad-red); }

        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-xs { padding: 4px 9px; font-size: 11px; border-radius: 4px; }

        /* =============================================
           CARD — Tarjeta contenedora
           ============================================= */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            overflow: hidden;
        }

        .card-h {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
        }

        .card-h h3 {
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .card-act {
            font-size: 12px;
            color: var(--blue);
            cursor: pointer;
            font-weight: 600;
            background: none;
            border: none;
            font-family: 'Outfit';
            text-decoration: none;
        }

        .card-act:hover { color: var(--purple); }
        .card-b { padding: 14px 18px; }

        /* =============================================
           TABLA
           ============================================= */
        .tbl { width: 100%; border-collapse: collapse; }

        .tbl thead th {
            padding: 12px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .8px;
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
        }

        .tbl tbody td {
            padding: 13px 14px;
            font-size: 13.5px;
            border-bottom: 1px solid rgba(30,42,74,0.4);
        }

        .tbl tbody tr { cursor: pointer; transition: var(--fast); }
        .tbl tbody tr:hover td { background: var(--bg-hover); }
        .tbl tbody tr:last-child td { border-bottom: none; }

        /* =============================================
           BADGES — Etiquetas de estado
           ============================================= */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: var(--r);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-blue { background: rgba(74,108,247,0.12); color: var(--blue); }
        .badge-yellow { background: rgba(255,193,7,0.12); color: var(--yellow); }
        .badge-orange { background: rgba(255,145,0,0.12); color: var(--orange); }
        .badge-green { background: rgba(0,230,118,0.1); color: var(--green); }
        .badge-red { background: rgba(239,68,68,0.1); color: var(--red); }
        .badge-gray { background: rgba(85,96,120,0.15); color: var(--text-muted); }
        .badge-purple { background: rgba(124,91,245,0.1); color: var(--purple); }

        /* =============================================
           ACCIONES RÁPIDAS
           ============================================= */
        .quick-acts {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .qbtn {
            background: var(--bg-card);
            border: 1px dashed var(--border);
            border-radius: var(--r-md);
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: var(--fast);
            color: var(--text-primary);
            text-decoration: none;
        }

        .qbtn:hover {
            border-style: solid;
            border-color: var(--blue);
            background: var(--bg-hover);
        }

        .qbtn-icon {
            width: 40px; height: 40px;
            border-radius: var(--r);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        .qbtn:nth-child(1) .qbtn-icon { background: rgba(74,108,247,0.12); }
        .qbtn:nth-child(2) .qbtn-icon { background: rgba(0,230,118,0.1); }
        .qbtn:nth-child(3) .qbtn-icon { background: rgba(236,72,153,0.1); }
        .qbtn:nth-child(4) .qbtn-icon { background: rgba(124,91,245,0.1); }

        .qbtn h4 { font-size: 13.5px; font-weight: 600; }
        .qbtn p { font-size: 11px; color: var(--text-muted); }

        /* =============================================
           CONTENT GRID — Layout de 2 columnas
           ============================================= */
        .cgrid {
            display: grid;
            grid-template-columns: 5fr 3fr;
            gap: 16px;
            margin-bottom: 22px;
        }

        /* =============================================
           WORK ITEMS (dashboard recientes)
           ============================================= */
        .wi {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 0;
            border-bottom: 1px solid rgba(30,42,74,0.4);
            cursor: pointer;
            transition: var(--fast);
            text-decoration: none;
            color: var(--text-primary);
        }

        .wi:last-child { border-bottom: none; }
        .wi:hover { transform: translateX(2px); }

        .wi-info { flex: 1; }
        .wi-info h4 { font-size: 13px; font-weight: 600; }
        .wi-info p { font-size: 11px; color: var(--text-muted); }

        .wi-days { font-size: 11px; color: var(--text-muted); font-family: 'JetBrains Mono'; }
        .wi-days.delayed { color: var(--red); font-weight: 600; }

        .dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .dot-blue { background: var(--blue); }
        .dot-yellow { background: var(--yellow); }
        .dot-orange { background: var(--orange); }
        .dot-green { background: var(--green); }
        .dot-red { background: var(--red); }
        .dot-gray { background: var(--text-muted); }
        .dot-purple { background: var(--purple); }

        /* =============================================
           ALERT ITEMS
           ============================================= */
        .ai {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(30,42,74,0.4);
        }
        .ai:last-child { border-bottom: none; }

        .ai-icon {
            width: 32px; height: 32px;
            border-radius: var(--r);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .ai-icon.warn { background: rgba(255,193,7,0.1); }
        .ai-icon.danger { background: rgba(239,68,68,0.1); }
        .ai-icon.ok { background: rgba(0,230,118,0.1); }
        .ai-icon.info { background: rgba(74,108,247,0.1); }

        .ai h4 { font-size: 12.5px; font-weight: 600; }
        .ai p { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

        /* =============================================
           BIRTHDAY ITEMS
           ============================================= */
        .bi {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 0;
            border-bottom: 1px solid rgba(30,42,74,0.4);
        }
        .bi:last-child { border-bottom: none; }

        .bi-av {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--grad-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }

        .bi-info { flex: 1; }
        .bi-info h4 { font-size: 13px; font-weight: 600; }
        .bi-info p { font-size: 11px; color: var(--text-muted); }

        .bi-btn {
            background: var(--green);
            color: var(--bg-deep);
            border: none;
            padding: 6px 12px;
            border-radius: var(--r);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit';
            transition: var(--fast);
        }
        .bi-btn:hover { opacity: .9; }

        /* =============================================
           ALERT BANNER
           ============================================= */
        .alert-banner {
            background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(236,72,153,0.04));
            border: 1px solid rgba(239,68,68,0.15);
            border-radius: var(--r-md);
            padding: 13px 18px;
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 20px;
        }

        .alert-banner .a-text { font-size: 13px; color: var(--text-secondary); flex: 1; }
        .alert-banner strong { color: var(--red); }

        .alert-banner .a-btn {
            background: var(--red);
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: var(--r);
            font-family: 'Outfit';
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        /* =============================================
           SELECT / COMBO (filtros)
           ============================================= */
        .combo {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 9px 14px;
            color: var(--text-primary);
            font-family: 'Outfit';
            font-size: 13px;
            outline: none;
            min-width: 170px;
            cursor: pointer;
        }
        .combo:focus { border-color: var(--blue); }

        /* =============================================
           SEARCH BOX
           ============================================= */
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 9px 14px;
            width: 280px;
        }
        .search-box:focus-within { border-color: var(--blue); }

        .search-box input {
            background: none;
            border: none;
            outline: none;
            color: var(--text-primary);
            font-family: 'Outfit';
            font-size: 13px;
            width: 100%;
        }
        .search-box input::placeholder { color: var(--text-muted); }

        .filters-row {
            display: flex;
            gap: 9px;
            margin-bottom: 18px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* =============================================
           FORMULARIOS
           ============================================= */
        .fg { margin-bottom: 14px; }

        .fg label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .fg input, .fg select, .fg textarea {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 10px 13px;
            color: var(--text-primary);
            font-family: 'Outfit';
            font-size: 13.5px;
            outline: none;
        }

        .fg input:focus, .fg select:focus { border-color: var(--blue); }

        .frow { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .frow3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }

        .fsec-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--blue);
            margin: 18px 0 10px;
            display: flex;
            align-items: center;
            gap: 7px;
            padding-bottom: 7px;
            border-bottom: 1px solid var(--border);
        }

        /* Error de validación */
        .field-error {
            color: var(--red);
            font-size: 11px;
            margin-top: 3px;
        }

        /* =============================================
           TOAST / MENSAJES FLASH
           ============================================= */
        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--r-md);
            margin-bottom: 18px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideD .4s ease;
        }

        @keyframes slideD {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .flash-success {
            background: rgba(0,230,118,0.08);
            border: 1px solid rgba(0,230,118,0.2);
            color: var(--green);
        }

        .flash-error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: var(--red);
        }

        /* =============================================
           RESPONSIVE
           ============================================= */
        @media (max-width: 1200px) {
            .stats { grid-template-columns: repeat(3, 1fr); }
            .quick-acts { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .stats { grid-template-columns: 1fr 1fr; }
            .quick-acts { grid-template-columns: 1fr; }
            .cgrid { grid-template-columns: 1fr; }
            .search-box { display: none !important; }
            #menuBtn { display: flex !important; }
        }
    </style>
    @yield('styles')
</head>
<body>

    {{-- =============================================
         SIDEBAR — Navegación lateral
         ============================================= --}}
    <aside class="sidebar" id="sidebar">
        {{-- Logo y nombre --}}
        <div class="sb-brand">
            <div class="sb-logo">👁️</div>
            <div>
                <h2>Universo Visual</h2>
                <span>Sistema de Gestión</span>
            </div>
        </div>

        {{-- Menú de navegación --}}
        <nav class="sb-nav">
            <div class="sb-label">Principal</div>
            
            <a href="{{ route('dashboard') }}" 
               class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sb-icon">🏠</span> Inicio
            </a>
            
            <a href="{{ route('works.index') }}" 
               class="sb-item {{ request()->routeIs('works.*') ? 'active' : '' }}">
                <span class="sb-icon">👓</span> Trabajos
                @php $activeWorks = \App\Models\Work::whereNotIn('status', ['delivered','cancelled'])->count(); @endphp
                @if($activeWorks > 0)
                    <span class="sb-badge">{{ $activeWorks }}</span>
                @endif
            </a>
            
            <a href="{{ route('clients.index') }}" 
               class="sb-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <span class="sb-icon">👥</span> Clientes
            </a>
            
            <a href="{{ route('laboratories.index') }}" 
               class="sb-item {{ request()->routeIs('laboratories.*') ? 'active' : '' }}">
                <span class="sb-icon">🏭</span> Laboratorios
            </a>

            <div class="sb-label">Análisis</div>
            
            {{-- Reportes: solo visible para admin --}}
            @if(Auth::user()->isAdmin())
                <a href="{{ route('reports.index') }}" 
                   class="sb-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <span class="sb-icon">📊</span> Reportes y Estadísticas
                </a>
                
                <a href="{{ route('reports.daily') }}" 
                   class="sb-item {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                    <span class="sb-icon">📋</span> Resumen Diario
                </a>
            @endif

            <div class="sb-label">Herramientas</div>
            
            <a href="{{ route('clients.birthdays') }}" 
               class="sb-item {{ request()->routeIs('clients.birthdays') ? 'active' : '' }}">
                <span class="sb-icon">🎂</span> Cumpleaños
                @php
                    $birthdayCount = \App\Models\Client::whereNotNull('birth_date')
                        ->get()->filter(fn($c) => $c->isBirthdayThisWeek())->count();
                @endphp
                @if($birthdayCount > 0)
                    <span class="sb-badge">{{ $birthdayCount }}</span>
                @endif
            </a>
        </nav>

        {{-- Usuario actual --}}
        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                </div>
                <div>
                    <h4>{{ Auth::user()->name }}</h4>
                    <span>👑 {{ Auth::user()->role_name }}</span>
                </div>
            </div>
        </div>
    </aside>

    {{-- =============================================
         CONTENIDO PRINCIPAL
         ============================================= --}}
    <main class="main">
        {{-- Barra superior --}}
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:14px">
                <button class="tb-btn" id="menuBtn" style="display:none" 
                        onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                <div class="tb-greeting">
                    <h1>¡Hola, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
                    <p>{{ now()->translatedFormat('l j \\d\\e F Y') }}</p>
                </div>
            </div>
            <div class="tb-right">
                <form action="{{ route('works.index') }}" method="GET" class="search-box">
                    <span>🔍</span>
                    <input name="search" placeholder="Buscar cliente, trabajo o código..." 
                           value="{{ request('search') }}">
                </form>
                <a href="{{ route('works.create') }}" class="tb-btn" title="Nuevo Trabajo">➕</a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="tb-btn" title="Cerrar Sesión">🚪</button>
                </form>
            </div>
        </header>

        {{-- Contenido de la página --}}
        <div class="page-content">
            {{-- Mensajes flash (éxito/error) --}}
            @if(session('success'))
                <div class="flash-msg flash-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-msg flash-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- JavaScript global --}}
    <script>
        // Responsive: mostrar botón hamburguesa en móvil
        if (window.innerWidth <= 768) {
            document.getElementById('menuBtn').style.display = 'flex';
        }
        window.addEventListener('resize', () => {
            document.getElementById('menuBtn').style.display = window.innerWidth <= 768 ? 'flex' : 'none';
        });

        // Auto-ocultar mensajes flash después de 5 segundos
        document.querySelectorAll('.flash-msg').forEach(msg => {
            setTimeout(() => {
                msg.style.opacity = '0';
                msg.style.transform = 'translateY(-10px)';
                msg.style.transition = 'all .3s ease';
                setTimeout(() => msg.remove(), 300);
            }, 5000);
        });
    </script>
    @yield('scripts')
</body>
</html>