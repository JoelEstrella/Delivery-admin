(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').controller('DeliveryFormController', ['$window', function ($window) {
        var vm = this;

        vm.items = [];
        vm.plants = [];

        vm.init = function () {
            var data = $window.deliveryFormData || {};
            var items = data.items && data.items.length ? data.items : [{ plant_id: '', quantity: 1 }];

            vm.items = angular.copy(items);
            vm.plants = angular.copy(data.plants || []);
        };

        vm.addItem = function () {
            vm.items.push({ plant_id: '', quantity: 1 });
        };

        vm.removeItem = function (item) {
            var index = vm.items.indexOf(item);

            if (index > -1) {
                vm.items.splice(index, 1);
            }

            if (!vm.items.length) {
                vm.addItem();
            }
        };
    }]);
})(window.angular);
