@extends('layouts.admin')

@section('title', 'Dashboard | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    <div class="mb-4">
        <div class="ui-page-title">Dashboard</div>
        <div class="ui-page-subtitle">Resumen general de la operación del sistema</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-admin stat-card stat-card--blue p-4 h-100">
                <div class="text-muted-soft small text-uppercase mb-1">Usuarios</div>
                <div class="display-6 fw-bold mb-0">{{ $summary['users'] }}</div>
                <div class="text-muted-soft small">Activos: {{ $summary['active_users'] }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-admin stat-card stat-card--berry p-4 h-100">
                <div class="text-muted-soft small text-uppercase mb-1">CCT</div>
                <div class="display-6 fw-bold mb-0">{{ $summary['ccts'] }}</div>
                <div class="text-muted-soft small">Centros registrados</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-admin stat-card stat-card--green p-4 h-100">
                <div class="text-muted-soft small text-uppercase mb-1">Plantas</div>
                <div class="display-6 fw-bold mb-0">{{ $summary['plants'] }}</div>
                <div class="text-muted-soft small">Stock total: {{ $summary['stock_total'] }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-admin stat-card stat-card--gold p-4 h-100">
                <div class="text-muted-soft small text-uppercase mb-1">Entregas pendientes</div>
                <div class="display-6 fw-bold mb-0">{{ $summary['deliveries_pending'] }}</div>
                <div class="text-muted-soft small">Validaciones pendientes: {{ $summary['validations_pending'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card-admin ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="mb-1">Actividad reciente</h5>
                        <div class="text-muted-soft small">Últimos movimientos y acciones registradas</div>
                    </div>
                    <a href="{{ route('admin.logs.index') }}" class="ui-btn ui-btn--ghost ui-btn--sm">Ver bitácora</a>
                </div>

                <div class="table-responsive">
                        <table class="table table-hover align-middle ui-table table-admin mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Módulo</th>
                                <th>Acción</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '—' }}</td>
                                    <td><span class="badge bg-dark-soft badge-soft">{{ $log->module }}</span></td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ optional($log->user)->name ?? 'Sistema' }}</td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted-soft py-4">Aún no hay actividad registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card-admin ui-card p-4 h-100">
                <h5 class="mb-3">Accesos rápidos</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.index') }}" class="ui-btn ui-btn--ghost text-start">Usuarios</a>
                    <a href="{{ route('admin.roles.index') }}" class="ui-btn ui-btn--ghost text-start">Roles</a>
                    <a href="{{ route('admin.ccts.index') }}" class="ui-btn ui-btn--ghost text-start">CCT</a>
                    <a href="{{ route('admin.plants.index') }}" class="ui-btn ui-btn--ghost text-start">Plantas</a>
                    <a href="{{ route('admin.deliveries.index') }}" class="ui-btn ui-btn--ghost text-start">Entregas</a>
                    <a href="{{ route('admin.delivery-validations.index') }}" class="ui-btn ui-btn--ghost text-start">Validaciones</a>
                </div>
            </div>
        </div>
    </div>
@endsection
