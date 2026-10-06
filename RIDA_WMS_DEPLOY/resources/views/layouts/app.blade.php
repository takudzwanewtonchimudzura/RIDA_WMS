<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="session-idle-timeout" content="{{ config('session.idle_timeout', 20) }}">
    @endauth
    <title>@yield('title', 'RIDA RWIMS')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/rida-logo-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/rida-logo-180.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 276px;
            --sidebar-small: 76px;
            --ink: #17272d;
            --muted: #60747b;
            --canvas: #f3f7f6;
            --sidebar: #17353a;
            --sidebar-hover: #25494e;
            --accent: #36b6a0;
            --focus: #ffd166;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--canvas); color: var(--ink); font-family: Inter, sans-serif; overflow-x: hidden; }
        #sidebar { position: fixed; z-index: 1050; inset: 0 auto 0 0; display: flex; flex-direction: column; width: var(--sidebar-width); height: 100vh; height: 100dvh; color: #e7f0ef; background: var(--sidebar); transition: width .2s ease, transform .2s ease; }
        .sidebar-header { min-height: 68px; display: flex; align-items: center; justify-content: space-between; gap: .5rem; padding: .75rem 1rem; border-bottom: 1px solid #ffffff20; }
        .brand { display: flex; align-items: center; gap: .7rem; min-width: 0; color: white; text-decoration: none; font-weight: 700; white-space: nowrap; }
        .brand img { height: 32px; width: 32px; border-radius: 6px; background: #fff; object-fit: contain; padding: 2px; }
        .sidebar-control, .mobile-toggle { display: inline-grid; place-items: center; width: 42px; height: 42px; flex: 0 0 42px; border: 0; border-radius: 6px; background: transparent; color: inherit; }
        .sidebar-control:hover, .sidebar-control:focus-visible { background: var(--sidebar-hover); }
        .sidebar-menu { flex: 1 1 auto; min-height: 0; overflow-y: auto; padding: .65rem .55rem 1rem; }
        .menu-group + .menu-group { margin-top: 1.1rem; }
        .menu-group-title { padding: .65rem .7rem .35rem; color: #a9c1c1; font-size: .69rem; font-weight: 700; letter-spacing: .07em; }
        .menu-item { display: flex; align-items: center; gap: .75rem; width: 100%; min-height: 44px; margin: .12rem 0; padding: .6rem .75rem; border: 0; border-radius: 5px; background: transparent; color: #e7f0ef; text-align: left; text-decoration: none; font: inherit; cursor: pointer; }
        .menu-item:hover, .menu-item.active, .menu-parent[aria-expanded="true"] { background: var(--sidebar-hover); color: #fff; }
        .menu-icon { display: inline-grid; place-items: center; width: 24px; flex: 0 0 24px; color: #73d5c1; }
        .menu-text { flex: 1 1 auto; min-width: 0; }
        .menu-toggle { transition: transform .18s ease; font-size: .75rem; }
        .menu-parent[aria-expanded="true"] .menu-toggle { transform: rotate(180deg); }
        .submenu { display: none; margin: .1rem 0 .35rem 1.15rem; padding-left: .5rem; border-left: 1px solid #ffffff30; }
        .submenu.open { display: block; }
        .submenu-item { display: block; padding: .55rem .7rem; border-radius: 4px; color: #d2e1df; text-decoration: none; font-size: .9rem; }
        .submenu-item:hover, .submenu-item.active { background: #ffffff14; color: #fff; }
        .user-info { display: flex; align-items: center; gap: .75rem; flex: 0 0 auto; padding: .9rem 1rem; border-top: 1px solid #ffffff20; }
        .user-avatar { display: grid; place-items: center; width: 38px; height: 38px; flex: 0 0 38px; border-radius: 50%; background: #317c78; color: white; font-weight: 700; }
        .user-name { overflow: hidden; color: white; font-size: .88rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
        .user-role { color: #b7ccca; font-size: .75rem; }
        #content { min-height: 100vh; margin-left: var(--sidebar-width); transition: margin-left .2s ease; }
        #sidebar.collapsed { width: var(--sidebar-small); }
        #sidebar.collapsed ~ #content { margin-left: var(--sidebar-small); }
        #sidebar.collapsed .brand-text, #sidebar.collapsed .menu-group-title, #sidebar.collapsed .menu-text, #sidebar.collapsed .menu-toggle, #sidebar.collapsed .submenu, #sidebar.collapsed .user-details { display: none; }
        #sidebar.collapsed .menu-item { justify-content: center; padding-inline: .4rem; }
        #sidebar.collapsed .user-info { justify-content: center; padding-inline: .4rem; }
        .top-navbar { position: sticky; top: 0; z-index: 900; display: flex; align-items: center; justify-content: space-between; gap: 1rem; min-height: 68px; padding: .7rem 1.5rem; border-bottom: 1px solid #dce6e4; background: #fff; }
        .topbar-start, .topbar-end { display: flex; align-items: center; gap: .75rem; min-width: 0; }
        .page-heading { overflow: hidden; margin: 0; font-size: 1.15rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
        .main-content { padding: 1.5rem; }
        .mobile-toggle { display: none; color: var(--ink); }
        .sidebar-backdrop { display: none; }
        :focus-visible { outline: 3px solid var(--focus); outline-offset: 2px; }
        body.navigation-open { overflow: hidden; }

        @media (max-width: 768px) {
            #sidebar { width: min(var(--sidebar-width), calc(100vw - 2.5rem)); transform: translateX(-100%); }
            #sidebar.mobile-open { transform: translateX(0); }
            #sidebar.collapsed { width: min(var(--sidebar-width), calc(100vw - 2.5rem)); }
            #sidebar.collapsed .brand-text, #sidebar.collapsed .menu-group-title, #sidebar.collapsed .menu-text, #sidebar.collapsed .menu-toggle, #sidebar.collapsed .submenu, #sidebar.collapsed .user-details { display: initial; }
            #sidebar.collapsed .menu-item { justify-content: flex-start; padding: .6rem .75rem; }
            #sidebar.collapsed .submenu.open { display: block; }
            #sidebar.collapsed .user-info { justify-content: flex-start; padding: .9rem 1rem; }
            #content, #sidebar.collapsed ~ #content { margin-left: 0; }
            .mobile-toggle { display: inline-grid; }
            .sidebar-control { display: none; }
            .sidebar-backdrop.visible { display: block; position: fixed; z-index: 1040; inset: 0; width: 100%; height: 100%; border: 0; background: #0d2029a6; }
            .top-navbar { min-height: 60px; padding: .5rem .75rem; }
            .page-heading { font-size: 1rem; }
            .topbar-start, .topbar-end { gap: .4rem; }
            .main-content { padding: 1rem .75rem; }
            .user-dropdown-label { max-width: 112px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
    @stack('styles')
    @yield('styles')
</head>
<body>
    <aside id="sidebar" aria-label="Application navigation">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="brand"><img src="{{ asset('images/rida-logo-192.png') }}" alt="RIDA logo"><span class="brand-text">RIDA RWIMS</span></a>
            <button type="button" class="sidebar-control" id="sidebarToggle" aria-label="Collapse navigation"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
        </div>
        <nav class="sidebar-menu" aria-label="Main navigation">
            <div class="menu-group">
                <div class="menu-group-title">WORKSPACE</div>
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="menu-icon"><i class="fas fa-house" aria-hidden="true"></i></span><span class="menu-text">Dashboard</span></a>
                @php $prospectingActive = request()->routeIs('prospecting.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $prospectingActive ? 'active' : '' }}" data-menu="prospecting" aria-controls="submenu-prospecting" aria-expanded="{{ $prospectingActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-compass" aria-hidden="true"></i></span><span class="menu-text">Prospecting</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $prospectingActive ? 'open' : '' }}" id="submenu-prospecting"><a href="{{ route('prospecting.index') }}" class="submenu-item {{ request()->routeIs('prospecting.index') ? 'active' : '' }}">All sites</a><a href="{{ route('prospecting.create') }}" class="submenu-item {{ request()->routeIs('prospecting.create') ? 'active' : '' }}">New site survey</a></div>
                @php $boreholeActive = request()->routeIs('boreholes.*') || request()->routeIs('maps.geographic'); @endphp
                <button type="button" class="menu-item menu-parent {{ $boreholeActive ? 'active' : '' }}" data-menu="boreholes" aria-controls="submenu-boreholes" aria-expanded="{{ $boreholeActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-water" aria-hidden="true"></i></span><span class="menu-text">Boreholes & maps</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $boreholeActive ? 'open' : '' }}" id="submenu-boreholes"><a href="{{ route('boreholes.index') }}" class="submenu-item {{ request()->routeIs('boreholes.index') ? 'active' : '' }}">All boreholes</a><a href="{{ route('boreholes.create') }}" class="submenu-item {{ request()->routeIs('boreholes.create') ? 'active' : '' }}">New borehole</a><a href="{{ route('maps.geographic') }}" class="submenu-item {{ request()->routeIs('maps.geographic') ? 'active' : '' }}">Geographic map</a><a href="{{ route('reports.technical.analysis') }}" class="submenu-item {{ request()->routeIs('reports.technical.analysis') ? 'active' : '' }}">Borehole reports</a></div>
                <a href="{{ route('water-points.index') }}" class="menu-item {{ request()->routeIs('water-points.*') ? 'active' : '' }}"><span class="menu-icon"><i class="fas fa-faucet" aria-hidden="true"></i></span><span class="menu-text">Water points</span></a>
                @php $damsActive = request()->routeIs('dams.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $damsActive ? 'active' : '' }}" data-menu="dams" aria-controls="submenu-dams" aria-expanded="{{ $damsActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-water" aria-hidden="true"></i></span><span class="menu-text">Dams</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $damsActive ? 'open' : '' }}" id="submenu-dams"><a href="{{ route('dams.index') }}" class="submenu-item {{ request()->routeIs('dams.index') ? 'active' : '' }}">All dams</a><a href="{{ route('dams.create') }}" class="submenu-item {{ request()->routeIs('dams.create') ? 'active' : '' }}">New dam</a><a href="{{ route('dams.levels') }}" class="submenu-item {{ request()->routeIs('dams.levels') ? 'active' : '' }}">Dam levels</a><a href="{{ route('dams.inspections') }}" class="submenu-item {{ request()->routeIs('dams.inspections') ? 'active' : '' }}">Inspections</a></div>
                @php $pipedActive = request()->routeIs('piped-water-schemes.*') || request()->routeIs('scheme-maintenances.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $pipedActive ? 'active' : '' }}" data-menu="piped" aria-controls="submenu-piped" aria-expanded="{{ $pipedActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-faucet-drip" aria-hidden="true"></i></span><span class="menu-text">Piped water schemes</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                                <div class="submenu {{ $pipedActive ? 'open' : '' }}" id="submenu-piped"><a href="{{ route('piped-water-schemes.index') }}" class="submenu-item {{ request()->routeIs('piped-water-schemes.index') ? 'active' : '' }}">All schemes</a><a href="{{ route('piped-water-schemes.create') }}" class="submenu-item {{ request()->routeIs('piped-water-schemes.create') ? 'active' : '' }}">New scheme</a><a href="{{ route('scheme-maintenances.index') }}" class="submenu-item {{ request()->routeIs('scheme-maintenances.*') ? 'active' : '' }}">Maintenance records</a><a href="{{ route('piped-water-schemes.networks') }}" class="submenu-item {{ request()->routeIs('piped-water-schemes.networks') && !request()->routeIs('scheme-maintenances.*') ? 'active' : '' }}">Networks</a><a href="{{ route('piped-water-schemes.quality') }}" class="submenu-item {{ request()->routeIs('piped-water-schemes.quality') ? 'active' : '' }}">Water quality</a></div>
                @php $irrigationActive = request()->routeIs('irrigation-schemes.*') || request()->routeIs('irrigation-maintenances.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $irrigationActive ? 'active' : '' }}" data-menu="irrigation" aria-controls="submenu-irrigation" aria-expanded="{{ $irrigationActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-seedling" aria-hidden="true"></i></span><span class="menu-text">Irrigation schemes</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $irrigationActive ? 'open' : '' }}" id="submenu-irrigation"><a href="{{ route('irrigation-schemes.index') }}" class="submenu-item {{ request()->routeIs('irrigation-schemes.index') ? 'active' : '' }}">All schemes</a><a href="{{ route('irrigation-schemes.create') }}" class="submenu-item {{ request()->routeIs('irrigation-schemes.create') ? 'active' : '' }}">New scheme</a><a href="{{ route('irrigation-maintenances.index') }}" class="submenu-item {{ request()->routeIs('irrigation-maintenances.*') ? 'active' : '' }}">Maintenance records</a></div>
                @php $ticketActive = request()->routeIs('tickets.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $ticketActive ? 'active' : '' }}" data-menu="tickets" aria-controls="submenu-tickets" aria-expanded="{{ $ticketActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-ticket" aria-hidden="true"></i></span><span class="menu-text">Support tickets</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $ticketActive ? 'open' : '' }}" id="submenu-tickets"><a href="{{ route('tickets.index') }}" class="submenu-item {{ request()->routeIs('tickets.index') ? 'active' : '' }}">My tickets</a><a href="{{ route('tickets.create') }}" class="submenu-item {{ request()->routeIs('tickets.create') ? 'active' : '' }}">New ticket</a></div>
                @php $consumablesActive = request()->routeIs('consumables.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $consumablesActive ? 'active' : '' }}" data-menu="consumables" aria-controls="submenu-consumables" aria-expanded="{{ $consumablesActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-boxes-stacked" aria-hidden="true"></i></span><span class="menu-text">Inventory</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $consumablesActive ? 'open' : '' }}" id="submenu-consumables"><a href="{{ route('consumables.inventory.index') }}" class="submenu-item {{ request()->routeIs('consumables.inventory.*') ? 'active' : '' }}">Stock inventory</a><a href="{{ route('consumables.stock-requests.index') }}" class="submenu-item {{ request()->routeIs('consumables.stock-requests.*') ? 'active' : '' }}">Stock requests</a><a href="{{ route('consumables.usage-reports.index') }}" class="submenu-item {{ request()->routeIs('consumables.usage-reports.*') ? 'active' : '' }}">Usage reports</a>@if(auth()->user()->isAdmin())<a href="{{ route('consumables.categories.index') }}" class="submenu-item {{ request()->routeIs('consumables.categories.*') ? 'active' : '' }}">Categories</a><a href="{{ route('consumables.suppliers.index') }}" class="submenu-item {{ request()->routeIs('consumables.suppliers.*') ? 'active' : '' }}">Suppliers</a>@endif</div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="menu-group">
                <div class="menu-group-title">ADMINISTRATION</div>
                @php $usersActive = request()->routeIs('users.*'); @endphp
                <button type="button" class="menu-item menu-parent {{ $usersActive ? 'active' : '' }}" data-menu="users" aria-controls="submenu-users" aria-expanded="{{ $usersActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-users" aria-hidden="true"></i></span><span class="menu-text">Users</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $usersActive ? 'open' : '' }}" id="submenu-users"><a href="{{ route('users.index') }}" class="submenu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">All users</a><a href="{{ route('users.create') }}" class="submenu-item {{ request()->routeIs('users.create') ? 'active' : '' }}">Add user</a><a href="{{ route('users.roles') }}" class="submenu-item {{ request()->routeIs('users.roles') ? 'active' : '' }}">Roles & permissions</a><a href="{{ route('users.activity') }}" class="submenu-item {{ request()->routeIs('users.activity') ? 'active' : '' }}">User activity</a></div>
                <a href="{{ route('clients.index') }}" class="menu-item {{ request()->routeIs('clients.*') ? 'active' : '' }}"><span class="menu-icon"><i class="fas fa-address-book" aria-hidden="true"></i></span><span class="menu-text">Clients</span></a>
                <a href="{{ route('invoices.index') }}" class="menu-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}"><span class="menu-icon"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></span><span class="menu-text">Invoices</span></a>
            </div>
            @endif
            @php $reportsActive = request()->routeIs('reports.*'); @endphp
            <div class="menu-group">
                <div class="menu-group-title">REPORTS</div>
                <a href="{{ route('reports.executive.dashboard') }}" class="menu-item {{ request()->routeIs('reports.executive.dashboard') ? 'active' : '' }}"><span class="menu-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span><span class="menu-text">Executive dashboard</span></a>
                <button type="button" class="menu-item menu-parent {{ $reportsActive ? 'active' : '' }}" data-menu="reports" aria-controls="submenu-reports" aria-expanded="{{ $reportsActive ? 'true' : 'false' }}"><span class="menu-icon"><i class="fas fa-chart-column" aria-hidden="true"></i></span><span class="menu-text">Advanced reports</span><span class="menu-toggle"><i class="fas fa-chevron-down" aria-hidden="true"></i></span></button>
                <div class="submenu {{ $reportsActive ? 'open' : '' }}" id="submenu-reports"><a href="{{ route('reports.operational.performance') }}" class="submenu-item {{ request()->routeIs('reports.operational.performance') ? 'active' : '' }}">Operational performance</a><a href="{{ route('reports.technical.analysis') }}" class="submenu-item {{ request()->routeIs('reports.technical.analysis') ? 'active' : '' }}">Technical analysis</a><a href="{{ route('reports.consumables.performance') }}" class="submenu-item {{ request()->routeIs('reports.consumables.performance') ? 'active' : '' }}">Consumables performance</a><a href="{{ route('reports.geographic.distribution') }}" class="submenu-item {{ request()->routeIs('reports.geographic.distribution') ? 'active' : '' }}">Geographic distribution</a><a href="{{ route('reports.financial') }}" class="submenu-item {{ request()->routeIs('reports.financial') ? 'active' : '' }}">Financial reports</a></div>
                <a href="{{ route('analytics.dashboard') }}" class="menu-item {{ request()->routeIs('analytics.dashboard') ? 'active' : '' }}"><span class="menu-icon"><i class="fas fa-chart-pie" aria-hidden="true"></i></span><span class="menu-text">Analytics</span></a>
            </div>
        </nav>
        @auth
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="user-details"><div class="user-name">{{ auth()->user()->name }}</div><div class="user-role">{{ ucfirst(auth()->user()->role) }}</div></div>
            <form action="{{ route('logout') }}" method="POST" class="ms-auto">@csrf<button class="menu-item p-0 border-0 bg-transparent text-white-50" type="submit" title="Log out" aria-label="Log out"><i class="fas fa-right-from-bracket" aria-hidden="true"></i></button></form>
        </div>
        @endauth
    </aside>
    <button type="button" id="sidebarBackdrop" class="sidebar-backdrop" aria-label="Close navigation menu" tabindex="-1"></button>

    @auth
    <div class="modal fade" id="idleTimeoutModal" tabindex="-1" aria-labelledby="idleTimeoutModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="idleTimeoutModalLabel"><i class="fas fa-clock me-2" aria-hidden="true"></i>Session about to expire</h5></div>
                <div class="modal-body">
                    <p class="mb-1">You've been inactive for a while. For your security, you'll be signed out in <strong id="idleCountdown">60</strong> seconds.</p>
                    <p class="mb-0 text-muted small">Select "Stay signed in" to continue your session.</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('logout') }}" method="POST" class="me-auto">@csrf<button class="btn btn-outline-secondary" type="submit">Log out now</button></form>
                    <button type="button" class="btn btn-primary" id="idleStaySignedIn">Stay signed in</button>
                </div>
            </div>
        </div>
    </div>
    <form id="idleLogoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    @endauth

    <div id="content">
        <header class="top-navbar">
            <div class="topbar-start">
                <button type="button" class="mobile-toggle" id="mobileToggle" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false"><i class="fas fa-bars" aria-hidden="true"></i></button>
                <h1 class="page-heading">@yield('page-title', $__env->yieldContent('title', 'Dashboard'))</h1>
            </div>
            <div class="topbar-end">
                @auth
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user me-2" aria-hidden="true"></i><span class="user-dropdown-label">{{ auth()->user()->name }}</span></button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown"><li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item" type="submit"><i class="fas fa-right-from-bracket me-2" aria-hidden="true"></i>Log out</button></form></li></ul>
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>
                @endauth
            </div>
        </header>
        <main class="main-content">@yield('content')</main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const sidebarToggle = document.getElementById('sidebarToggle');

            const setDrawerOpen = (open) => {
                sidebar.classList.toggle('mobile-open', open);
                sidebarBackdrop.classList.toggle('visible', open);
                document.body.classList.toggle('navigation-open', open);
                mobileToggle.setAttribute('aria-expanded', String(open));
                mobileToggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
            };

            mobileToggle.addEventListener('click', () => setDrawerOpen(!sidebar.classList.contains('mobile-open')));
            sidebarBackdrop.addEventListener('click', () => {
                setDrawerOpen(false);
                mobileToggle.focus();
            });
            sidebarToggle.addEventListener('click', () => {
                const collapsed = sidebar.classList.toggle('collapsed');
                sidebarToggle.setAttribute('aria-label', collapsed ? 'Expand navigation' : 'Collapse navigation');
                sidebarToggle.querySelector('i').className = collapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
            });

            document.querySelectorAll('.menu-parent').forEach((parent) => {
                parent.addEventListener('click', () => {
                    const submenu = document.getElementById(parent.getAttribute('aria-controls'));
                    const open = parent.getAttribute('aria-expanded') !== 'true';
                    document.querySelectorAll('.menu-parent').forEach((other) => {
                        if (other !== parent) {
                            other.setAttribute('aria-expanded', 'false');
                            document.getElementById(other.getAttribute('aria-controls'))?.classList.remove('open');
                        }
                    });
                    parent.setAttribute('aria-expanded', String(open));
                    submenu?.classList.toggle('open', open);
                });
            });

            document.querySelectorAll('#sidebar a[href]').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.matchMedia('(max-width: 768px)').matches) setDrawerOpen(false);
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
                    setDrawerOpen(false);
                    mobileToggle.focus();
                }
            });
            window.addEventListener('resize', () => {
                if (!window.matchMedia('(max-width: 768px)').matches) setDrawerOpen(false);
            });

            // Idle-session auto logout: warns the user before the server-enforced timeout hits.
            const timeoutMeta = document.querySelector('meta[name="session-idle-timeout"]');
            if (timeoutMeta) {
                const idleMinutes = parseInt(timeoutMeta.content, 10) || 20;
                const warnBeforeMs = 60 * 1000;
                const idleLimitMs = Math.max(idleMinutes * 60 * 1000 - warnBeforeMs, 10 * 1000);
                const modalEl = document.getElementById('idleTimeoutModal');
                const countdownEl = document.getElementById('idleCountdown');
                const logoutForm = document.getElementById('idleLogoutForm');
                const staySignedInBtn = document.getElementById('idleStaySignedIn');
                const idleModal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;

                let idleTimer, countdownTimer, secondsLeft;

                const heartbeat = () => fetch('{{ route('session.heartbeat') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                }).catch(() => {});

                const startCountdown = () => {
                    secondsLeft = 60;
                    if (countdownEl) countdownEl.textContent = secondsLeft;
                    idleModal?.show();
                    countdownTimer = setInterval(() => {
                        secondsLeft -= 1;
                        if (countdownEl) countdownEl.textContent = Math.max(secondsLeft, 0);
                        if (secondsLeft <= 0) {
                            clearInterval(countdownTimer);
                            logoutForm?.submit();
                        }
                    }, 1000);
                };

                const resetIdleTimer = () => {
                    clearTimeout(idleTimer);
                    idleTimer = setTimeout(startCountdown, idleLimitMs);
                };

                staySignedInBtn?.addEventListener('click', () => {
                    clearInterval(countdownTimer);
                    idleModal?.hide();
                    heartbeat();
                    resetIdleTimer();
                });

                ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'].forEach((evt) => {
                    document.addEventListener(evt, () => {
                        if (modalEl?.classList.contains('show')) return;
                        resetIdleTimer();
                    }, { passive: true });
                });

                resetIdleTimer();
            }
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>