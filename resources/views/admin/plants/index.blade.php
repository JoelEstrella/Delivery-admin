@extends('layouts.admin')

@section('page-title', 'Plantas')
@section('ngApp', 'plants')
@section('ngController', 'plants')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<div ng-cloak ng-init="init()">
    <div class="card-admin ui-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <div class="ui-page-title">Plantas</div>
                <div class="ui-page-subtitle">Catálogo de especies y material vegetal</div>
            </div>

            <button type="button" class="ui-btn ui-btn--primary" ng-click="openCreateModal()">
                <span>+</span> Nueva planta
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
                        <th style="min-width: 90px;">Imagen</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Descripción corta</th>
                        <th>Imágenes</th>
                        <th>Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="loading">
                        <td colspan="7" class="text-center text-muted-soft py-5">Cargando plantas...</td>
                    </tr>

                    <tr ng-if="!loading && !plants.length">
                        <td colspan="7" class="text-center text-muted-soft py-5">No hay plantas registradas.</td>
                    </tr>

                    <tr ng-repeat="plant in plants track by plant.id">
                        <td>
                            <img
                                ng-if="plant.primary_image_url"
                                ng-src="@{{ plant.primary_image_url }}"
                                alt="@{{ plant.name }}"
                                style="width: 56px; height: 56px; object-fit: cover; border-radius: 12px; border: 1px solid #e5e7eb;">

                            <div
                                ng-if="!plant.primary_image_url"
                                class="d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px; border-radius: 12px; border: 1px dashed #d1d5db; color: #9ca3af;">
                                —
                            </div>
                        </td>

                        <td class="fw-semibold">@{{ plant.name }}</td>
                        <td>@{{ plant.slug || '—' }}</td>
                        <td>@{{ plant.short_description || '—' }}</td>
                        <td>@{{ plant.images_count || 0 }}</td>
                        <td>
                            <span class="ui-badge ui-badge--soft badge"
                                  ng-class="plant.is_active ? 'bg-success' : 'bg-secondary'">
                                @{{ plant.is_active ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <button type="button" class="ui-btn ui-btn--secondary ui-btn--sm"
                                        ng-click="openViewModal(plant)">
                                    Ver
                                </button>

                                <button type="button" class="ui-btn ui-btn--secondary ui-btn--sm"
                                        ng-click="openEditModal(plant)">
                                    Editar
                                </button>

                                <button type="button" class="ui-btn ui-btn--dark ui-btn--sm"
                                        ng-click="confirmDelete(plant)">
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

        <div class="ui-modal__dialog ui-modal__dialog--xl" role="dialog" aria-modal="true">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">Plantas</div>
                    <div class="ui-modal__title">
                        @{{ isEditMode ? 'Editar planta' : 'Crear planta' }}
                    </div>
                </div>

                <button type="button" class="ui-icon-button" ng-click="closeFormModal()" aria-label="Cerrar">
                    ✕
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content">
                    <form name="plantForm" ng-submit="submitPlant(plantForm)" novalidate>
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
                                           'is-invalid': plantForm.name.$touched && plantForm.name.$invalid,
                                           'is-valid': plantForm.name.$touched && plantForm.name.$valid
                                       }">

                                <small class="text-danger" ng-show="plantForm.name.$touched && plantForm.name.$invalid">
                                    <span ng-show="plantForm.name.$error.required">El nombre es obligatorio.</span>
                                    <span ng-show="plantForm.name.$error.minlength">Mínimo 3 caracteres.</span>
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text"
                                           name="slug"
                                           class="form-control"
                                           ng-model="formData.slug"
                                           required
                                           ng-minlength="3"
                                           ng-class="{
                                               'is-invalid': plantForm.slug.$touched && plantForm.slug.$invalid,
                                               'is-valid': plantForm.slug.$touched && plantForm.slug.$valid
                                           }">

                                    <button type="button" class="btn btn-outline-secondary" ng-click="normalizeSlug()">
                                        Generar
                                    </button>
                                </div>

                                <small class="text-danger" ng-show="plantForm.slug.$touched && plantForm.slug.$invalid">
                                    <span ng-show="plantForm.slug.$error.required">El slug es obligatorio.</span>
                                    <span ng-show="plantForm.slug.$error.minlength">Mínimo 3 caracteres.</span>
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción corta</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    ng-model="formData.short_description"></textarea>
                            </div>

                            <div class="col-12" style="background:#fff;">
                                <label class="form-label fw-semibold">Descripción HTML</label>
                                <div id="plant-description-editor" style="height: 260px;"></div>
                                <small class="text-muted d-block mt-2">
                                    Puedes usar negritas, listas, enlaces, tablas y formato básico.
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Cuidados</label>
                                <textarea
                                    class="form-control"
                                    rows="4"
                                    ng-model="formData.care_instructions"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-check form-switch m-0 d-inline-flex align-items-center gap-2">
                                    <input type="checkbox"
                                           class="form-check-input m-0"
                                           ng-model="formData.is_active">
                                    <span class="form-check-label">Planta activa</span>
                                </label>
                            </div>

                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label class="form-label fw-semibold mb-1">Imágenes</label>
                                        <div class="small text-muted-soft">
                                            Máximo 7 imágenes. Formatos: JPG, JPEG, PNG, WEBP. Máximo 5 MB por archivo.
                                        </div>
                                    </div>
                                </div>

                                <input
                                    type="file"
                                    class="form-control"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    onchange="angular.element(this).scope().handleImageSelection(this)">

                                <small class="text-danger d-block mt-2" ng-if="imageError">
                                    @{{ imageError }}
                                </small>
                            </div>

                            <div class="col-12" ng-if="existingImages.length || imagePreviews.length">
                                <div class="row g-3 mt-1">
                                    <div class="col-12 col-md-6 col-xl-3"
                                         ng-repeat="image in existingImages track by 'existing-' + image.id">
                                        <div class="border rounded-3 p-2 h-100 bg-white">
                                            <img ng-src="@{{ image.url }}"
                                                 alt="Imagen existente"
                                                 style="width:100%; height:140px; object-fit:cover; border-radius:10px;">

                                            <div class="form-check mt-2">
                                                <input class="form-check-input"
                                                       type="radio"
                                                       name="primary_image"
                                                       ng-checked="getCombinedPrimaryIndex() === $index"
                                                       ng-click="setPrimaryIndex($index)">
                                                <label class="form-check-label">Principal</label>
                                            </div>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger w-100 mt-2"
                                                    ng-click="removeExistingImage($index)">
                                                Quitar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3"
                                         ng-repeat="image in imagePreviews track by 'new-' + $index">
                                        <div class="border rounded-3 p-2 h-100 bg-white">
                                            <img ng-src="@{{ image.url }}"
                                                 alt="Nueva imagen"
                                                 style="width:100%; height:140px; object-fit:cover; border-radius:10px;">

                                            <div class="small text-muted mt-2 text-truncate">@{{ image.name }}</div>

                                            <div class="form-check mt-2">
                                                <input class="form-check-input"
                                                       type="radio"
                                                       name="primary_image"
                                                       ng-checked="getCombinedPrimaryIndex() === (existingImages.length + $index)"
                                                       ng-click="setPrimaryIndex(existingImages.length + $index)">
                                                <label class="form-check-label">Principal</label>
                                            </div>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger w-100 mt-2"
                                                    ng-click="removeNewImage($index)">
                                                Quitar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="ui-btn ui-btn--secondary" ng-click="closeFormModal()">
                                Cancelar
                            </button>

                            <button type="submit" class="ui-btn ui-btn--primary" ng-disabled="saving">
                                @{{ saving ? 'Guardando...' : (isEditMode ? 'Actualizar planta' : 'Guardar planta') }}
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

        <div class="ui-modal__dialog ui-modal__dialog--xl" role="dialog" aria-modal="true">
            <div class="ui-modal__header">
                <div>
                    <div class="ui-modal__eyebrow">Plantas</div>
                    <div class="ui-modal__title">Detalle de planta</div>
                </div>

                <button type="button" class="ui-icon-button" ng-click="closeViewModal()" aria-label="Cerrar">
                    ✕
                </button>
            </div>

            <div class="ui-modal__body">
                <div class="ui-modal__content" ng-if="selectedPlant">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <img
                                ng-if="selectedPlant.primary_image_url"
                                ng-src="@{{ selectedPlant.primary_image_url }}"
                                alt="@{{ selectedPlant.name }}"
                                style="width:100%; max-height:320px; object-fit:cover; border-radius:18px; border:1px solid #e5e7eb;">

                            <div
                                ng-if="!selectedPlant.primary_image_url"
                                class="d-flex align-items-center justify-content-center border rounded-4"
                                style="width:100%; height:320px; color:#9ca3af;">
                                Sin imagen principal
                            </div>

                            <div class="row g-2 mt-2" ng-if="selectedPlant.images.length">
                                <div class="col-4" ng-repeat="image in selectedPlant.images track by image.id">
                                    <img ng-src="@{{ image.url }}"
                                         alt="Imagen"
                                         style="width:100%; height:90px; object-fit:cover; border-radius:12px; border:1px solid #e5e7eb;">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Nombre</label>
                                    <div class="fw-semibold">@{{ selectedPlant.name || '—' }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Slug</label>
                                    <div>@{{ selectedPlant.slug || '—' }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Activo</label>
                                    <div>
                                        <span class="ui-badge ui-badge--soft badge"
                                              ng-class="selectedPlant.is_active ? 'bg-success' : 'bg-secondary'">
                                            @{{ selectedPlant.is_active ? 'Sí' : 'No' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Imágenes</label>
                                    <div>@{{ selectedPlant.images_count || 0 }}</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted">Descripción corta</label>
                                    <div>@{{ selectedPlant.short_description || '—' }}</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted">Descripción HTML</label>
                                    <div class="border rounded-3 p-3 bg-light" ng-bind-html="selectedPlant.description_html"></div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted">Cuidados</label>
                                    <div>@{{ selectedPlant.care_instructions || '—' }}</div>
                                </div>
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
@endsection

@push('scripts')
<script>
    window.plantsUrl = @json(route('admin.plants.index'));
</script>
<script src="{{ asset('js/plants.js') }}"></script>
@endpush