<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BudgetTrack') — BudgetTrack</title>
    <meta name="description" content="BudgetTrack - Gérez vos dépenses personnelles facilement.">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --md-sys-color-primary: #006A60;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #74F8E5;
            --md-sys-color-on-primary-container: #00201D;
            --md-sys-color-secondary: #4A6360;
            --md-sys-color-on-secondary: #FFFFFF;
            --md-sys-color-secondary-container: #CCE8E4;
            --md-sys-color-tertiary: #7B5800;
            --md-sys-color-tertiary-container: #FFDEAA;
            --md-sys-color-error: #BA1A1A;
            --md-sys-color-on-error: #FFFFFF;
            --md-sys-color-error-container: #FFDAD6;
            --md-sys-color-surface: #F4FBF8;
            --md-sys-color-on-surface: #161D1C;
            --md-sys-color-surface-variant: #DAE5E2;
            --md-sys-color-outline: #6F7977;
            --md-sys-color-outline-variant: #BEC9C7;
            --md-sys-color-background: #F4FBF8;
            --color-budget-ok: #006A60;
            --color-budget-warn: #7B5800;
            --color-budget-exceeded: #BA1A1A;
            --motion-duration-short: 200ms;
            --motion-duration-medium: 300ms;
            --motion-easing-standard: cubic-bezier(0.2, 0, 0, 1.0);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Noto Sans', sans-serif;
            background: var(--md-sys-color-background);
            color: var(--md-sys-color-on-surface);
            display: flex;
            min-height: 100vh;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            width: 256px;
            background: var(--md-sys-color-surface);
            border-right: 1px solid var(--md-sys-color-outline-variant);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform var(--motion-duration-medium) var(--motion-easing-standard);
        }

        .sidebar-header {
            padding: 24px 20px 16px;
            border-bottom: 1px solid var(--md-sys-color-outline-variant);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-logo-icon {
            width: 40px; height: 40px;
            background: var(--md-sys-color-primary);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 22px;
        }

        .sidebar-logo-text {
            font-family: 'Noto Sans Display', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--md-sys-color-primary);
        }

        .sidebar-nav { padding: 12px 8px; flex: 1; overflow-y: auto; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 100px;
            text-decoration: none;
            color: var(--md-sys-color-on-surface);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: background var(--motion-duration-short) var(--motion-easing-standard);
        }

        .nav-item:hover {
            background: rgba(0, 106, 96, 0.08);
        }

        .nav-item.active {
            background: var(--md-sys-color-primary-container);
            color: var(--md-sys-color-on-primary-container);
            font-weight: 700;
        }

        .nav-item .material-symbols-outlined { font-size: 22px; }

        .sidebar-footer {
            padding: 16px 8px;
            border-top: 1px solid var(--md-sys-color-outline-variant);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            background: var(--md-sys-color-surface-variant);
        }

        .user-avatar {
            width: 36px; height: 36px;
            background: var(--md-sys-color-primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .user-name { font-weight: 500; font-size: 14px; flex: 1; }
        .user-role { font-size: 11px; color: var(--md-sys-color-outline); }

        /* ─── Main Content ─── */
        .main-content {
            margin-left: 256px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background: var(--md-sys-color-surface);
            border-bottom: 1px solid var(--md-sys-color-outline-variant);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .top-bar-title {
            font-family: 'Noto Sans Display', sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--md-sys-color-on-surface);
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--md-sys-color-on-surface);
        }

        .page-content {
            padding: 24px;
            flex: 1;
            animation: fadeIn var(--motion-duration-medium) var(--motion-easing-standard);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Cards ─── */
        .card {
            background: var(--md-sys-color-surface);
            border: 1px solid var(--md-sys-color-outline-variant);
            border-radius: 12px;
            padding: 20px;
            transition: box-shadow var(--motion-duration-short) var(--motion-easing-standard);
        }

        .card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 100px;
            font-family: 'Noto Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all var(--motion-duration-short) var(--motion-easing-standard);
        }

        .btn-primary {
            background: var(--md-sys-color-primary);
            color: var(--md-sys-color-on-primary);
        }

        .btn-primary:hover { background: #00574f; box-shadow: 0 2px 8px rgba(0,106,96,0.4); }

        .btn-outlined {
            background: transparent;
            color: var(--md-sys-color-primary);
            border: 1.5px solid var(--md-sys-color-primary);
        }

        .btn-outlined:hover { background: rgba(0,106,96,0.08); }

        .btn-danger {
            background: transparent;
            color: var(--md-sys-color-error);
            border: 1.5px solid var(--md-sys-color-error);
        }

        .btn-danger:hover { background: rgba(186,26,26,0.08); }

        .btn-text {
            background: transparent;
            color: var(--md-sys-color-primary);
            padding: 10px 16px;
        }

        .btn-text:hover { background: rgba(0,106,96,0.08); }

        .btn-sm { padding: 6px 16px; font-size: 12px; }

        /* ─── FAB ─── */
        .fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--md-sys-color-primary);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0,106,96,0.4);
            transition: all var(--motion-duration-short) var(--motion-easing-standard);
            z-index: 200;
        }

        .fab:hover { background: #00574f; box-shadow: 0 8px 24px rgba(0,106,96,0.5); transform: translateY(-2px); }

        /* ─── Form Fields ─── */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--md-sys-color-outline);
            margin-bottom: 6px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--md-sys-color-outline-variant);
            border-radius: 8px;
            font-family: 'Noto Sans', sans-serif;
            font-size: 14px;
            background: var(--md-sys-color-surface);
            color: var(--md-sys-color-on-surface);
            transition: border-color var(--motion-duration-short);
            outline: none;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--md-sys-color-primary);
            box-shadow: 0 0 0 3px rgba(0,106,96,0.12);
        }

        .form-error { color: var(--md-sys-color-error); font-size: 12px; margin-top: 4px; }

        /* ─── Progress Bars ─── */
        .progress-bar-wrap {
            background: var(--md-sys-color-surface-variant);
            border-radius: 100px;
            height: 8px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 100px;
            transition: width 0.5s ease;
        }

        /* ─── Alerts / Banners ─── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .alert-warning {
            background: var(--md-sys-color-tertiary-container);
            color: #3A2900;
            border-left: 4px solid var(--md-sys-color-tertiary);
        }

        .alert-error {
            background: var(--md-sys-color-error-container);
            color: var(--md-sys-color-on-error-container);
            border-left: 4px solid var(--md-sys-color-error);
        }

        .alert-success {
            background: var(--md-sys-color-primary-container);
            color: var(--md-sys-color-on-primary-container);
            border-left: 4px solid var(--md-sys-color-primary);
        }

        /* ─── Table ─── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: var(--md-sys-color-surface-variant);
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            color: var(--md-sys-color-on-surface);
        }
        td { padding: 12px 16px; border-bottom: 1px solid var(--md-sys-color-outline-variant); font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(0,0,0,0.02); }

        /* ─── Modal Overlay ─── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active { display: flex; }

        .modal {
            background: var(--md-sys-color-surface);
            border-radius: 28px;
            padding: 24px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.24);
            animation: slideUp var(--motion-duration-medium) var(--motion-easing-standard);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .modal-title {
            font-family: 'Noto Sans Display', sans-serif;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 20px;
        }

        /* ─── Category Badge ─── */
        .cat-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 500;
            color: white;
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .hamburger { display: block; }
            .page-content { padding: 16px; }
        }

        /* ─── Grid ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        @media (max-width: 900px) {
            .charts-grid { grid-template-columns: 1fr; }
        }

        /* ─── Pagination ─── */
        .pagination { display: flex; justify-content: center; margin-top: 20px; gap: 4px; }
        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            color: var(--md-sys-color-on-surface);
            border: 1px solid var(--md-sys-color-outline-variant);
        }
        .pagination .active span {
            background: var(--md-sys-color-primary);
            color: white;
            border-color: var(--md-sys-color-primary);
        }
        .pagination a:hover { background: var(--md-sys-color-surface-variant); }

        /* Display amounts */
        .amount-display {
            font-family: 'Noto Sans Display', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--md-sys-color-primary);
        }
    </style>
</head>
<body>

{{-- Overlay pour mobile --}}
<div id="sidebar-overlay" onclick="closeSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:99;"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <span class="material-symbols-outlined">savings</span>
            </div>
            <span class="sidebar-logo-text">BudgetTrack</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="material-symbols-outlined">dashboard</span>
            Tableau de bord
        </a>
        <a href="{{ route('expenses.index') }}" class="nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">receipt_long</span>
            Mes dépenses
        </a>
        <a href="{{ route('budgets.index') }}" class="nav-item {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">account_balance_wallet</span>
            Budget
        </a>
        <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">category</span>
            Catégories
        </a>
        <a href="{{ route('expenses.trash') }}" class="nav-item {{ request()->routeIs('expenses.trash') ? 'active' : '' }}">
            <span class="material-symbols-outlined">delete_sweep</span>
            Corbeille
        </a>
        @if(auth()->user()->isAdmin())
        <div style="height:1px;background:var(--md-sys-color-outline-variant);margin:8px 0;"></div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            Administration
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->isAdmin() ? 'Administrateur' : 'Utilisateur' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:8px;">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;">
                <span class="material-symbols-outlined">logout</span>
                Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="main-content">
    <header class="top-bar">
        <div style="display:flex;align-items:center;gap:16px;">
            <button class="hamburger" onclick="toggleSidebar()">
                <span class="material-symbols-outlined" style="font-size:28px;">menu</span>
            </button>
            <h1 class="top-bar-title">@yield('title', 'Tableau de bord')</h1>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <a href="{{ route('profile.edit') }}" class="btn btn-text btn-sm">
                <span class="material-symbols-outlined">account_circle</span>
                {{ auth()->user()->name }}
            </a>
        </div>
    </header>

    <main class="page-content">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:16px;" role="alert">
                <span class="material-symbols-outlined">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:16px;" role="alert">
                <span class="material-symbols-outlined">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('open');
    overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
}
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.remove('open');
    overlay.style.display = 'none';
}
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}
// Fermer modal en cliquant sur l'overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>

@stack('scripts')
</body>
</html>
