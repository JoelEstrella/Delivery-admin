@extends('layouts.admin')

@section('page-title', 'Roles')
@section('ngApp', 'roles')
@section('ngController', 'roles')

@section('content')
<div ng-init="init()" ng-cloak>
    <div class="card-admin ui-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <div class="ui-page-title">{{ $title }}</div>
                <div class="ui-page-subtitle">{{ $subtitle }}</div>
            </div>

            <button type="button" class="ui-btn ui-btn--primary" ng-click="openCreateModal()">
                <span >+</span> Nuevo registro
            </button>
        </div>

        <div class="mt-4">
            <label class="form-label fw-semibold">Búsqueda</label>
            <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center">
                <input type="text"
                    class="form-control"
                    placeholder="Buscar por nombre, slug o descripción..."
                    ng-model="filters.search"
                    ng-change="onSearchChange()">

                <div class="d-flex gap-2">
                    <button type="button" class="ui-btn ui-btn--ghost" ng-click="clearSearch()">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-admin ui-card p-4">
        <div class="table-responsive">
            <table class="table ui-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Permisos</th>
                        <th>Usuarios</th>
                        <th>Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="loading">
                        <td colspan="6" class="text-center text-muted-soft py-5">Cargando roles...</td>
                    </tr>

                    <tr ng-if="!loading && !roles.length">
                        <td colspan="6" class="text-center text-muted-soft py-5">No hay roles registrados.</td>
                    </tr>

                    <tr ng-repeat="role in roles track by role.id">
                        <td class="fw-semibold">@{{ role.name }}</td>
                        <td>@{{ role.slug || '—' }}</td>
                        <td>@{{ role.permissions_count }}</td>
                        <td>@{{ role.users_count }}</td>
                        <td>
                            <span class="ui-badge ui-badge--soft badge"
                                  ng-class="role.is_active ? 'bg-success' : 'bg-secondary'">
                                @{{ role.is_active ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <button type="button" class="ui-btn ui-btn--secondary ui-btn--sm"
                                        ng-click="openViewModal(role)">
                                    Ver
                                </button>

                                <button type="button" class="ui-btn ui-btn--secondary ui-btn--sm"
                                        ng-click="openEditModal(role)">
                                    Editar
                                </button>

                                <button type="button" class="ui-btn ui-btn--dark ui-btn--sm"
                                        ng-click="confirmDelete(role)">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREATE / EDIT --}}
    <div class="ui-modal" ng-if="showFormModal" ng-class="{ 'is-open': showFormModal }">
        <div class="ui-modal__backdrop" ng-click="closeFormModal()"></div>

        <div class="ui-modal__dialog ui-modal__dialog--lg" role="dialog" aria-modal="true">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">Roles</div>
                    <div class="ui-modal__title">
                        @{{ isEditMode ? 'Editar rol' : 'Crear rol' }}
                    </div>
                </div>

                <button type="button" class="ui-icon-button" ng-click="closeFormModal()" aria-label="Cerrar">
                    ✕
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content">
                    <form name="roleForm" ng-submit="submitRole(roleForm)" novalidate>
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       ng-model="formData.name"
                                       required
                                       ng-minlength="3"
                                       ng-class="{
                                           'is-invalid': roleForm.name.$touched && roleForm.name.$invalid,
                                           'is-valid': roleForm.name.$touched && roleForm.name.$valid
                                       }">

                                <small class="text-danger" ng-show="roleForm.name.$touched && roleForm.name.$invalid">
                                    <span ng-show="roleForm.name.$error.required">El nombre es obligatorio.</span>
                                    <span ng-show="roleForm.name.$error.minlength">Mínimo 3 caracteres.</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="slug"
                                       class="form-control"
                                       ng-model="formData.slug"
                                       required
                                       ng-minlength="3"
                                       ng-disabled="isEditMode && formData.slug === 'super-admin'"
                                       ng-blur="normalizeSlug()"
                                       ng-class="{
                                           'is-invalid': roleForm.slug.$touched && roleForm.slug.$invalid,
                                           'is-valid': roleForm.slug.$touched && roleForm.slug.$valid
                                       }">

                                <small class="text-danger" ng-show="roleForm.slug.$touched && roleForm.slug.$invalid">
                                    <span ng-show="roleForm.slug.$error.required">El slug es obligatorio.</span>
                                    <span ng-show="roleForm.slug.$error.minlength">Mínimo 3 caracteres.</span>
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description"
                                          class="form-control"
                                          rows="4"
                                          ng-model="formData.description"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-check form-switch m-0 d-inline-flex align-items-center gap-2">
                                    <input type="checkbox"
                                           class="form-check-input m-0"
                                           ng-model="formData.is_active"
                                           ng-disabled="isEditMode && formData.slug === 'super-admin'">
                                    <span class="form-check-label">Rol activo</span>
                                </label>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Permisos <span class="text-danger">*</span></label>

                                <div class="alert alert-light border mb-3 py-2 px-3" ng-if="!permissions.length">
                                    No hay permisos disponibles.
                                </div>

                                <div class="row g-3" ng-if="permissions.length">
                                    <div class="col-12 col-md-6 col-xl-4" ng-repeat="group in groupedPermissions track by group.module">
                                        <div class="border rounded-3 h-100 p-3">
                                            <div class="fw-semibold mb-2 text-capitalize">@{{ group.module }}</div>

                                            <div class="d-flex flex-column gap-2">
                                                <label class="form-check" ng-repeat="permission in group.items track by permission.id">
                                                    <input type="checkbox"
                                                           class="form-check-input"
                                                           ng-checked="hasPermission(permission.id)"
                                                           ng-click="togglePermission(permission.id)">
                                                    <span class="form-check-label">
                                                        @{{ permission.label }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <small class="text-danger d-block mt-2" ng-show="submitted && !formData.permissions.length">
                                    Debes seleccionar al menos un permiso.
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="ui-btn ui-btn--secondary" ng-click="closeFormModal()">
                                Cancelar
                            </button>

                            <button type="submit" class="ui-btn ui-btn--primary" ng-disabled="saving">
                                @{{ saving ? 'Guardando...' : (isEditMode ? 'Actualizar' : 'Guardar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VIEW --}}
    <div class="ui-modal" ng-if="showViewModal" ng-class="{ 'is-open': showViewModal }">
        <div class="ui-modal__backdrop" ng-click="closeViewModal()"></div>

        <div class="ui-modal__dialog ui-modal__dialog--lg" role="dialog" aria-modal="true">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">Roles</div>
                    <div class="ui-modal__title">Detalle del rol</div>
                </div>

                <button type="button" class="ui-icon-button" ng-click="closeViewModal()" aria-label="Cerrar">
                    ✕
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content" ng-if="selectedRole">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted">Nombre</label>
                            <div class="fw-semibold">@{{ selectedRole.name }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted">Slug</label>
                            <div>@{{ selectedRole.slug || '—' }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted">Descripción</label>
                            <div>@{{ selectedRole.description || 'Sin descripción' }}</div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted">Activo</label>
                            <div>
                                <span class="ui-badge ui-badge--soft badge"
                                      ng-class="selectedRole.is_active ? 'bg-success' : 'bg-secondary'">
                                    @{{ selectedRole.is_active ? 'Sí' : 'No' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted">Permisos</label>
                            <div>@{{ selectedRole.permissions_count }}</div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted">Usuarios</label>
                            <div>@{{ selectedRole.users_count }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted">Listado de permisos</label>

                            <div class="row g-2" ng-if="selectedRole.permissions.length">
                                <div class="col-12 col-md-6 col-xl-4"
                                     ng-repeat="permission in selectedRole.permissions track by permission.id">
                                    <div class="border rounded-3 px-3 py-2 bg-light">
                                        @{{ permission.label }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-muted" ng-if="!selectedRole.permissions.length">
                                Sin permisos asignados.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="ui-btn ui-btn--secondary" ng-click="closeViewModal()">
                            Cerrar
                        </button>
                        <button type="button" class="ui-btn ui-btn--primary" ng-click="editFromView()">
                            Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.rolesUrl = @json(route('admin.roles.index'));
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/roles.js') }}"></script>
@endpush