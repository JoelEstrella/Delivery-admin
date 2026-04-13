var app = angular.module('organizations', ['angularUtils.directives.dirPagination']);

app.controller('organizations', function ($http, $scope) {

    $scope.items = [];
    $scope.saving = false;
    $scope.loading = false;

    //Modal
    $scope.title = "";
    $scope.subtitle = "";
    $scope.submitLabel = "";

    //Paginación
    $scope.currentPage = 1;
    $scope.pageSize = 10;

    const moduleTitle = title.users;
    const url = 'directions';

    // Listar Registros
    $scope.getList = () => {

        $scope.loading = true;

        $http({
            url: url,
            method: 'GET'
        }).then(
            function successCallback(response) {
                console.log('exitoso: ', response);
                $scope.items = response.data.data;
            },
            function errorCallback(error) {
                console.log('error: ', error);
                const errors = getErrors(error)
                showAlert('error', moduleTitle, errors);

            }).finally(function () {
                $scope.loading = false;
            });

    }

    //Abrir Modal para creación
    $scope.create = () => {

        $http({
            url: `${url}/create`,
            method: 'GET'
        }).then(
            function successCallback(response) {
                console.log('exitoso: ', response);

                const data = response.data.data

                const { title, subtitle, submitLabel } = data;

                $scope.title = title;
                $scope.subtitle = subtitle;
                $scope.submitLabel = submitLabel;

                openCreateModal();
            },
            function errorCallback(error) {
                console.log('error: ', error);
                const errors = getErrors(error)
                showAlert('error', moduleTitle, errors);
            }
        );

    }

    // Guardar Usuarios
    $scope.save = () => {

        if ($scope.createForm.$invalid) {
            angular.forEach($scope.createForm.$error, function (fields) {
                angular.forEach(fields, function (field) {
                    field.$setTouched();
                });
            });

            return;
        }

        console.log("Guardando ...");

        $scope.saving = true;

        $http({
            url: url,
            method: 'POST',
            data: $scope.newItem
        }).then(
            function (response) {
                console.log("exitoso:", response);
                const { data, message } = response.data;
                $scope.items.unshift(data);
                $scope.resetForm();
                showAlert('success', moduleTitle, message);
            }
        ).catch(function (error) {
            console.log("error:", error);
            const errors = getErrors(error)
            showAlert('error', moduleTitle, errors);

        }).finally(function () {
            $scope.saving = false;
            $scope.closeCreateModal();
        });

    }

    //Abrir Modal para edición
    $scope.edit = (item) => {
        console.log("Abriendo Modal");

        $http({
            url: `${url}/${item.id}/edit`,
            method: 'GET'
        }).then(
            function successCallback(response) {
                console.log('exitoso: ', response);

                const data = response.data.data

                const { title, subtitle, submitLabel, direction } = data;

                $scope.title = title;
                $scope.subtitle = subtitle;
                $scope.submitLabel = submitLabel;
                $scope.formData = angular.copy(direction);

                console.log($scope.formData);


                openEditModal();
            },
            function errorCallback(error) {
                console.log('error: ', error);
                const errors = getErrors(error)
                showAlert('error', moduleTitle, errors);
            }
        );

    }

    // Actualizar Usuarios
    $scope.update = () => {

        if ($scope.editForm.$invalid) {
            angular.forEach($scope.editForm.$error, function (fields) {
                angular.forEach(fields, function (field) {
                    field.$setTouched();
                });
            });

            return;
        }

        console.log("Guardando ACTUALIZACION ...", $scope.formData);

        $scope.saving = true;

        $http({
            url: `${url}/${$scope.formData.id}`,
            method: 'PUT',
            data: $scope.formData
        }).then(
            function (response) {
                console.log("exitoso:", response);
                const { data, message } = response.data;

                const item = $scope.items.find(item => item.id === data.id);

                if (item) {
                    Object.assign(item, data);
                }

                showAlert('success', moduleTitle, message);
            }
        ).catch(function (error) {
            console.log("error:", error);
            const errors = getErrors(error)
            showAlert('error', moduleTitle, errors);

        }).finally(function () {
            $scope.saving = false;
            $scope.closeEditModal();
        });

    }

    // Abrir Modal
    const openCreateModal = () => {
        $scope.resetForm();
        $scope.showCreateModal = true;
    }

    // Cerrar Modal
    $scope.closeCreateModal = () => {
        $scope.showCreateModal = false;
    }

    // Abrir Modal
    const openEditModal = () => {
        $scope.showEditModal = true;
    }

    // Cerrar Modal
    $scope.closeEditModal = () => {
        $scope.showEditModal = false;
    }


    //Resetear Form
    $scope.resetForm = () => {
        $scope.newItem = {
            is_active: true
        };

        if ($scope.createForm) {
            $scope.createForm.$setPristine();
            $scope.createForm.$setUntouched();
        }

    };

});