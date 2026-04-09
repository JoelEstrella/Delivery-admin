var app = angular.module('plants', []);

app.controller('plants', function ($http, $scope, $timeout, $sce) {
    var quillInstance = null;

    $scope.plants = [];
    $scope.loading = false;
    $scope.saving = false;
    $scope.submitted = false;

    $scope.showFormModal = false;
    $scope.showViewModal = false;
    $scope.isEditMode = false;

    $scope.selectedPlant = null;
    $scope.currentPlantId = null;
    $scope.searchTimeout = null;
    $scope.imageError = '';

    $scope.filters = {
        search: ''
    };

    $scope.formData = {};
    $scope.newImages = [];
    $scope.imagePreviews = [];
    $scope.existingImages = [];
    $scope.removedImageIds = [];
    $scope.primaryImageIndex = 0;

    $scope.init = function () {
        $scope.resetForm();
        $scope.getPlants();
    };

    $scope.resetForm = function () {
        $scope.formData = {
            name: '',
            slug: '',
            short_description: '',
            description_html: '',
            care_instructions: '',
            is_active: true
        };

        $scope.newImages = [];
        $scope.imagePreviews = [];
        $scope.existingImages = [];
        $scope.removedImageIds = [];
        $scope.primaryImageIndex = 0;
        $scope.currentPlantId = null;
        $scope.isEditMode = false;
        $scope.submitted = false;
        $scope.imageError = '';

        if ($scope.plantForm) {
            $scope.plantForm.$setPristine();
            $scope.plantForm.$setUntouched();
        }
    };

    $scope.getPlants = function () {
        $scope.loading = true;

        $http({
            url: window.plantsUrl,
            method: 'GET',
            params: {
                search: $scope.filters.search
            },
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            $scope.plants = response.data.plants || [];
        }).catch(function (error) {
            console.error('Error al cargar plantas:', error);
            $scope.showAlert('error', 'Error', 'No se pudieron cargar las plantas.');
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.onSearchChange = function () {
        if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
        }

        $scope.searchTimeout = $timeout(function () {
            $scope.getPlants();
        }, 350);
    };

    $scope.clearSearch = function () {
        $scope.filters.search = '';
        $scope.getPlants();
    };

    $scope.openCreateModal = function () {
        $scope.resetForm();
        $scope.showFormModal = true;

        $timeout(function () {
            initEditor('');
        }, 100);
    };

    $scope.openEditModal = function (plant) {
        $scope.resetForm();
        $scope.isEditMode = true;
        $scope.currentPlantId = plant.id;
        $scope.showFormModal = true;

        $http({
            url: window.plantsUrl + '/' + plant.id,
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            var data = response.data.plant || {};

            $scope.formData = {
                name: data.name || '',
                slug: data.slug || '',
                short_description: data.short_description || '',
                description_html: data.description_html || '',
                care_instructions: data.care_instructions || '',
                is_active: !!data.is_active
            };

            $scope.existingImages = angular.copy(data.images || []);

            var primaryExistingIndex = $scope.existingImages.findIndex(function (image) {
                return !!image.is_primary;
            });

            $scope.primaryImageIndex = primaryExistingIndex >= 0 ? primaryExistingIndex : 0;

            $timeout(function () {
                initEditor($scope.formData.description_html || '');
            }, 100);
        }).catch(function (error) {
            console.error('Error al cargar detalle de planta:', error);
            $scope.showAlert('error', 'Error', 'No se pudo cargar la información de la planta.');
            $scope.closeFormModal();
        });
    };

    $scope.openViewModal = function (plant) {
        $scope.selectedPlant = null;
        $scope.showViewModal = true;

        $http({
            url: window.plantsUrl + '/' + plant.id,
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            var data = response.data.plant || {};
            data.description_html = $sce.trustAsHtml(data.description_html || '—');
            $scope.selectedPlant = data;
        }).catch(function (error) {
            console.error('Error al cargar detalle de planta:', error);
            $scope.showAlert('error', 'Error', 'No se pudo cargar el detalle de la planta.');
            $scope.closeViewModal();
        });
    };

    $scope.closeFormModal = function () {
        $scope.showFormModal = false;
        destroyEditor();

        $timeout(function () {
            $scope.resetForm();
        }, 150);
    };

    $scope.closeViewModal = function () {
        $scope.showViewModal = false;
        $scope.selectedPlant = null;
    };

    $scope.editFromView = function () {
        if (!$scope.selectedPlant) return;

        var plant = angular.copy($scope.selectedPlant);
        $scope.closeViewModal();

        $timeout(function () {
            $scope.openEditModal(plant);
        }, 150);
    };

    $scope.normalizeSlug = function () {
        if (!$scope.formData.slug && $scope.formData.name) {
            $scope.formData.slug = $scope.formData.name;
        }

        if ($scope.formData.slug) {
            $scope.formData.slug = $scope.formData.slug
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9-_]/g, '');
        }
    };

    $scope.handleImageSelection = function (input) {
        $scope.$apply(function () {
            var files = Array.from(input.files || []);
            $scope.imageError = '';

            if (!files.length) {
                return;
            }

            var totalAfterAdd = $scope.existingImages.length + $scope.newImages.length + files.length;

            if (totalAfterAdd > 7) {
                $scope.imageError = 'Solo puedes cargar un máximo de 7 imágenes.';
                input.value = '';
                return;
            }

            var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                if (allowedTypes.indexOf(file.type) === -1) {
                    $scope.imageError = 'Solo se permiten imágenes JPG, JPEG, PNG y WEBP.';
                    input.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    $scope.imageError = 'Cada imagen debe pesar máximo 5 MB.';
                    input.value = '';
                    return;
                }
            }

            files.forEach(function (file) {
                $scope.newImages.push(file);

                var reader = new FileReader();
                reader.onload = function (e) {
                    $scope.$apply(function () {
                        $scope.imagePreviews.push({
                            name: file.name,
                            url: e.target.result
                        });
                    });
                };
                reader.readAsDataURL(file);
            });

            input.value = '';
        });
    };

    $scope.removeExistingImage = function (index) {
        var image = $scope.existingImages[index];

        if (image && image.id) {
            $scope.removedImageIds.push(image.id);
        }

        $scope.existingImages.splice(index, 1);

        if ($scope.primaryImageIndex >= ($scope.existingImages.length + $scope.newImages.length)) {
            $scope.primaryImageIndex = 0;
        }
    };

    $scope.removeNewImage = function (index) {
        $scope.newImages.splice(index, 1);
        $scope.imagePreviews.splice(index, 1);

        if ($scope.primaryImageIndex >= ($scope.existingImages.length + $scope.newImages.length)) {
            $scope.primaryImageIndex = 0;
        }
    };

    $scope.setPrimaryIndex = function (index) {
        $scope.primaryImageIndex = index;
    };

    $scope.getCombinedPrimaryIndex = function () {
        return $scope.primaryImageIndex;
    };

    $scope.submitPlant = function (form) {
        $scope.submitted = true;
        $scope.formData.description_html = getEditorContent();

        if (form.$invalid) {
            angular.forEach(form.$error, function (fields) {
                angular.forEach(fields, function (field) {
                    field.$setTouched();
                });
            });

            $scope.showAlert('warning', 'Formulario incompleto', 'Revisa los campos obligatorios.');
            return;
        }

        $scope.saving = true;

        var formData = new FormData();
        formData.append('name', $scope.formData.name || '');
        formData.append('slug', $scope.formData.slug || '');
        formData.append('short_description', $scope.formData.short_description || '');
        formData.append('description_html', $scope.formData.description_html || '');
        formData.append('care_instructions', $scope.formData.care_instructions || '');
        formData.append('is_active', $scope.formData.is_active ? 1 : 0);
        formData.append('primary_image_index', $scope.primaryImageIndex);

        $scope.newImages.forEach(function (file) {
            formData.append('images[]', file);
        });

        $scope.removedImageIds.forEach(function (id) {
            formData.append('removed_images[]', id);
        });

        var url = $scope.isEditMode
            ? window.plantsUrl + '/' + $scope.currentPlantId
            : window.plantsUrl;

        if ($scope.isEditMode) {
            formData.append('_method', 'PUT');
        }

        $http({
            url: url,
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': undefined
            },
            data: formData,
            transformRequest: angular.identity
        }).then(function (response) {
            $scope.showAlert('success', 'Éxito', response.data.message || 'Operación realizada correctamente.');
            $scope.closeFormModal();
            $scope.getPlants();
        }).catch(function (error) {
            console.error('Error al guardar planta:', error);

            if (error.status === 422 && error.data) {
                var message = 'Revisa la información capturada.';

                if (error.data.message) {
                    message = error.data.message;
                } else if (error.data.errors) {
                    var firstKey = Object.keys(error.data.errors)[0];
                    if (firstKey && error.data.errors[firstKey].length) {
                        message = error.data.errors[firstKey][0];
                    }
                }

                $scope.showAlert('warning', 'Validación', message);
                return;
            }

            $scope.showAlert('error', 'Error', 'No se pudo guardar la planta.');
        }).finally(function () {
            $scope.saving = false;
        });
    };

    $scope.confirmDelete = function (plant) {
        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar planta?',
            text: 'La planta se eliminará del catálogo.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#333'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $http({
                url: window.plantsUrl + '/' + plant.id,
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(function (response) {
                $scope.showAlert('success', 'Éxito', response.data.message || 'Planta eliminada correctamente.');
                $scope.getPlants();
            }).catch(function (error) {
                console.error('Error al eliminar planta:', error);
                $scope.showAlert('error', 'Error', 'No se pudo eliminar la planta.');
            });
        });
    };

    $scope.showAlert = function (icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#333'
        });
    };

    function initEditor(content) {
        if (typeof Quill === 'undefined') {
            console.warn('Quill no está cargado.');
            return;
        }

        var editorElement = document.getElementById('plant-description-editor');
        if (!editorElement) {
            return;
        }

        editorElement.innerHTML = '';

        quillInstance = new Quill('#plant-description-editor', {
            theme: 'snow',
            placeholder: 'Escribe la descripción...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        if (content) {
            quillInstance.root.innerHTML = content;
        }

        quillInstance.on('text-change', function () {
            $scope.$evalAsync(function () {
                $scope.formData.description_html = quillInstance.root.innerHTML;
            });
        });
    }

    function destroyEditor() {
        quillInstance = null;
    }

    function getEditorContent() {
        return quillInstance ? quillInstance.root.innerHTML : ($scope.formData.description_html || '');
    }
});