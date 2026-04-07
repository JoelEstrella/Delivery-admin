<aside class="admin-sidebar">
    <div class="admin-brand">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand__link" aria-label="Ir al dashboard">
            <span class="ui-brand-mark" aria-hidden="true"></span>
            <span class="ui-brand-copy">
                <span class="admin-brand__eyebrow">Sistema administrativo</span>
                <span class="admin-brand__title">Panel institucional</span>
            </span>
        </a>
    </div>

    <div>
        <div class="sidebar-section-label">Navegación</div>
        <nav class="d-grid gap-2">
            <a href="{{ route('admin.dashboard') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-feather="home" width="18" height="18"></i>
                <span>Dashboard</span>
            </a>
        </nav>
    </div>

    @if($currentUser && ($currentUser->hasPermission('users.view') || $currentUser->hasPermission('roles.view') || $currentUser->hasPermission('logs.view')))
        <div>
            <div class="sidebar-section-label">SeguridadDDDD</div>
            <nav class="d-grid gap-2">
                @if($currentUser->hasPermission('users.view'))
                    <a href="{{ route('admin.users.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i data-feather="users" width="18" height="18"></i>
                        <span>Usuarios</span>
                    </a>
                @endif
                @if($currentUser->hasPermission('roles.view'))
                    <a href="{{ route('admin.roles.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i data-feather="shield" width="18" height="18"></i>
                        <span>Roles</span>
                    </a>
                @endif
                @if($currentUser->hasPermission('logs.view'))
                    <a href="{{ route('admin.logs.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                        <i data-feather="file-text" width="18" height="18"></i>
                        <span>Bitácora</span>
                    </a>
                @endif
            </nav>
        </div>
    @endif

    <div>
        <div class="sidebar-section-label">Operación</div>
        <nav class="d-grid gap-2">
            @if($currentUser && $currentUser->hasPermission('ccts.view'))
                <a href="{{ route('admin.ccts.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.ccts.*') ? 'active' : '' }}">
                    <i data-feather="book-open" width="18" height="18"></i>
                    <span>CCT</span>
                </a>
            @endif
            @if($currentUser && $currentUser->hasPermission('plants.view'))
                <a href="{{ route('admin.plants.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.plants.*') ? 'active' : '' }}">
                    <i data-feather="leaf" width="18" height="18"></i>
                    <span>Plantas</span>
                </a>
            @endif
            @if($currentUser && $currentUser->hasPermission('directions.view'))
                <a href="{{ route('admin.directions.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.directions.*') ? 'active' : '' }}">
                    <i data-feather="map-pin" width="18" height="18"></i>
                    <span>Direcciones</span>
                </a>
            @endif
            @if($currentUser && $currentUser->hasPermission('deliveries.view'))
                <a href="{{ route('admin.deliveries.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">
                    <i data-feather="package" width="18" height="18"></i>
                    <span>Entregas</span>
                </a>
            @endif
            @if($currentUser && $currentUser->hasPermission('validations.view'))
                <a href="{{ route('admin.delivery-validations.index') }}" class="ui-nav-link sidebar-link {{ request()->routeIs('admin.delivery-validations.*') ? 'active' : '' }}">
                    <i data-feather="check-circle" width="18" height="18"></i>
                    <span>Validaciones</span>
                </a>
            @endif
        </nav>
    </div>

    <div class="mt-4 pt-4 small text-muted-soft">
        Sistema institucional<br>
        Gobierno del Estado de Yucatán
    </div>
</aside>
