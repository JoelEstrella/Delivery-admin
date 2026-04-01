(function (angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp').factory('UserService', ['$window', function ($window) {
        var defaultRoutes = $window.userModuleRoutes || {};

        function routes() {
            return $window.userModuleRoutes || defaultRoutes;
        }

        function csrfToken() {
            var meta = $window.document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function toFormData(payload) {
            var formData = new $window.FormData();

            Object.keys(payload || {}).forEach(function (key) {
                var value = payload[key];

                if (value === null || typeof value === 'undefined') {
                    return;
                }

                if (typeof value === 'boolean') {
                    formData.append(key, value ? '1' : '0');
                    return;
                }

                formData.append(key, value);
            });

            formData.append('_token', csrfToken());
            return formData;
        }

        function readJson(response) {
            return response.json().catch(function () {
                return {};
            });
        }

        function requestJson(url, options) {
            var config = options || {};
            config.credentials = 'same-origin';
            config.headers = Object.assign({
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }, config.headers || {});

            return $window.fetch(url, config).then(function (response) {
                return readJson(response).then(function (data) {
                    if (!response.ok) {
                        var error = new Error(data.message || 'Error de servidor');
                        error.status = response.status;
                        error.data = data;
                        throw error;
                    }

                    return data;
                });
            });
        }

        return {
            getUsers: function () {
                return requestJson(routes().index, { method: 'GET' });
            },
            createUser: function (payload) {
                return requestJson(routes().store, {
                    method: 'POST',
                    body: toFormData(payload),
                });
            },
        };
    }]);
})(window.angular);
