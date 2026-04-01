(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').controller('UserController', ['UserService', function (UserService) {
        var vm = this;

        vm.userList = [];
        vm.roles = [];
        vm.loading = false;
        vm.saving = false;
        vm.showCreateModal = false;
        vm.errorMessage = '';
        vm.newUser = defaultUser();

        function defaultUser() {
            return {
                name: '',
                email: '',
                username: '',
                password: '',
                role_id: '',
                is_active: true,
            };
        }

        vm.init = function () {
            vm.getUsers();
        };

        vm.getUsers = function () {
            vm.loading = true;

            return UserService.getUsers().then(function (data) {
                vm.userList = data.users || [];
                vm.roles = data.roles || [];
                vm.loading = false;
            }).catch(function () {
                vm.loading = false;
                vm.errorMessage = 'No se pudo cargar la lista de usuarios.';
            });
        };

        vm.openCreateModal = function () {
            vm.errorMessage = '';
            vm.newUser = defaultUser();
            vm.showCreateModal = true;
        };

        vm.closeCreateModal = function () {
            vm.showCreateModal = false;
            vm.errorMessage = '';
            vm.newUser = defaultUser();
        };

        vm.saveUser = function () {
            vm.saving = true;
            vm.errorMessage = '';

            return UserService.createUser(vm.newUser).then(function () {
                vm.saving = false;
                vm.closeCreateModal();
                return vm.getUsers();
            }).catch(function (error) {
                vm.saving = false;

                var payload = error && error.data ? error.data : {};
                if (payload.errors) {
                    var firstKey = Object.keys(payload.errors)[0];
                    vm.errorMessage = payload.errors[firstKey][0];
                } else {
                    vm.errorMessage = payload.message || 'No se pudo guardar el usuario.';
                }
            });
        };
    }]);
})(window.angular);
