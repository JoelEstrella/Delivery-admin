(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').factory('CctService', ['AdminEntityStore', function (AdminEntityStore) {
        return AdminEntityStore('ccts');
    }]);
})(window.angular);
