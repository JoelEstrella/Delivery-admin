(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').factory('UserService', ['AdminEntityStore', function (AdminEntityStore) {
        return AdminEntityStore('users');
    }]);
})(window.angular);
