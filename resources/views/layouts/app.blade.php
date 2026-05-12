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
         * Tema CLARO profesional.
         * Color institucional: #103192
         */
        :root {
            /* Fondos */
            --bg-deep: #edf0f8;
            --bg-primary: #F5F7FC;
            --bg-card: #FFFFFF;
            --bg-hover: #e6ecfa;
            --bg-elevated: #F0F2F5;
            --bg-input: #FFFFFF;

            /* Bordes */
            --border: #e0e0e0;
            --border-light: #eaedf3;

            /* Textos */
            --text-primary: #1a1a2e;
            --text-secondary: #6c757d;
            --text-muted: #9ca3af;

            /* Colores de acento */
            --blue: #103192;
            --purple: #1a4fd0;
            --cyan: #0284c7;
            --green: #16a34a;
            --yellow: #d97706;
            --orange: #ea580c;
            --red: #dc2626;
            --pink: #db2777;

            /* Gradientes */
            --grad-blue: linear-gradient(135deg, #103192, #1a4fd0);
            --grad-green: linear-gradient(135deg, #16a34a, #15803d);
            --grad-warn: linear-gradient(135deg, #d97706, #ea580c);
            --grad-red: linear-gradient(135deg, #dc2626, #db2777);

            /* Bordes redondeados */
            --r: 8px;
            --r-md: 12px;
            --r-lg: 16px;
            --r-full: 50px;

            /* Transiciones */
            --fast: .18s ease;
            --smooth: .3s ease;

            /* Sombras */
            --shadow-sm: 0 1px 3px rgba(16, 49, 146, 0.06);
            --shadow: 0 2px 10px rgba(16, 49, 146, 0.08);
            --shadow-md: 0 4px 15px rgba(16, 49, 146, 0.1);
            --shadow-lg: 0 8px 30px rgba(16, 49, 146, 0.12);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-deep);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-deep); }
        ::-webkit-scrollbar-thumb { background: #c5cad3; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #a0a7b4; }

        /* =============================================
           SIDEBAR — Navegación lateral
           ============================================= */
        .sidebar {
            position: fixed;
            left: 0; top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #eef1fb 0%, #e2e8f8 50%, #dce3f5 100%);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid #ced6ea;
        }

        /* Scrollbar dentro del sidebar */
        .sb-nav::-webkit-scrollbar { width: 4px; }
        .sb-nav::-webkit-scrollbar-track { background: transparent; }
        .sb-nav::-webkit-scrollbar-thumb { background: #b8c2da; border-radius: 10px; }

        .sb-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(16, 49, 146, 0.1);
        }

        .sb-logo {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .sb-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 3px;
            overflow-y: auto;
        }

        .sb-label {
            font-size: 10px;
            font-weight: 700;
            color: #8692b0;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            padding: 18px 14px 6px;
        }

        .sb-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #3d4f7c;
            transition: all 0.25s ease;
            position: relative;
            text-decoration: none;
        }

        .sb-item:hover {
            background: rgba(255, 255, 255, 0.6);
            color: #103192;
            box-shadow: 0 2px 8px rgba(16, 49, 146, 0.06);
        }

        .sb-item.active {
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(16, 49, 146, 0.35);
        }

        .sb-item.active::before {
            display: none;
        }

        .sb-icon {
            font-size: 18px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .sb-badge {
            margin-left: auto;
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: var(--r-full);
            min-width: 22px;
            text-align: center;
        }

        .sb-item.active .sb-badge {
            background: rgba(255,255,255,0.28);
        }

        .sb-footer {
            padding: 14px 12px;
            border-top: 1px solid rgba(16, 49, 146, 0.1);
        }

        .sb-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .sb-avatar {
            width: 40px; height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #103192, #1a4fd0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(16, 49, 146, 0.25);
        }

        .sb-user h4 { font-size: 13px; font-weight: 600; color: #1a1a2e; }
        .sb-user span { font-size: 11px; color: #6b7a9e; }

        .sb-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            margin-top: 10px;
            padding: 9px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #6b7a9e;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .sb-logout:hover {
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.2);
            color: #dc2626;
        }

        /* =============================================
           MAIN — Contenido principal
           ============================================= */
        .main { margin-left: 260px; min-height: 100vh; }

        /* =============================================
           TOPBAR — Barra superior sticky
           ============================================= */
        .topbar {
            position: sticky;
            top: 0; z-index: 50;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, rgba(255,255,255,0.97), rgba(237,240,248,0.95));
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(16, 49, 146, 0.06);
            box-shadow: 0 2px 12px rgba(16, 49, 146, 0.04);
        }

        .tb-greeting h1 {
            font-size: 20px;
            font-weight: 700;
            color: #103192;
        }
        .tb-greeting p { font-size: 12.5px; color: var(--text-secondary); }

        .tb-right { display: flex; align-items: center; gap: 10px; }

        .tb-btn {
            width: 42px; height: 42px;
            border-radius: var(--r-md);
            border: 1px solid #d6dce8;
            background: #f5f7fc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            color: #4a5568;
            text-decoration: none;
        }

        .tb-btn:hover {
            border-color: #103192;
            background: #e8edff;
            color: #103192;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16,49,146,0.1);
        }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 8px; height: 8px;
            background: var(--red);
            border-radius: 50%;
            border: 2px solid #FFFFFF;
        }

        /* =============================================
           PAGE — Contenido de cada página
           ============================================= */
        .page-content { padding: 24px 28px; }

        /* Page header */
        .ph {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .ph h2 {
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #103192;
        }

        .ph-acts { display: flex; gap: 8px; }

        /* =============================================
           TARJETAS DE ESTADÍSTICAS
           ============================================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--r-lg);
            padding: 22px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .stat::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            border-radius: 0 4px 4px 0;
        }

        .stat.s-blue::before { background: var(--blue); }
        .stat.s-green::before { background: var(--green); }
        .stat.s-yellow::before { background: var(--yellow); }
        .stat.s-red::before { background: var(--red); }
        .stat.s-purple::before { background: var(--purple); }

        .stat.s-blue { border-left: none; }
        .stat.s-green { border-left: none; }
        .stat.s-yellow { border-left: none; }
        .stat.s-red { border-left: none; }
        .stat.s-purple { border-left: none; }

        .stat:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: transparent;
        }

        .stat.s-blue:hover { box-shadow: 0 6px 20px rgba(16,49,146,0.15); }
        .stat.s-green:hover { box-shadow: 0 6px 20px rgba(22,163,74,0.15); }
        .stat.s-yellow:hover { box-shadow: 0 6px 20px rgba(217,119,6,0.15); }
        .stat.s-red:hover { box-shadow: 0 6px 20px rgba(220,38,38,0.15); }
        .stat.s-purple:hover { box-shadow: 0 6px 20px rgba(26,79,208,0.15); }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .s-blue .stat-icon { background: rgba(16,49,146,0.1); }
        .s-green .stat-icon { background: rgba(22,163,74,0.1); }
        .s-yellow .stat-icon { background: rgba(217,119,6,0.1); }
        .s-red .stat-icon { background: rgba(220,38,38,0.1); }
        .s-purple .stat-icon { background: rgba(26,79,208,0.1); }

        .stat-change {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: var(--r);
        }

        .stat-change.up { color: var(--green); background: rgba(22,163,74,0.08); }
        .stat-change.down { color: var(--red); background: rgba(220,38,38,0.08); }

        .stat-val {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-primary);
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
            padding: 10px 20px;
            border-radius: var(--r);
            font-family: 'Outfit';
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            color: #fff;
            text-decoration: none;
        }

        .btn:hover { transform: scale(1.02); }

        .btn-p { background: var(--grad-blue); box-shadow: 0 4px 12px rgba(16,49,146,0.25); }
        .btn-p:hover { box-shadow: 0 6px 20px rgba(16,49,146,0.35); }

        .btn-s {
            background: #FFFFFF;
            color: #4a5568;
            border: 1px solid #d6dce8;
        }
        .btn-s:hover { border-color: #103192; color: #103192; background: #f0f4ff; }

        .btn-g { background: var(--grad-green); }
        .btn-g:hover { box-shadow: 0 4px 12px rgba(22,163,74,0.25); }

        .btn-v { background: linear-gradient(135deg, #db2777, #7c3aed); }
        .btn-v:hover { box-shadow: 0 4px 12px rgba(219,39,119,0.3); }

        .btn-e {
            background: #FFFFFF;
            color: var(--green);
            border: 1px solid rgba(22,163,74,0.2);
        }
        .btn-e:hover { background: rgba(22,163,74,0.05); }

        .btn-danger { background: var(--grad-red); }

        .btn-sm { padding: 7px 14px; font-size: 12px; }
        .btn-xs { padding: 5px 10px; font-size: 11px; border-radius: 6px; }

        /* =============================================
           CARD — Tarjeta contenedora
           ============================================= */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--r-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-h {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-light);
            background: linear-gradient(135deg, #f8f9ff, #f0f4ff);
        }

        .card-h h3 {
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #103192;
        }

        .card-act {
            font-size: 12px;
            color: #103192;
            cursor: pointer;
            font-weight: 600;
            background: none;
            border: none;
            font-family: 'Outfit';
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .card-act:hover { color: var(--purple); }
        .card-b { padding: 16px 20px; }

        /* =============================================
           TABLA
           ============================================= */
        .tbl { width: 100%; border-collapse: collapse; }

        .tbl thead th {
            padding: 14px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: .8px;
            background: #103192;
        }

        .tbl thead th:first-child { border-radius: var(--r-md) 0 0 0; }
        .tbl thead th:last-child { border-radius: 0 var(--r-md) 0 0; }

        .tbl tbody td {
            padding: 14px 16px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-primary);
        }

        .tbl tbody tr { cursor: pointer; transition: all 0.3s ease; }
        .tbl tbody tr:nth-child(even) td { background: #f5f7fc; }
        .tbl tbody tr:hover td { background: #e8edff; }
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

        .badge-blue { background: #e8edff; color: #103192; border: 1px solid #c7d2fe; }
        .badge-yellow { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-orange { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .badge-green { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-red { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-gray { background: #f3f4f6; color: var(--text-secondary); border: 1px solid #e5e7eb; }
        .badge-purple { background: #e0e7ff; color: #1e3a8a; border: 1px solid #c7d2fe; }

        /* =============================================
           ACCIONES RÁPIDAS
           ============================================= */
        .quick-acts {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .qbtn {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--r-lg);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-primary);
            text-decoration: none;
            box-shadow: var(--shadow-sm);
        }

        .qbtn:hover {
            border-color: #103192;
            background: linear-gradient(135deg, #f8f9ff, #eef2ff);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(16,49,146,0.12);
        }

        .qbtn-icon {
            width: 48px; height: 48px;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .qbtn:nth-child(1) .qbtn-icon { background: linear-gradient(135deg, #e8edff, #d6deff); }
        .qbtn:nth-child(2) .qbtn-icon { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
        .qbtn:nth-child(3) .qbtn-icon { background: linear-gradient(135deg, #fce7f3, #fbcfe8); }
        .qbtn:nth-child(4) .qbtn-icon { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }

        .qbtn h4 { font-size: 14px; font-weight: 600; color: #1a1a2e; }
        .qbtn p { font-size: 11.5px; color: var(--text-muted); }

        /* =============================================
           CONTENT GRID — Layout de 2 columnas
           ============================================= */
        .cgrid {
            display: grid;
            grid-template-columns: 5fr 3fr;
            gap: 18px;
            margin-bottom: 24px;
        }

        /* =============================================
           WORK ITEMS (dashboard recientes)
           ============================================= */
        .wi {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin: 2px -6px;
            border-radius: var(--r-md);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-primary);
            border-bottom: none;
        }

        .wi + .wi { border-top: 1px solid var(--border-light); margin-top: 0; border-radius: var(--r-md); }
        .wi:hover { background: #f0f4ff; transform: translateX(3px); }

        .wi-info { flex: 1; }
        .wi-info h4 { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .wi-info p { font-size: 11px; color: var(--text-muted); }

        .wi-days { font-size: 11px; color: var(--text-muted); font-family: 'JetBrains Mono'; }
        .wi-days.delayed { color: var(--red); font-weight: 600; }

        .dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .dot-blue { background: #103192; }
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
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-light);
        }
        .ai:last-child { border-bottom: none; }

        .ai-icon {
            width: 36px; height: 36px;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .ai-icon.warn { background: linear-gradient(135deg, #fef3c7, #fde68a); }
        .ai-icon.danger { background: linear-gradient(135deg, #fee2e2, #fecaca); }
        .ai-icon.ok { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
        .ai-icon.info { background: linear-gradient(135deg, #e8edff, #c7d2fe); }

        .ai h4 { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .ai p { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* =============================================
           BIRTHDAY ITEMS
           ============================================= */
        .bi {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-light);
        }
        .bi:last-child { border-bottom: none; }

        .bi-av {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--grad-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .bi-info { flex: 1; }
        .bi-info h4 { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .bi-info p { font-size: 11px; color: var(--text-muted); }

        .bi-btn {
            background: var(--grad-green);
            color: #FFFFFF;
            border: none;
            padding: 7px 14px;
            border-radius: var(--r);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit';
            transition: all 0.3s ease;
        }
        .bi-btn:hover { opacity: .9; transform: scale(1.02); }

        /* =============================================
           ALERT BANNER
           ============================================= */
        .alert-banner {
            background: linear-gradient(135deg, #fff5f5, #fef2f2);
            border: 1px solid #fecaca;
            border-left: 4px solid var(--red);
            border-radius: var(--r-lg);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            box-shadow: 0 2px 12px rgba(220, 38, 38, 0.08);
        }

        .alert-banner .a-text { font-size: 13px; color: var(--text-secondary); flex: 1; }
        .alert-banner strong { color: var(--red); }

        .alert-banner .a-btn {
            background: var(--red);
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: var(--r);
            font-family: 'Outfit';
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .alert-banner .a-btn:hover { opacity: 0.9; }

        /* =============================================
           SELECT / COMBO (filtros)
           ============================================= */
        .combo {
            background: #fafbff;
            border: 1px solid #d6dce8;
            border-radius: var(--r);
            padding: 10px 14px;
            color: var(--text-primary);
            font-family: 'Outfit';
            font-size: 13px;
            outline: none;
            min-width: 170px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .combo:focus { border-color: #103192; background: #fff; box-shadow: 0 0 0 3px rgba(16,49,146,0.1); }

        /* =============================================
           SEARCH BOX
           ============================================= */
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f0f4ff;
            border: 1px solid rgba(16,49,146,0.08);
            border-radius: var(--r-full);
            padding: 10px 18px;
            width: 320px;
            transition: all 0.3s ease;
        }
        .search-box:focus-within {
            border-color: #103192;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(16,49,146,0.1);
        }

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
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* =============================================
           FORMULARIOS
           ============================================= */
        .fg { margin-bottom: 16px; }

        .fg label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #333;
            margin-bottom: 6px;
        }

        .fg input, .fg select, .fg textarea {
            width: 100%;
            background: #fafbff;
            border: 1px solid #d6dce8;
            border-radius: var(--r);
            padding: 11px 14px;
            color: var(--text-primary);
            font-family: 'Outfit';
            font-size: 13.5px;
            outline: none;
            transition: all 0.3s ease;
        }

        .fg input:focus, .fg select:focus, .fg textarea:focus {
            border-color: #103192;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(16,49,146,0.1);
        }

        .frow { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .frow3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

        .fsec-title {
            font-size: 13px;
            font-weight: 700;
            color: #103192;
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-light);
        }

        /* Error de validación */
        .field-error {
            color: var(--red);
            font-size: 11px;
            margin-top: 4px;
        }

        /* =============================================
           TOAST / MENSAJES FLASH
           ============================================= */
        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--r-md);
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideD .4s ease;
            box-shadow: var(--shadow-sm);
        }

        @keyframes slideD {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .flash-success {
            background: linear-gradient(135deg, #d4edda, #dcfce7);
            border: 1px solid #86efac;
            border-left: 4px solid #16a34a;
            color: #155724;
        }

        .flash-error {
            background: linear-gradient(135deg, #f8d7da, #fef2f2);
            border: 1px solid #fca5a5;
            border-left: 4px solid #dc2626;
            color: #721c24;
        }

        /* =============================================
           ANIMACIONES GLOBALES
           ============================================= */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat { animation: fadeInUp 0.5s ease both; }
        .stat:nth-child(1) { animation-delay: 0.05s; }
        .stat:nth-child(2) { animation-delay: 0.1s; }
        .stat:nth-child(3) { animation-delay: 0.15s; }
        .stat:nth-child(4) { animation-delay: 0.2s; }
        .stat:nth-child(5) { animation-delay: 0.25s; }

        .card { animation: fadeInUp 0.5s ease both; animation-delay: 0.1s; }
        .qbtn { animation: fadeInUp 0.4s ease both; }
        .qbtn:nth-child(1) { animation-delay: 0.05s; }
        .qbtn:nth-child(2) { animation-delay: 0.1s; }
        .qbtn:nth-child(3) { animation-delay: 0.15s; }
        .qbtn:nth-child(4) { animation-delay: 0.2s; }

        /* =============================================
           RESPONSIVE
           ============================================= */
        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 99;
            backdrop-filter: blur(4px);
        }

        .mobile-overlay.active { display: block; }

        /* --- Tablas responsive: scroll horizontal --- */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: var(--r-md);
        }

        @media (max-width: 1200px) {
            .stats { grid-template-columns: repeat(3, 1fr); }
            .quick-acts { grid-template-columns: repeat(2, 1fr); }
            .frow3 { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            }
            .main { margin-left: 0; }
            .stats { grid-template-columns: 1fr 1fr; }
            .quick-acts { grid-template-columns: 1fr; }
            .cgrid { grid-template-columns: 1fr; }
            .frow { grid-template-columns: 1fr; }
            .frow3 { grid-template-columns: 1fr; }
            .search-box { display: none !important; }
            #menuBtn { display: flex !important; }
            .topbar { padding: 12px 16px; }
            .page-content { padding: 16px 14px; }

            .ph { flex-direction: column; align-items: flex-start; gap: 12px; }
            .ph h2 { font-size: 19px; }
            .ph-acts { width: 100%; }
            .ph-acts .btn { flex: 1; justify-content: center; font-size: 12px; padding: 9px 12px; }

            .tb-greeting h1 { font-size: 17px; }
            .tb-greeting p { font-size: 11px; }

            .stat { padding: 16px; }
            .stat-val { font-size: 22px; }
            .stat-icon { width: 40px; height: 40px; font-size: 18px; }
            .stat-label { font-size: 11px; }

            .card-h { padding: 14px 16px; }
            .card-h h3 { font-size: 13.5px; }
            .card-b { padding: 14px 16px; }

            .tbl { min-width: 600px; }

            .filters-row { flex-wrap: wrap; }
            .filters-row .combo { min-width: 0; flex: 1; font-size: 12px; }
            .filters-row .search-box { width: 100% !important; display: flex !important; }

            .btn { font-size: 12px; padding: 8px 14px; }

            .wi { padding: 10px 8px; gap: 8px; }
            .wi-info h4 { font-size: 12px; }
            .wi-info p { font-size: 10px; }
            .wi .badge { font-size: 10px; padding: 3px 7px; }
            .wi-days { font-size: 10px; }

            .fg label { font-size: 11.5px; }
            .fg input, .fg select, .fg textarea { padding: 10px 12px; font-size: 13px; }

            .alert-banner { flex-wrap: wrap; padding: 12px 14px; }
            .alert-banner .a-text { font-size: 12px; min-width: 100%; }
            .alert-banner .a-btn { width: 100%; text-align: center; margin-top: 6px; }
        }

        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr; }
            .page-content { padding: 14px 10px; }
            .topbar { padding: 10px 12px; }

            .ph h2 { font-size: 17px; }

            .stat { padding: 14px; }
            .stat-val { font-size: 20px; }

            .card-h { padding: 12px 14px; }
            .card-b { padding: 12px 14px; }

            .badge { font-size: 10px; padding: 3px 7px; }

            .tb-btn { width: 36px; height: 36px; font-size: 14px; }
        }
    </style>
    @yield('styles')
</head>
<body>

    {{-- Overlay para cerrar sidebar en móvil --}}
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeSidebar()"></div>

    {{-- =============================================
         SIDEBAR — Navegación lateral
         ============================================= --}}
    <aside class="sidebar" id="sidebar">
        {{-- Logo y nombre --}}
        <div class="sb-brand">
            <img src="{{ asset('images/univer_logo_azul_sf.png') }}" alt="Universo Visual" class="sb-logo">
        </div>

        {{-- Menú de navegación --}}
        <nav class="sb-nav">
            <div class="sb-label">Principal</div>

            <a href="{{ route('dashboard') }}"
               class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               onclick="closeSidebarMobile()">
                <span class="sb-icon">🏠</span> Inicio
            </a>

            <a href="{{ route('works.index') }}"
               class="sb-item {{ request()->routeIs('works.*') ? 'active' : '' }}"
               onclick="closeSidebarMobile()">
                <span class="sb-icon">👓</span> Trabajos
                @php $activeWorks = \App\Models\Work::whereNotIn('status', ['delivered','cancelled'])->count(); @endphp
                @if($activeWorks > 0)
                    <span class="sb-badge">{{ $activeWorks }}</span>
                @endif
            </a>

            <a href="{{ route('clients.index') }}"
               class="sb-item {{ request()->routeIs('clients.*') ? 'active' : '' }}"
               onclick="closeSidebarMobile()">
                <span class="sb-icon">👥</span> Clientes
            </a>

            <a href="{{ route('laboratories.index') }}"
               class="sb-item {{ request()->routeIs('laboratories.*') ? 'active' : '' }}"
               onclick="closeSidebarMobile()">
                <span class="sb-icon">🏭</span> Laboratorios
            </a>

            @if(Auth::user()->isAdmin())
                <div class="sb-label">Análisis</div>

                <a href="{{ route('reports.index') }}"
                   class="sb-item {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                   onclick="closeSidebarMobile()">
                    <span class="sb-icon">📊</span> Reportes y Estadísticas
                </a>

                <a href="{{ route('reports.daily') }}"
                   class="sb-item {{ request()->routeIs('reports.daily') ? 'active' : '' }}"
                   onclick="closeSidebarMobile()">
                    <span class="sb-icon">📋</span> Resumen Diario
                </a>

                <a href="{{ route('employees.index') }}"
                   class="sb-item {{ request()->routeIs('employees.*') ? 'active' : '' }}"
                   onclick="closeSidebarMobile()">
                    <span class="sb-icon">👩‍💼</span> Trabajadoras
                </a>
            @else
                <div class="sb-label">Alertas</div>

                <a href="{{ route('alerts.index') }}"
                   class="sb-item {{ request()->routeIs('alerts.*') ? 'active' : '' }}"
                   onclick="closeSidebarMobile()">
                    <span class="sb-icon">🔔</span> Alertas
                    @php
                        $alertsCount = \App\Http\Controllers\DashboardController::alertsCountFor(Auth::user());
                    @endphp
                    @if($alertsCount > 0)
                        <span class="sb-badge">{{ $alertsCount }}</span>
                    @endif
                </a>
            @endif

            <div class="sb-label">Herramientas</div>

            <a href="{{ route('clients.birthdays') }}"
               class="sb-item {{ request()->routeIs('clients.birthdays') ? 'active' : '' }}"
               onclick="closeSidebarMobile()">
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

        {{-- Usuario actual + logout --}}
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
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sb-logout">🚪 Cerrar Sesión</button>
            </form>
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
                        onclick="toggleSidebar()">☰</button>
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
                <div class="flash-msg flash-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-msg flash-error">❌ {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- JavaScript global --}}
    <script>
        // Sidebar toggle para móvil
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('mobileOverlay').classList.toggle('active');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('mobileOverlay').classList.remove('active');
        }

        function closeSidebarMobile() {
            if (window.innerWidth <= 768) closeSidebar();
        }

        // Responsive: mostrar botón hamburguesa en móvil
        function checkMobile() {
            const btn = document.getElementById('menuBtn');
            if (window.innerWidth <= 768) {
                btn.style.display = 'flex';
            } else {
                btn.style.display = 'none';
                closeSidebar();
            }
        }
        checkMobile();
        window.addEventListener('resize', checkMobile);

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

    {{-- =============================================
         CHATBOT ADMIN — Asistente con acceso a la BD
         ============================================= --}}
    <style>
        .uv-admin-fab {
            position: fixed; bottom: 22px; right: 22px;
            width: 60px; height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff; border: none;
            font-size: 26px; cursor: pointer;
            box-shadow: 0 6px 20px rgba(16,49,146,0.35);
            display: flex; align-items: center; justify-content: center;
            z-index: 9998;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .uv-admin-fab:hover { transform: scale(1.08); box-shadow: 0 8px 28px rgba(16,49,146,0.45); }
        .uv-admin-fab.hidden { display: none; }

        .uv-admin-win {
            position: fixed; bottom: 22px; right: 22px;
            width: 380px; height: 560px;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(16,49,146,0.25);
            display: none; flex-direction: column;
            overflow: hidden;
            z-index: 9999;
            transform: translateY(20px) scale(0.96); opacity: 0;
            transition: transform .25s ease, opacity .25s ease;
            font-family: 'Outfit', sans-serif;
        }
        .uv-admin-win.open { display: flex; transform: translateY(0) scale(1); opacity: 1; }

        .uv-admin-head {
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff;
            padding: 14px 16px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .uv-admin-head h3 { font-size: 15px; font-weight: 700; margin: 0; }
        .uv-admin-head button {
            background: rgba(255,255,255,0.18); border: none; color: #fff;
            width: 28px; height: 28px; border-radius: 8px; cursor: pointer;
            font-size: 16px; font-weight: 700;
            transition: background .2s ease;
        }
        .uv-admin-head button:hover { background: rgba(255,255,255,0.32); }

        .uv-admin-body {
            flex: 1; overflow-y: auto; padding: 14px;
            background: #F8F9FC; display: flex; flex-direction: column; gap: 10px;
        }
        .uv-admin-body::-webkit-scrollbar { width: 5px; }
        .uv-admin-body::-webkit-scrollbar-thumb { background: #c5cad3; border-radius: 10px; }

        .uv-amsg { max-width: 85%; padding: 10px 14px; border-radius: 14px; font-size: 13.5px; line-height: 1.45; word-wrap: break-word; white-space: pre-wrap; animation: uvAFade .25s ease; }
        @keyframes uvAFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .uv-amsg.bot { background: #103192; color: #fff; align-self: flex-start; border-bottom-left-radius: 4px; }
        .uv-amsg.user { background: #e9ecef; color: #1a1a2e; align-self: flex-end; border-bottom-right-radius: 4px; }
        .uv-amsg.typing { background: #103192; color: #fff; align-self: flex-start; border-bottom-left-radius: 4px; padding: 12px 16px; }
        .uv-adot { display: inline-block; width: 6px; height: 6px; margin: 0 1px; border-radius: 50%; background: #fff; animation: uvABlink 1.2s infinite; }
        .uv-adot:nth-child(2) { animation-delay: .2s; }
        .uv-adot:nth-child(3) { animation-delay: .4s; }
        @keyframes uvABlink { 0%, 60%, 100% { opacity: .3; } 30% { opacity: 1; } }

        .uv-admin-input {
            display: flex; gap: 8px; padding: 12px;
            border-top: 1px solid #e0e0e0; background: #FFFFFF;
        }
        .uv-admin-input input {
            flex: 1; border: 1px solid #e0e0e0; border-radius: 10px;
            padding: 10px 14px; font-family: 'Outfit', sans-serif; font-size: 13.5px;
            outline: none; background: #F8F9FC; color: #1a1a2e;
            transition: all .2s ease;
        }
        .uv-admin-input input:focus { border-color: #103192; background: #fff; box-shadow: 0 0 0 3px rgba(16,49,146,0.08); }
        .uv-admin-input button {
            background: linear-gradient(135deg, #103192, #1a4fd0);
            color: #fff; border: none; border-radius: 10px;
            padding: 0 16px; font-size: 16px; cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .uv-admin-input button:hover:not(:disabled) { transform: scale(1.05); box-shadow: 0 4px 12px rgba(16,49,146,0.25); }
        .uv-admin-input button:disabled { opacity: .5; cursor: not-allowed; }

        .uv-achips { display: flex; flex-direction: column; gap: 6px; align-self: stretch; margin-top: 4px; animation: uvAFade .3s ease; }
        .uv-achips-label { font-size: 11px; color: #6c757d; padding: 0 4px 2px; font-weight: 500; }
        .uv-achip {
            background: #fff; border: 1px solid #c7d2fe; color: #103192;
            padding: 9px 14px; border-radius: 12px; cursor: pointer;
            font-family: 'Outfit', sans-serif; font-size: 12.5px; font-weight: 500;
            text-align: left; transition: all .2s ease;
        }
        .uv-achip:hover { background: #103192; color: #fff; border-color: #103192; transform: translateX(3px); box-shadow: 0 3px 10px rgba(16,49,146,0.2); }

        @media (max-width: 480px) {
            .uv-admin-win { width: 100%; height: 100%; bottom: 0; right: 0; border-radius: 0; }
            .uv-admin-fab { bottom: 16px; right: 16px; width: 54px; height: 54px; font-size: 22px; }
        }
    </style>

    <button class="uv-admin-fab" id="uvAdminFab" onclick="uvAdminOpen()" aria-label="Abrir asistente admin">🤖</button>

    <div class="uv-admin-win" id="uvAdminWin" role="dialog" aria-label="Asistente Univer">
        <div class="uv-admin-head">
            <h3>Univer — Asistente Admin 🤖</h3>
            <button onclick="uvAdminClose()" aria-label="Cerrar chat">✕</button>
        </div>
        <div class="uv-admin-body" id="uvAdminBody"></div>
        <form class="uv-admin-input" id="uvAdminForm" onsubmit="return uvAdminSend(event)">
            <input type="text" id="uvAdminInput" placeholder="Pregúntame por trabajos, ingresos, clientes..." autocomplete="off" maxlength="1000" required>
            <button type="submit" id="uvAdminSendBtn">➤</button>
        </form>
    </div>

    <script>
    (function() {
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const MAX_HISTORY = 20;
        const WELCOME = "¡Hola! 👋 Soy Univer, tu asistente del sistema 🤖✨\n\nTengo acceso a la base de datos en tiempo real. Pregúntame lo que necesites o elige una opción 👇";
        const SUGGESTIONS = [
            '💰 ¿Cuáles fueron mis ingresos este mes?',
            '🏭 ¿Qué le debo a los laboratorios?',
            '⏰ ¿Cuántos trabajos hay retrasados?',
            '🎂 ¿Quién cumple años esta semana?',
            '📦 ¿Qué trabajos están listos para entrega?',
            '📊 Generame un informe completo',
        ];

        // Historial vivo (en memoria). Se reinicia al cerrar el chat.
        let history = [];

        const fab  = document.getElementById('uvAdminFab');
        const win  = document.getElementById('uvAdminWin');
        const body = document.getElementById('uvAdminBody');
        const input = document.getElementById('uvAdminInput');
        const sendBtn = document.getElementById('uvAdminSendBtn');

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }
        function formatMarkdown(text) {
            // Escapa primero, luego convierte **texto** en <strong>
            return escapeHtml(text).replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>');
        }
        function appendMessage(role, text) {
            const div = document.createElement('div');
            div.className = 'uv-amsg ' + (role === 'bot' ? 'bot' : 'user');
            div.innerHTML = formatMarkdown(text);
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
        }
        function showTyping() {
            const div = document.createElement('div');
            div.className = 'uv-amsg typing';
            div.id = 'uvATyping';
            div.innerHTML = '<span class="uv-adot"></span><span class="uv-adot"></span><span class="uv-adot"></span>';
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
        }
        function hideTyping() {
            const t = document.getElementById('uvATyping');
            if (t) t.remove();
        }
        function renderChips() {
            const wrap = document.createElement('div');
            wrap.className = 'uv-achips';
            wrap.id = 'uvAChips';
            const lbl = document.createElement('div');
            lbl.className = 'uv-achips-label';
            lbl.textContent = 'Sugerencias rápidas';
            wrap.appendChild(lbl);
            SUGGESTIONS.forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'uv-achip';
                btn.textContent = s;
                btn.onclick = () => sendUserText(s);
                wrap.appendChild(btn);
            });
            body.appendChild(wrap);
            body.scrollTop = body.scrollHeight;
        }
        function hideChips() {
            const c = document.getElementById('uvAChips');
            if (c) c.remove();
        }
        function resetChat() {
            history = [];
            body.innerHTML = '';
        }
        function initChat() {
            resetChat();
            appendMessage('bot', WELCOME);
            renderChips();
        }

        window.uvAdminOpen = function() {
            if (body.children.length === 0) initChat();
            fab.classList.add('hidden');
            win.classList.add('open');
            setTimeout(() => input.focus(), 250);
        };
        window.uvAdminClose = function() {
            win.classList.remove('open');
            fab.classList.remove('hidden');
            setTimeout(resetChat, 280);
        };

        async function sendUserText(text) {
            text = String(text || '').trim();
            if (!text) return;
            hideChips();
            appendMessage('user', text);
            input.value = '';
            input.disabled = true;
            sendBtn.disabled = true;
            showTyping();

            try {
                const res = await fetch('{{ route('chat.admin') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        message: text,
                        history: history.slice(-MAX_HISTORY),
                    }),
                });
                hideTyping();
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                const reply = data.response || 'No pude responder en este momento.';
                appendMessage('bot', reply);
                history.push({ role: 'user', content: text });
                history.push({ role: 'assistant', content: reply });
                if (history.length > MAX_HISTORY) history = history.slice(-MAX_HISTORY);
            } catch (err) {
                hideTyping();
                appendMessage('bot', 'Hubo un problema de conexión. Intenta de nuevo en un momento. 💙');
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            }
        }

        window.uvAdminSend = function(e) {
            e.preventDefault();
            sendUserText(input.value);
            return false;
        };
    })();
    </script>
</body>
</html>