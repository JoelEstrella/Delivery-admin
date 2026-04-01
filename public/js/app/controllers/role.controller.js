(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').controller('RoleController', ['RoleService', 'AdminListControllerBuilder', function (RoleService, AdminListControllerBuilder) {
        AdminListControllerBuilder({
            service: RoleService,
            bootId: 'admin-roles-bootstrap',
            defaultSortField: 'name',
        }).call(this);
    }]);
})(window.angular);
