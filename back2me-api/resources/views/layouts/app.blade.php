<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back2Me - IUGET</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.maquette-head')
    @livewireStyles
</head>
<body>
    @auth
    <header class="topbar">
        <div class="logo">
            <div class="logo-icon">🏠</div>
            <a href="{{ route('dashboard') }}" class="logo-text">Objets<span>Trouvés</span> <span style="font-weight:400;color:#94a3b8;font-size:13px;">· IUGET</span></a>
        </div>
        <nav class="nav-tabs">
            <a href="{{ route('dashboard') }}" class="nav-tab {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Tableau de bord</a>
            <a href="{{ route('objets.index', ['status' => 'found']) }}" class="nav-tab {{ request()->routeIs('objets.*') ? 'active' : '' }}">📦 Objets trouvés</a>
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('claims.pending') }}" class="nav-tab {{ request()->routeIs('claims.*') ? 'active' : '' }}">✅ Objets réclamés</a>
                <a href="{{ route('commissions.index') }}" class="nav-tab {{ request()->routeIs('commissions.*') ? 'active' : '' }}">💰 Commissions</a>
            @endif
            <a href="{{ route('notifications.index') }}" class="nav-tab {{ request()->routeIs('notifications.*') ? 'active' : '' }}">🔔 Notifications</a>
        </nav>
        <div class="topbar-right">
            <a href="{{ route('notifications.index') }}" class="notif-btn">🔔<span class="notif-badge"></span></a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-tab">Déconnexion</button>
            </form>
            <div class="avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim(auth()->user()->name), 0, 2)) }}</div>
        </div>
    </header>
    @endauth

    @if(request()->routeIs('login') || request()->routeIs('register'))
        {{ $slot }}
    @else
        {{ $slot }}
    @endif
    @auth
        <livewire:objets.create-modal />
    @endauth

    @stack('scripts')
    @livewireScripts
</body>
</html>
