var app = angular.module('deliveries', ['angularUtils.directives.dirPagination']);

app.controller('deliveries', function ($http, $scope) {


    //Paginación
    $scope.currentPage = 1;
    $scope.pageSize = 10;

   

});