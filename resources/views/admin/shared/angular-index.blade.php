@php
    $recordsArray = method_exists($records, 'items') ? $records->items() : (is_array($records) ? $records : []);
    $boot = [
        'records' => $recordsArray,
        'columns' => $columns,
        'routes' => [
            'base' => route('admin.ccts.index'),
            'create' => $createRoute,
        ],
    ];
    $bootJson = htmlspecialchars(json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
@endphp

<div class="card-admin p-4 mb-4" ng-app="deliveryAdminApp" ng-controller="CctController as vm" ng-cloak ng-init="vm.init('admin-ccts-bootstrap')">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
        <div>
            <div class="page-title">{{ $title }}</div>
            @if(!empty($subtitle))
                <div class="page-subtitle">{{ $subtitle }}</div>
            @endif
        </div>

        @if($createRoute)
            <a href="{{ $createRoute }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i data-feather="plus" width="16" height="16"></i>
                Nuevo registro
            </a>
        @endif
    </div>

    <div class="row g-3 align-items-end mt-4">
        <div class="col-12 col-md-8 col-lg-6">
            <label class="form-label small text-muted">Búsqueda</label>
            <input type="text" class="form-control" ng-model="vm.filters.search" ng-change="vm.applyFilters()" placeholder="Buscar en CCT...">
        </div>
        <div class="col-12 col-md-auto">
            <button type="button" class="btn btn-outline-secondary" ng-click="vm.clearFilters()">Limpiar</button>
            <button type="button" class="btn btn-outline-primary ms-2" ng-click="vm.reload()">Recargar</button>
        </div>
    </div>
</div>

<div class="card-admin p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle table-admin">
            <thead>
                <tr>
                    <th ng-repeat="column in vm.columns" class="cursor-pointer" ng-click="vm.toggleSort(column)">
                        <span>[[ column.label ]]</span>
                        <small class="text-muted ms-1" ng-if="vm.sort.field === column.field">[[ vm.sort.reverse ? '↓' : '↑' ]]</small>
                    </th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="vm.loading">
                    <td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-4">Cargando...</td>
                </tr>

                <tr ng-if="!vm.loading && !vm.filteredRecords.length">
                    <td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-5">No hay registros para mostrar.</td>
                </tr>

                <tr ng-repeat="record in vm.filteredRecords track by record.id">
                    <td ng-repeat="column in vm.columns">
                        <span ng-switch="column.type">
                            <span ng-switch-when="badge" class="badge badge-soft bg-[[ vm.resolveBadgeClass(column, vm.getValue(record, column.field)) ]]">[[ vm.resolveBadgeLabel(column, vm.getValue(record, column.field)) ]]</span>
                            <span ng-switch-when="date">[[ vm.formatDate(vm.getValue(record, column.field)) ]]</span>
                            <span ng-switch-when="datetime">[[ vm.formatDateTime(vm.getValue(record, column.field)) ]]</span>
                            <span ng-switch-default>[[ vm.formatValue(vm.getValue(record, column.field)) ]]</span>
                        </span>
                    </td>
                    <td class="text-end actions">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-outline-primary" ng-href="[[ vm.viewUrl(record) ]]">Ver</a>
                            <a class="btn btn-outline-secondary" ng-href="[[ vm.editUrl(record) ]]">Editar</a>
                            <button class="btn btn-outline-danger" type="button" ng-click="vm.destroy(record)">Eliminar</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3 small text-muted">
        <span>[[ vm.filteredRecords.length ]] registros visibles</span>
        <span>Carga reactiva con AngularJS</span>
    </div>
</div>

<script type="application/json" id="admin-ccts-bootstrap">{!! $bootJson !!}</script>
