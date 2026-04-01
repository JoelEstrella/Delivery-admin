<header class="admin-topbar">
    <div class="ui-topbar__inner">
        <div class="ui-topbar__left">
            <button id="sidebarToggle" class="ui-icon-button" type="button" aria-label="Abrir o cerrar navegación">
                <i data-feather="menu" width="18" height="18"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="admin-topbar-brand" aria-label="Ir al dashboard">
                <img src="{{ asset('images/segey.png') }}" alt="Logo institucional" class="admin-topbar-brand__logo">
            </a>
        </div>

        <div class="dropdown ui-topbar__right">
            <button class="ui-dropdown-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="ui-avatar">
                    <img src="{{ asset('images/user.jpg') }}" alt="Usuario" class="admin-user-avatar__img">
                </span>
                <span class="text-start d-none d-sm-inline">
                    <span class="d-block fw-semibold">{{ optional($currentUser)->name }}</span>
                    <small class="text-muted-soft">{{ optional(optional($currentUser)->role)->name }}</small>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <div class="dropdown-item-text">
                        <div class="fw-semibold">{{ optional($currentUser)->name }}</div>
                        <small class="text-muted-soft">{{ optional(optional($currentUser)->role)->name }}</small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="px-2">
                        @csrf
                        <button class="ui-btn ui-btn--negative w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                            <i data-feather="log-out" width="16" height="16"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
