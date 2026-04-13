@extends('layouts.admin')

@section('page-title', 'Escuelas')
@section('ngApp', 'work-centers')
@section('ngController', 'work-centers')

@section('content')
<div ng-init="getList()" ng-cloak>
    <div class="card-admin ui-card p-4 mb-4">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="ui-page-title">{{ $title }}</div>
                <div class="ui-page-subtitle">{{ $subtitle }}</div>
            </div>
            <!-- <button type="button" class="ui-btn ui-btn--primary" ng-click="create()">
                Crear
            </button> -->
        </div>
    </div>

    <div class="card-admin ui-card p-4">
        <div class="table-responsive" style="overflow-x: auto">
            <table class="table ui-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Clave CCT</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Localidad</th>
                        <th>Municipio</th>
                        <th>Director</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <!-- <th>Opciones</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="loading">
                        <td colspan="5" class="text-center text-muted-soft py-5">
                            Cargando registros...
                        </td>
                    </tr>

                    <tr ng-if="!loading && !items.length">
                        <td colspan="5" class="text-center text-muted-soft py-5">
                            Sin registros.
                        </td>
                    </tr>

                    <tr dir-paginate="item in items | itemsPerPage:pageSize" current-page="currentPage"
                        pagination-id="itemsPagination">
                        <td>@{{ item.CLAVECCT }}</td>
                        <td>@{{ item.NOMBRECT }}</td>
                        <td>@{{ item.TIPO }}</td>
                        <td>@{{ item.LOCALIDAD }} @{{ item.N_LOCALIDAD }}</td>
                        <td>@{{ item.MUNICIPIO }} @{{ item.N_MUNICIPIO }}</td>
                        <td>@{{ item.DIRECTOR }}</td>
                        <td>@{{ item.CORREOELE }}</td>
                        <td>
                            <span class="ui-badge ui-badge--soft badge"
                                ng-class="item.STATUS ? 'bg-success' : 'bg-secondary'">
                                @{{ item.STATUS ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <!-- <td>
                            <button class="btn btn-sm btn-primary" ng-click="edit(item)">
                                Editar
                            </button>
                        </td> -->
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div role="toolbar" class="btn-toolbar d-flex justify-content-start mt-3" aria-label="Segey">
            <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true"
                on-page-change="pageChangeHandler(newPageNumber)" pagination-id="itemsPagination">
            </dir-pagination-controls>
        </div>


    </div>

    <!-- MODAL CREAR USUARIO -->
    <div class="ui-modal" ng-class="{ 'is-open': showCreateModal }">
        <div class="ui-modal__backdrop" ng-click="closeCreateModal()"></div>
        <div class="ui-modal__dialog" role="dialog" aria-modal="true" aria-label="Crear usuario">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">@{{title}}</div>
                    <div class="ui-modal__title">@{{subtitle}}</div>
                </div>
                <button type="button" class="ui-icon-button" ng-click="closeCreateModal()" aria-label="Cerrar">
                    <i data-feather="x" width="18" height="18"></i>
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content">

                    <form name="createForm" ng-submit="save()" novalidate>

                        <div class="row g-3">


                            <div class="col-12 col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" ng-model="newItem.name" required
                                    ng-minlength="3" ng-class="{
                   'is-invalid': createForm.name.$touched && createForm.name.$invalid,
                   'is-valid': createForm.name.$touched && createForm.name.$valid
               }">
                                <small class="text-danger"
                                    ng-show="createForm.name.$touched && createForm.name.$invalid">
                                    <span ng-show="createForm.name.$error.required">El nombre es obligatorio</span>
                                    <span ng-show="createForm.name.$error.minlength">Mínimo 3 caracteres</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" name="email" class="form-control" ng-model="newItem.email" required
                                    ng-class="{
                   'is-invalid': createForm.email.$touched && createForm.email.$invalid,
                   'is-valid': createForm.email.$touched && createForm.email.$valid
               }">

                                <small class="text-danger"
                                    ng-show="createForm.email.$touched && createForm.email.$invalid">
                                    <span ng-show="createForm.email.$error.required">El correo es obligatorio</span>
                                    <span ng-show="createForm.email.$error.email">Formato inválido</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Usuario Respondable</label>
                                <input type="text" name="responsible_name" class="form-control"
                                    ng-model="newItem.responsible_name" ng-minlength="4" required ng-class="{
                   'is-invalid': createForm.responsible_name.$touched && createForm.responsible_name.$invalid,
                   'is-valid': createForm.responsible_name.$touched && createForm.responsible_name.$valid
               }">

                                <small class="text-danger"
                                    ng-show="createForm.responsible_name.$touched && createForm.responsible_name.$invalid">
                                    <span ng-show="createForm.responsible_name.$error.required">El Usuario es
                                        obligatorio</span>
                                    <span ng-show="createForm.responsible_name.$error.minlength">Mínimo 4
                                        caracteres</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Número</label>
                                <input type="text" name="phone" class="form-control" ng-model="newItem.phone"
                                    ng-minlength="4" required ng-class="{
                   'is-invalid': createForm.phone.$touched && createForm.phone.$invalid,
                   'is-valid': createForm.phone.$touched && createForm.phone.$valid
               }">

                                <small class="text-danger"
                                    ng-show="createForm.phone.$touched && createForm.phone.$invalid">
                                    <span ng-show="createForm.phone.$error.required">El Telefono es
                                        obligatorio</span>
                                    <span ng-show="createForm.phone.$error.minlength">10
                                        caracteres</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="address" class="form-control" ng-model="newItem.address"
                                    ng-minlength="4" required ng-class="{
                   'is-invalid': createForm.address.$touched && createForm.address.$invalid,
                   'is-valid': createForm.address.$touched && createForm.address.$valid
               }">

                                <small class="text-danger"
                                    ng-show="createForm.address.$touched && createForm.address.$invalid">
                                    <span ng-show="createForm.address.$error.required">La ubicación es
                                        obligatorio</span>
                                    <span ng-show="createForm.address.$error.minlength">3
                                        caracteres</span>
                                </small>
                            </div>



                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <label class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input m-0" ng-model="newItem.is_active">
                                    <span class="form-check-label">Usuario activo</span>
                                </label>
                            </div>

                        </div>


                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="ui-btn ui-btn--primary" ng-disabled="saving">

                                <span ng-if="!saving">@{{submitLabel}}</span>

                                <span ng-if="saving">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Guardando...
                                </span>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL Editar USUARIO -->
    <div class="ui-modal" ng-class="{ 'is-open': showEditModal }">
        <div class="ui-modal__backdrop" ng-click="closeEditModal()"></div>
        <div class="ui-modal__dialog" role="dialog" aria-modal="true" aria-label="Crear usuario">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">@{{title}}</div>
                    <div class="ui-modal__title">@{{subtitle}}</div>
                </div>
                <button type="button" class="ui-icon-button" ng-click="closeEditModal()" aria-label="Cerrar">
                    <i data-feather="x" width="18" height="18"></i>
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content">

                    <form name="editForm" ng-submit="update()" novalidate>

                        <div class="row g-3">

                            <!-- Nombre -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" ng-model="formData.name" required
                                    ng-minlength="3" ng-class="{
                   'is-invalid': editForm.name.$touched && editForm.name.$invalid,
                   'is-valid': editForm.name.$touched && editForm.name.$valid
               }">

                                <small class="text-danger" ng-show="editForm.name.$touched && editForm.name.$invalid">
                                    <span ng-show="editForm.name.$error.required">El nombre es obligatorio</span>
                                    <span ng-show="editForm.name.$error.minlength">Mínimo 3 caracteres</span>
                                </small>
                            </div>

                            <!-- Correo -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" name="email" class="form-control" ng-model="formData.email" required
                                    ng-class="{
                   'is-invalid': editForm.email.$touched && editForm.email.$invalid,
                   'is-valid': editForm.email.$touched && editForm.email.$valid
               }">

                                <small class="text-danger" ng-show="editForm.email.$touched && editForm.email.$invalid">
                                    <span ng-show="editForm.email.$error.required">El correo es obligatorio</span>
                                    <span ng-show="editForm.email.$error.email">Formato inválido</span>
                                </small>
                            </div>

                            <!-- Usuario -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Usuario Responsable</label>
                                <input type="text" name="responsible_name" class="form-control"
                                    ng-model="formData.responsible_name" ng-minlength="4" required ng-class="{
                   'is-invalid': editForm.responsible_name.$touched && editForm.responsible_name.$invalid,
                   'is-valid': editForm.responsible_name.$touched && editForm.responsible_name.$valid
               }">

                                <small class="text-danger"
                                    ng-show="editForm.responsible_name.$touched && editForm.responsible_name.$invalid">
                                    <span ng-show="editForm.responsible_name.$error.required">El Usuario es
                                        obligatorio</span>
                                    <span ng-show="editForm.responsible_name.$error.minlength">Mínimo 4
                                        caracteres</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Número</label>
                                <input type="text" name="phone" class="form-control" ng-model="formData.phone"
                                    ng-minlength="4" required ng-class="{
                   'is-invalid': editForm.phone.$touched && editForm.phone.$invalid,
                   'is-valid': editForm.phone.$touched && editForm.phone.$valid
               }">

                                <small class="text-danger" ng-show="editForm.phone.$touched && editForm.phone.$invalid">
                                    <span ng-show="editForm.phone.$error.required">El Telefono es
                                        obligatorio</span>
                                    <span ng-show="editForm.phone.$error.minlength">10
                                        caracteres</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="address" class="form-control" ng-model="formData.address"
                                    ng-minlength="4" required ng-class="{
                   'is-invalid': editForm.address.$touched && editForm.address.$invalid,
                   'is-valid': editForm.address.$touched && editForm.address.$valid
               }">

                                <small class="text-danger"
                                    ng-show="editForm.address.$touched && editForm.address.$invalid">
                                    <span ng-show="editForm.address.$error.required">La ubicación es
                                        obligatorio</span>
                                    <span ng-show="editForm.address.$error.minlength">3
                                        caracteres</span>
                                </small>
                            </div>




                            <!-- Activo -->
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <label class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input m-0" ng-model="formData.is_active">
                                    <span class="form-check-label">Usuario activo</span>
                                </label>
                            </div>

                        </div>


                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="ui-btn ui-btn--primary" ng-disabled="saving">

                                <span ng-if="!saving">@{{submitLabel}}</span>

                                <span ng-if="saving">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Guardando...
                                </span>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/work-centers.js') }}"></script>
@endpush