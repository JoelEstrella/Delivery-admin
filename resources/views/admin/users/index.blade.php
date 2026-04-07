@extends('layouts.admin')

@section('page-title', 'Usuarios')
@section('ngApp', 'users')
@section('ngController', 'users')

@section('content')
<div ng-init="getUsers()" ng-cloak>
    <div class="card-admin ui-card p-4 mb-4">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="ui-page-title">{{ $title }}</div>
                <div class="ui-page-subtitle">{{ $subtitle }}</div>
            </div>
            <button type="button" class="ui-btn ui-btn--primary" ng-click="openCreateModal()">
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
                        <th>Estado </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="loading">
                        <td colspan="5" class="text-center text-muted-soft py-5">Cargando usuarios...</td>
                    </tr>
                    <tr ng-if="!loading && !users.length">
                        <td colspan="5" class="text-center text-muted-soft py-5">No hay usuarios registrados.</td>
                    </tr>
                    <tr ng-repeat="user in users">
                        <td>@{{ user.name }}</td>
                        <td>@{{ user.username || '—' }}</td>
                        <td>@{{ user.email }}</td>
                        <td>@{{ user.role ? user.role.name : '—' }}</td>
                        <td>
                            <span class="ui-badge ui-badge--soft badge"
                                ng-class="user.is_active ? 'bg-success' : 'bg-secondary'">
                                @{{ user.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL CREAR USUARIO -->
    <div class="ui-modal" ng-class="{ 'is-open': showCreateModal }">
        <div class="ui-modal__backdrop" ng-click="closeCreateModal()"></div>
        <div class="ui-modal__dialog" role="dialog" aria-modal="true" aria-label="Crear usuario">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">Usuarios</div>
                    <div class="ui-modal__title">Crear usuario</div>
                </div>
                <button type="button" class="ui-icon-button" ng-click="closeCreateModal()" aria-label="Cerrar">
                    <i data-feather="x" width="18" height="18"></i>
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content">

                    <form name="userCreateForm" ng-submit="saveUser()" novalidate>

                        <div class="row g-3">

                            <!-- Nombre -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" ng-model="newUser.name" required
                                    ng-minlength="3" ng-class="{
                   'is-invalid': userCreateForm.name.$touched && userCreateForm.name.$invalid,
                   'is-valid': userCreateForm.name.$touched && userCreateForm.name.$valid
               }">

                                <small class="text-danger"
                                    ng-show="userCreateForm.name.$touched && userCreateForm.name.$invalid">
                                    <span ng-show="userCreateForm.name.$error.required">El nombre es obligatorio</span>
                                    <span ng-show="userCreateForm.name.$error.minlength">Mínimo 3 caracteres</span>
                                </small>
                            </div>

                            <!-- Correo -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" name="email" class="form-control" ng-model="newUser.email" required
                                    ng-class="{
                   'is-invalid': userCreateForm.email.$touched && userCreateForm.email.$invalid,
                   'is-valid': userCreateForm.email.$touched && userCreateForm.email.$valid
               }">

                                <small class="text-danger"
                                    ng-show="userCreateForm.email.$touched && userCreateForm.email.$invalid">
                                    <span ng-show="userCreateForm.email.$error.required">El correo es obligatorio</span>
                                    <span ng-show="userCreateForm.email.$error.email">Formato inválido</span>
                                </small>
                            </div>

                            <!-- Usuario -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="username" class="form-control" ng-model="newUser.username"
                                    ng-minlength="4" required ng-class="{
                   'is-invalid': userCreateForm.username.$touched && userCreateForm.username.$invalid,
                   'is-valid': userCreateForm.username.$touched && userCreateForm.username.$valid
               }">

                                <small class="text-danger"
                                    ng-show="userCreateForm.username.$touched && userCreateForm.username.$invalid">
                                    <span ng-show="userCreateForm.username.$error.required">El Usuario es
                                        obligatorio</span>
                                    <span ng-show="userCreateForm.username.$error.minlength">Mínimo 4 caracteres</span>
                                </small>
                            </div>

                            <!-- Rol -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Rol</label>
                                <select name="role" class="form-select" ng-model="newUser.role_id"
                                    ng-options="role.id as role.name for role in roles" required ng-class="{
                    'is-invalid': userCreateForm.role.$touched && userCreateForm.role.$invalid,
                    'is-valid': userCreateForm.role.$touched && userCreateForm.role.$valid
                }">
                                    <option value="">Selecciona un rol</option>
                                </select>

                                <small class="text-danger"
                                    ng-show="userCreateForm.role.$touched && userCreateForm.role.$invalid">
                                    El rol es obligatorio
                                </small>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" ng-model="newUser.password"
                                    required ng-minlength="6" ng-class="{
                   'is-invalid': userCreateForm.password.$touched && userCreateForm.password.$invalid,
                   'is-valid': userCreateForm.password.$touched && userCreateForm.password.$valid
               }">

                                <small class="text-danger"
                                    ng-show="userCreateForm.password.$touched && userCreateForm.password.$invalid">
                                    <span ng-show="userCreateForm.password.$error.required">La contraseña es
                                        obligatoria</span>
                                    <span ng-show="userCreateForm.password.$error.minlength">Mínimo 6 caracteres</span>
                                </small>
                            </div>

                            <!-- Activo -->
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <label class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input m-0" ng-model="newUser.is_active">
                                    <span class="form-check-label">Usuario activo</span>
                                </label>
                            </div>

                        </div>


                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="ui-btn ui-btn--secondary"
                                ng-click="closeCreateModal()">Cancelar</button>
                            <button type="submit" class="ui-btn ui-btn--primary" ng-disabled="saving">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/users.js') }}"></script>
@endpush