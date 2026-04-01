(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').controller('CctController', ['CctService', 'AdminListControllerBuilder', function (CctService, AdminListControllerBuilder) {
        AdminListControllerBuilder({
            service: CctService,
            bootId: 'admin-ccts-bootstrap',
            defaultSortField: 'CLAVECCT',
        }).call(this);
    }]);
})(window.angular);
