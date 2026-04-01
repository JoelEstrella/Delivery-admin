(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').factory('RoleService', ['AdminEntityStore', function (AdminEntityStore) {
        return AdminEntityStore('roles');
    }]);
})(window.angular);
