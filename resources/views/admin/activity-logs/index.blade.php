@extends('layouts.admin')

@section('page-title', 'Bitácora')
@section('ngApp', 'activityLogsApp')
@section('ngController', 'ActivityLogsController')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="ui-page-title">Bitácora</div>
            <div class="ui-page-subtitle">Registro de acciones importantes del sistema</div>
        </div>
    </div>

    <div class="card-admin ui-card p-4">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-8 col-lg-6">
                <label class="form-label">Buscar</label>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Busca por módulo, acción, descripción, usuario o IP..."
                    ng-model="filters.search"
                    ng-change="onSearchChange()">
            </div>

            <div class="col-12 col-md-4 col-lg-3 d-flex align-items-end">
                <button type="button" class="ui-btn ui-btn--ghost w-100" ng-click="clearFilters()">
                    Limpiar filtro
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle ui-table table-admin mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 150px;">Fecha</th>
                        <th style="min-width: 130px;">Módulo</th>
                        <th style="min-width: 120px;">Acción</th>
                        <th style="min-width: 220px;">Descripción</th>
                        <th style="min-width: 160px;">Usuario</th>
                        <th style="min-width: 120px;">IP</th>
                        <th style="width: 90px;" class="text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="loading">
                        <td colspan="7" class="text-center py-4">
                            Cargando registros...
                        </td>
                    </tr>

                    <tr ng-if="!loading && records.length === 0">
                        <td colspan="7" class="text-center text-muted py-4">
                            No se encontraron registros.
                        </td>
                    </tr>

                    <tr ng-repeat="record in records track by record.id">
                        <td>@{{ record.created_at || '—' }}</td>
                        <td>
                            <span class="badge bg-dark-soft badge-soft">
                                @{{ record.module || '—' }}
                            </span>
                        </td>
                        <td>@{{ record.action || '—' }}</td>
                        <td>@{{ record.description || '—' }}</td>
                        <td>
                            <div>@{{ record.user.name || 'Sistema' }}</div>
                            <small class="text-muted" ng-if="record.user.role">
                                @{{ record.user.role }}
                            </small>
                        </td>
                        <td>@{{ record.ip_address || '—' }}</td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="ui-btn ui-btn--ghost ui-btn--sm"
                                ng-click="openDetail(record)">
                                Ver
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4"
            ng-if="pagination.last_page > 1">
            <div class="text-muted small">
                Mostrando @{{ pagination.from || 0 }} a @{{ pagination.to || 0 }}
                de @{{ pagination.total || 0 }} registros
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button
                    type="button"
                    class="ui-btn ui-btn--ghost ui-btn--sm"
                    ng-disabled="pagination.current_page <= 1 || loading"
                    ng-click="goToPage(pagination.current_page - 1)">
                    Anterior
                </button>

                <button
                    type="button"
                    class="ui-btn ui-btn--ghost ui-btn--sm"
                    ng-repeat="page in pages track by $index"
                    ng-class="{'active': page === pagination.current_page}"
                    ng-disabled="page === '...' || loading"
                    ng-click="page !== '...' && goToPage(page)">
                    @{{ page }}
                </button>

                <button
                    type="button"
                    class="ui-btn ui-btn--ghost ui-btn--sm"
                    ng-disabled="pagination.current_page >= pagination.last_page || loading"
                    ng-click="goToPage(pagination.current_page + 1)">
                    Siguiente
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade show"
        ng-if="showDetailModal"
        style="display: block; background: rgba(0,0,0,.45);"
        tabindex="-1"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Detalle de bitácora</h5>
                        <small class="text-muted">
                            @{{ selectedRecord.created_at || '—' }}
                        </small>
                    </div>
                    <button type="button" class="btn-close" ng-click="closeDetail()"></button>
                </div>

                <div class="modal-body" ng-if="selectedRecord">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Módulo</label>
                            <div class="form-control bg-light">@{{ selectedRecord.module || '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Acción</label>
                            <div class="form-control bg-light">@{{ selectedRecord.action || '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuario</label>
                            <div class="form-control bg-light">@{{ selectedRecord.user.name || 'Sistema' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol</label>
                            <div class="form-control bg-light">@{{ selectedRecord.user.role || '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IP</label>
                            <div class="form-control bg-light">@{{ selectedRecord.ip_address || '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Record ID</label>
                            <div class="form-control bg-light">@{{ selectedRecord.record_id || '—' }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <div class="form-control bg-light" style="min-height: 70px;">
                                @{{ selectedRecord.description || '—' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">User Agent</label>
                            <div class="form-control bg-light" style="min-height: 70px; white-space: normal;">
                                @{{ selectedRecord.user_agent || '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">Valores anteriores</label>
                            <pre class="bg-light border rounded p-3 small mb-0"
                                style="max-height: 280px; overflow: auto;">@{{ formatJson(selectedRecord.old_values) }}</pre>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">Valores nuevos</label>
                            <pre class="bg-light border rounded p-3 small mb-0"
                                style="max-height: 280px; overflow: auto;">@{{ formatJson(selectedRecord.new_values) }}</pre>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="ui-btn ui-btn--ghost" ng-click="closeDetail()">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.activityLogsUrl = @json(route('admin.logs.index'));
    </script>
    <script src="{{ asset('js/activity-logs.js') }}"></script>
@endpush