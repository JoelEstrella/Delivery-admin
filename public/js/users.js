var app = angular.module('users', []);

app.controller('users', function ($http, $scope) {

    $scope.users = [];
    $scope.loading = false;

    $scope.getUsers = () => {
        $scope.loading = true;
        $http({
            url: 'users',
            method: 'GET'
        }).then(
            function successCallback(response) {
                console.log('exitoso: ', response.data);
                $scope.loading = false;
                $scope.users = response.data.users;
                $scope.roles = response.data.roles;

            },
            function errorCallback(response) {
                console.log('error: ', response);
                $scope.loading = false;
            }
        );

    }

    $scope.openCreateModal = () => {
        $scope.showCreateModal = true;
    }

    $scope.closeCreateModal = () => {
        $scope.showCreateModal = false;
    }

    $scope.saveUser = () => {

        $scope.saving = true;
        console.log($scope.newUser);

        $http({
            url: 'users',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            data: $scope.newUser
        }).then(
            function successCallback(response) {
                console.log('exitoso: ', response.data);
            },
            function errorCallback(response) {
                console.log('error: ', response);
            }
        );

    }





});