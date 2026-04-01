(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').controller('UserController', ['UserService', 'AdminListControllerBuilder', function (UserService, AdminListControllerBuilder) {
        AdminListControllerBuilder({
            service: UserService,
            bootId: 'admin-users-bootstrap',
            defaultSortField: 'name',
        }).call(this);
    }]);
})(window.angular);
