var app = angular.module('roles', []);

app.controller('roles', function ($http, $scope, $timeout) {
    $scope.roles = [];
    $scope.permissions = [];
    $scope.groupedPermissions = [];

    $scope.loading = false;
    $scope.saving = false;
    $scope.submitted = false;

    $scope.showFormModal = false;
    $scope.showViewModal = false;
    $scope.isEditMode = false;

    $scope.selectedRole = null;
    $scope.currentRoleId = null;
    $scope.searchTimeout = null;

    $scope.filters = {
        search: ''
    };

    $scope.formData = {};

    $scope.init = function () {
        $scope.resetForm();
        $scope.getRoles();
    };

    $scope.resetForm = function () {
        $scope.formData = {
            name: '',
            slug: '',
            description: '',
            is_active: true,
            permissions: []
        };

        $scope.currentRoleId = null;
        $scope.isEditMode = false;
        $scope.submitted = false;

        if ($scope.roleForm) {
            $scope.roleForm.$setPristine();
            $scope.roleForm.$setUntouched();
        }
    };

    $scope.onSearchChange = function () {
        if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
        }

        $scope.searchTimeout = $timeout(function () {
            $scope.getRoles();
        }, 350);
    };

    $scope.groupPermissions = function () {
        var grouped = {};

        angular.forEach($scope.permissions, function (permission) {
            if (!grouped[permission.module]) {
                grouped[permission.module] = {
                    module: permission.module,
                    items: []
                };
            }

            grouped[permission.module].items.push(permission);
        });

        $scope.groupedPermissions = Object.keys(grouped).map(function (key) {
            return grouped[key];
        });
    };

    $scope.getRoles = function () {
        $scope.loading = true;

        $http({
            url: window.rolesUrl,
            method: 'GET',
            params: {
                search: $scope.filters.search
            },
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            $scope.roles = response.data.roles || [];
            $scope.permissions = response.data.permissions || [];
            $scope.groupPermissions();
        }).catch(function (error) {
            console.error('Error al cargar roles:', error);
            $scope.showAlert('error', 'Error', 'No se pudieron cargar los roles.');
        }).finally(function () {
            $scope.loading = false;
            $timeout(function () {
                if (window.feather) {
                    window.feather.replace();
                }
            });
        });
    };

    $scope.clearSearch = function () {
        $scope.filters.search = '';
        $scope.getRoles();
    };

    $scope.openCreateModal = function () {
        $scope.resetForm();
        $scope.showFormModal = true;
    };

    $scope.openEditModal = function (role) {
        $scope.resetForm();
        $scope.isEditMode = true;
        $scope.currentRoleId = role.id;
        $scope.showFormModal = true;

        $http({
            url: window.rolesUrl + '/' + role.id,
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            var data = response.data.role;

            $scope.formData = {
                name: data.name || '',
                slug: data.slug || '',
                description: data.description || '',
                is_active: !!data.is_active,
                permissions: angular.copy(data.permission_ids || [])
            };
        }).catch(function (error) {
            console.error('Error al cargar detalle del rol:', error);
            $scope.showAlert('error', 'Error', 'No se pudo cargar la información del rol.');
            $scope.closeFormModal();
        });
    };

    $scope.openViewModal = function (role) {
        $scope.selectedRole = null;
        $scope.showViewModal = true;

        $http({
            url: window.rolesUrl + '/' + role.id,
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            $scope.selectedRole = response.data.role;
        }).catch(function (error) {
            console.error('Error al cargar detalle del rol:', error);
            $scope.showAlert('error', 'Error', 'No se pudo cargar el detalle del rol.');
            $scope.closeViewModal();
        });
    };

    $scope.closeFormModal = function () {
        $scope.showFormModal = false;
        $timeout(function () {
            $scope.resetForm();
        }, 150);
    };

    $scope.closeViewModal = function () {
        $scope.showViewModal = false;
        $scope.selectedRole = null;
    };

    $scope.editFromView = function () {
        if (!$scope.selectedRole) return;
        var role = angular.copy($scope.selectedRole);
        $scope.closeViewModal();
        $timeout(function () {
            $scope.openEditModal(role);
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

    $scope.hasPermission = function (permissionId) {
        return $scope.formData.permissions.indexOf(permissionId) !== -1;
    };

    $scope.togglePermission = function (permissionId) {
        var index = $scope.formData.permissions.indexOf(permissionId);

        if (index === -1) {
            $scope.formData.permissions.push(permissionId);
        } else {
            $scope.formData.permissions.splice(index, 1);
        }
    };

    $scope.submitRole = function (form) {
        $scope.submitted = true;

        if (form.$invalid || !$scope.formData.permissions.length) {
            angular.forEach(form.$error, function (fields) {
                angular.forEach(fields, function (field) {
                    field.$setTouched();
                });
            });

            $scope.showAlert('warning', 'Formulario incompleto', 'Revisa los campos obligatorios.');
            return;
        }

        $scope.saving = true;

        var payload = angular.copy($scope.formData);
        payload.is_active = payload.is_active ? 1 : 0;

        var method = $scope.isEditMode ? 'PUT' : 'POST';
        var url = $scope.isEditMode ? window.rolesUrl + '/' + $scope.currentRoleId : window.rolesUrl;

        $http({
            url: url,
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: payload
        }).then(function (response) {
            $scope.showAlert('success', 'Éxito', response.data.message || 'Operación realizada correctamente.');
            $scope.closeFormModal();
            $scope.getRoles();
        }).catch(function (error) {
            console.error('Error al guardar rol:', error);

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

            $scope.showAlert('error', 'Error', 'No se pudo guardar el rol.');
        }).finally(function () {
            $scope.saving = false;
        });
    };

    $scope.confirmDelete = function (role) {
        Swal.fire({
            icon: 'warning',
            title: '¿Desactivar rol?',
            text: 'El rol dejará de estar disponible para uso.',
            showCancelButton: true,
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#333'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $http({
                url: window.rolesUrl + '/' + role.id,
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(function (response) {
                $scope.showAlert('success', 'Éxito', response.data.message || 'Rol desactivado correctamente.');
                $scope.getRoles();
            }).catch(function (error) {
                console.error('Error al eliminar rol:', error);

                var message = 'No se pudo desactivar el rol.';

                if (error.status === 422 && error.data && error.data.message) {
                    message = error.data.message;
                }

                $scope.showAlert('error', 'Error', message);
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
});