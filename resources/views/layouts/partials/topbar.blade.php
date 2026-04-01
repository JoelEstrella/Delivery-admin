<header class="admin-topbar px-3 px-lg-4 py-3">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn btn-light border d-inline-flex align-items-center justify-content-center" type="button">
                <i data-feather="menu" width="18" height="18"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="admin-topbar-brand" aria-label="Ir al dashboard">
                <img src="{{ asset('images/segey.png') }}" alt="Logo institucional" class="admin-topbar-brand__logo">
            </a>
        </div>

        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="admin-user-avatar">
                    <img src="{{ asset('images/user.jpg') }}" alt="Usuario" class="admin-user-avatar__img">
                </span>
                <span class="text-start d-none d-sm-inline">
                    <span class="d-block fw-semibold">{{ optional($currentUser)->name }}</span>
                    <small class="text-muted">{{ optional(optional($currentUser)->role)->name }}</small>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <div class="dropdown-item-text">
                        <div class="fw-semibold">{{ optional($currentUser)->name }}</div>
                        <small class="text-muted">{{ optional(optional($currentUser)->role)->name }}</small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="px-2">
                        @csrf
                        <button class="btn btn-outline-danger w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                            <i data-feather="log-out" width="16" height="16"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
