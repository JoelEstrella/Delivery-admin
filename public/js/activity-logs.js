var app = angular.module('activityLogsApp', []);

app.controller('ActivityLogsController', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
    $scope.records = [];
    $scope.loading = false;
    $scope.showDetailModal = false;
    $scope.selectedRecord = null;

    $scope.filters = {
        search: ''
    };

    $scope.pagination = {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        from: 0,
        to: 0
    };

    $scope.pages = [];
    var searchTimeout = null;

    $scope.fetchRecords = function (page) {
        $scope.loading = true;

        $http.get(window.activityLogsUrl, {
            params: {
                page: page || 1,
                search: $scope.filters.search || ''
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(function (response) {
            var data = response.data || {};

            $scope.records = data.data || [];
            $scope.pagination.current_page = data.current_page || 1;
            $scope.pagination.last_page = data.last_page || 1;
            $scope.pagination.per_page = data.per_page || 15;
            $scope.pagination.total = data.total || 0;
            $scope.pagination.from = data.from || 0;
            $scope.pagination.to = data.to || 0;

            $scope.pages = buildPages(
                $scope.pagination.current_page,
                $scope.pagination.last_page
            );
        }).catch(function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la bitácora.',
                confirmButtonColor: '#333'
            });
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.onSearchChange = function () {
        if (searchTimeout) {
            $timeout.cancel(searchTimeout);
        }

        searchTimeout = $timeout(function () {
            $scope.fetchRecords(1);
        }, 400);
    };

    $scope.clearFilters = function () {
        $scope.filters.search = '';
        $scope.fetchRecords(1);
    };

    $scope.goToPage = function (page) {
        if (!page || page === '...' || page < 1 || page > $scope.pagination.last_page || $scope.loading) {
            return;
        }

        $scope.fetchRecords(page);
    };

    $scope.openDetail = function (record) {
        $scope.selectedRecord = angular.copy(record);
        $scope.showDetailModal = true;
        angular.element(document.body).addClass('modal-open');
    };

    $scope.closeDetail = function () {
        $scope.selectedRecord = null;
        $scope.showDetailModal = false;
        angular.element(document.body).removeClass('modal-open');
    };

    $scope.formatJson = function (value) {
        if (!value) {
            return 'Sin información';
        }

        try {
            return JSON.stringify(value, null, 2);
        } catch (e) {
            return 'No se pudo formatear la información';
        }
    };

    function buildPages(currentPage, lastPage) {
        var pages = [];

        if (lastPage <= 7) {
            for (var i = 1; i <= lastPage; i++) {
                pages.push(i);
            }
            return pages;
        }

        pages.push(1);

        if (currentPage > 3) {
            pages.push('...');
        }

        var start = Math.max(2, currentPage - 1);
        var end = Math.min(lastPage - 1, currentPage + 1);

        for (var j = start; j <= end; j++) {
            pages.push(j);
        }

        if (currentPage < lastPage - 2) {
            pages.push('...');
        }

        pages.push(lastPage);

        return pages;
    }

    $scope.fetchRecords(1);
}]);