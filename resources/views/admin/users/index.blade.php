@extends('layouts.admin')

@section('title', $title . ' | ' . config('app.name', 'Sistema administrativo'))

@section('content')
<div ng-controller="UserController as vm" ng-init="vm.init()" ng-cloak>
    <div class="card-admin ui-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="ui-page-title">{{ $title }}</div>
                <div class="ui-page-subtitle">{{ $subtitle }}</div>
            </div>
            <button type="button" class="ui-btn ui-btn--primary" ng-click="vm.openCreateModal()">
                Crear usuario
            </button>
        </div>
    </div>

    <div class="card-admin ui-card p-4">
        <div class="table-responsive">
            <table class="table ui-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="vm.loading">
                        <td colspan="5" class="text-center text-muted-soft py-5">Cargando usuarios...</td>
                    </tr>
                    <tr ng-if="!vm.loading && !vm.userList.length">
                        <td colspan="5" class="text-center text-muted-soft py-5">No hay usuarios registrados.</td>
                    </tr>
                    <tr ng-repeat="user in vm.userList track by user.id">
                        <td>@{{ user.name }}</td>
                        <td>@{{ user.username || '—' }}</td>
                        <td>@{{ user.email }}</td>
                        <td>@{{ user.role ? user.role.name : '—' }}</td>
                        <td>
                            <span class="ui-badge ui-badge--soft badge" ng-class="user.is_active ? 'bg-success' : 'bg-secondary'">
                                @{{ user.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui-modal" ng-class="{ 'is-open': vm.showCreateModal }">
        <div class="ui-modal__backdrop" ng-click="vm.closeCreateModal()"></div>
        <div class="ui-modal__dialog" role="dialog" aria-modal="true" aria-label="Crear usuario">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">Usuarios</div>
                    <div class="ui-modal__title">Crear usuario</div>
                </div>
                <button type="button" class="ui-icon-button" ng-click="vm.closeCreateModal()" aria-label="Cerrar">
                    <i data-feather="x" width="18" height="18"></i>
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content">
                    <div class="ui-alert ui-alert--error" ng-if="vm.errorMessage">
                        @{{ vm.errorMessage }}
                    </div>

                    <form name="userCreateForm" ng-submit="vm.saveUser()" novalidate>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" ng-model="vm.newUser.name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" ng-model="vm.newUser.email" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Usuario</label>
                                <input type="text" class="form-control" ng-model="vm.newUser.username">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Rol</label>
                                <select class="form-select" ng-model="vm.newUser.role_id" ng-options="role.id as role.name for role in vm.roles" required>
                                    <option value="">Selecciona un rol</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" ng-model="vm.newUser.password" required>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <label class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input m-0" ng-model="vm.newUser.is_active">
                                    <span class="form-check-label">Usuario activo</span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="ui-btn ui-btn--secondary" ng-click="vm.closeCreateModal()">Cancelar</button>
                            <button type="submit" class="ui-btn ui-btn--primary" ng-disabled="vm.saving">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
      <script>
    </script>
</div>
@endsection
