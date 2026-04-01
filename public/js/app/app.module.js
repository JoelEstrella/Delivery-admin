(function (window, angular) {
    'use strict';

    if (!angular) {
        return;
    }

    angular.module('deliveryAdminApp', [])
        .config(['$interpolateProvider', function ($interpolateProvider) {
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
        }])
        .factory('AdminFormSubmitter', [function () {
            function csrfToken() {
                var meta = window.document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function appendFields(form, prefix, value) {
                if (value === null || typeof value === 'undefined') {
                    var empty = window.document.createElement('input');
                    empty.type = 'hidden';
                    empty.name = prefix;
                    empty.value = '';
                    form.appendChild(empty);
                    return;
                }

                if (Array.isArray(value)) {
                    value.forEach(function (item, index) {
                        appendFields(form, prefix + '[' + index + ']', item);
                    });
                    return;
                }

                if (typeof value === 'object') {
                    Object.keys(value).forEach(function (key) {
                        appendFields(form, prefix + '[' + key + ']', value[key]);
                    });
                    return;
                }

                var input = window.document.createElement('input');
                input.type = 'hidden';
                input.name = prefix;
                input.value = value;
                form.appendChild(input);
            }

            function submit(method, action, payload) {
                var form = window.document.createElement('form');
                form.method = 'POST';
                form.action = action;
                form.style.display = 'none';

                var token = window.document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = csrfToken();
                form.appendChild(token);

                if (method && method.toUpperCase() !== 'POST') {
                    var methodField = window.document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = method.toUpperCase();
                    form.appendChild(methodField);
                }

                if (payload && typeof payload === 'object') {
                    Object.keys(payload).forEach(function (key) {
                        appendFields(form, key, payload[key]);
                    });
                }

                window.document.body.appendChild(form);
                form.submit();
            }

            return {
                submit: submit,
            };
        }])
        .factory('AdminEntityStore', ['$q', 'AdminFormSubmitter', function ($q, AdminFormSubmitter) {
            function clone(value) {
                return angular.copy(value || []);
            }

            function toComparable(value) {
                if (value === null || typeof value === 'undefined') {
                    return '';
                }

                if (typeof value === 'string') {
                    return value.toLowerCase();
                }

                if (typeof value === 'number') {
                    return value;
                }

                if (typeof value === 'boolean') {
                    return value ? 1 : 0;
                }

                return String(value).toLowerCase();
            }

            return function (namespace) {
                var records = [];

                function hydrate(items) {
                    records = clone(items);
                    return $q.resolve(clone(records));
                }

                function all() {
                    return clone(records);
                }

                function getById(id) {
                    var key = parseInt(id, 10);
                    var record = records.filter(function (item) {
                        return parseInt(item.id, 10) === key;
                    })[0] || null;

                    return clone(record);
                }

                function replace(record) {
                    var key = parseInt(record.id, 10);
                    records = records.map(function (item) {
                        return parseInt(item.id, 10) === key ? clone(record) : item;
                    });

                    return clone(record);
                }

                function add(record) {
                    records.unshift(clone(record));
                    return clone(record);
                }

                function remove(id) {
                    var key = parseInt(id, 10);
                    records = records.filter(function (item) {
                        return parseInt(item.id, 10) !== key;
                    });

                    return true;
                }

                function search(query, fields) {
                    var term = (query || '').toString().trim().toLowerCase();
                    var safeFields = Array.isArray(fields) ? fields : [];

                    if (!term) {
                        return all();
                    }

                    return records.filter(function (record) {
                        return safeFields.some(function (field) {
                            var value = field.split('.').reduce(function (carry, segment) {
                                if (!carry || typeof carry !== 'object') {
                                    return null;
                                }

                                return carry[segment];
                            }, record);

                            return toComparable(value).toString().indexOf(term) !== -1;
                        });
                    }).map(function (item) {
                        return clone(item);
                    });
                }

                function submit(method, url, payload) {
                    if (!url) {
                        return $q.resolve(null);
                    }

                    AdminFormSubmitter.submit(method, url, payload || {});
                    return $q.resolve(true);
                }

                return {
                    namespace: namespace,
                    hydrate: hydrate,
                    all: all,
                    getAll: function () {
                        return $q.resolve(all());
                    },
                    getById: function (id) {
                        return $q.resolve(getById(id));
                    },
                    create: function (payload, url) {
                        if (url) {
                            return submit('POST', url, payload);
                        }

                        return $q.resolve(add(payload));
                    },
                    update: function (id, payload, url) {
                        var record = payload || {};
                        record.id = record.id || id;

                        if (url) {
                            return submit('PUT', url, record);
                        }

                        return $q.resolve(replace(record));
                    },
                    delete: function (id, url) {
                        if (url) {
                            return submit('DELETE', url, { id: id });
                        }

                        return $q.resolve(remove(id));
                    },
                    search: search,
                };
            };
        }])
        .factory('AdminListControllerBuilder', ['$window', function ($window) {
            function readBoot(bootId) {
                if (!bootId) {
                    return {};
                }

                var element = $window.document.getElementById(bootId);

                if (!element) {
                    return {};
                }

                try {
                    return JSON.parse(element.textContent || element.innerText || '{}');
                } catch (error) {
                    return {};
                }
            }

            function getFieldValue(record, field) {
                if (!field) {
                    return null;
                }

                return field.split('.').reduce(function (carry, segment) {
                    if (carry === null || typeof carry === 'undefined') {
                        return null;
                    }

                    return carry[segment];
                }, record);
            }

            function formatDate(value, includeTime) {
                if (!value) {
                    return '—';
                }

                var date = new Date(value);

                if (isNaN(date.getTime())) {
                    return value;
                }

                var day = String(date.getDate()).padStart(2, '0');
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var year = date.getFullYear();

                if (!includeTime) {
                    return day + '/' + month + '/' + year;
                }

                var hours = String(date.getHours()).padStart(2, '0');
                var minutes = String(date.getMinutes()).padStart(2, '0');

                return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
            }

            function sortValue(column, value) {
                if (column && (column.type === 'date' || column.type === 'datetime')) {
                    var parsed = value ? new Date(value).getTime() : 0;
                    return isNaN(parsed) ? 0 : parsed;
                }

                if (value === null || typeof value === 'undefined') {
                    return '';
                }

                if (typeof value === 'number') {
                    return value;
                }

                if (typeof value === 'boolean') {
                    return value ? 1 : 0;
                }

                return String(value).toLowerCase();
            }

            function compareValues(left, right, direction) {
                var leftValue = left;
                var rightValue = right;

                if (typeof leftValue === 'string') {
                    leftValue = leftValue.toLowerCase();
                }

                if (typeof rightValue === 'string') {
                    rightValue = rightValue.toLowerCase();
                }

                if (leftValue < rightValue) {
                    return direction ? 1 : -1;
                }

                if (leftValue > rightValue) {
                    return direction ? -1 : 1;
                }

                return 0;
            }

            return function (options) {
                options = options || {};

                return function () {
                    var vm = this;

                    vm.records = [];
                    vm.filteredRecords = [];
                    vm.columns = [];
                    vm.filters = { search: '' };
                    vm.loading = true;
                    vm.sort = {
                        field: options.defaultSortField || 'id',
                        reverse: false,
                    };
                    vm.routes = {};
                    vm.bootId = options.bootId || '';
                    vm.service = options.service;

                    vm.init = function (bootId) {
                        vm.bootId = bootId || vm.bootId;
                        var boot = readBoot(vm.bootId);

                        vm.columns = boot.columns || [];
                        vm.routes = boot.routes || {};
                        vm.loading = true;

                        vm.service.hydrate(boot.records || []).then(function (records) {
                            vm.records = records;
                            vm.applyFilters();
                            vm.loading = false;
                        });
                    };

                    vm.applyFilters = function () {
                        var search = (vm.filters.search || '').trim().toLowerCase();
                        var fields = vm.columns.map(function (column) {
                            return column.field;
                        });

                        vm.filteredRecords = fields.length ? vm.service.search(search, fields) : vm.service.all();

                        if (vm.sort.field) {
                            vm.filteredRecords.sort(function (left, right) {
                                var column = vm.columns.filter(function (item) {
                                    return item.field === vm.sort.field;
                                })[0] || {};

                                var leftValue = getFieldValue(left, vm.sort.field);
                                var rightValue = getFieldValue(right, vm.sort.field);

                                leftValue = sortValue(column, leftValue);
                                rightValue = sortValue(column, rightValue);

                                return compareValues(leftValue, rightValue, vm.sort.reverse);
                            });
                        }
                    };

                    vm.toggleSort = function (column) {
                        if (!column || !column.field) {
                            return;
                        }

                        if (vm.sort.field === column.field) {
                            vm.sort.reverse = !vm.sort.reverse;
                        } else {
                            vm.sort.field = column.field;
                            vm.sort.reverse = false;
                        }

                        vm.applyFilters();
                    };

                    vm.clearFilters = function () {
                        vm.filters.search = '';
                        vm.applyFilters();
                    };

                    vm.reload = function () {
                        vm.init(vm.bootId);
                    };

                    vm.getValue = function (record, field) {
                        return getFieldValue(record, field);
                    };

                    vm.formatValue = function (value) {
                        if (value === null || typeof value === 'undefined' || value === '') {
                            return '—';
                        }

                        return value;
                    };

                    vm.formatDate = function (value) {
                        return formatDate(value, false);
                    };

                    vm.formatDateTime = function (value) {
                        return formatDate(value, true);
                    };

                    vm.resolveBadgeLabel = function (column, value) {
                        if (!column || !column.map) {
                            return value;
                        }

                        var key = String(value);
                        var badge = column.map[key];

                        return badge ? badge.label : value;
                    };

                    vm.resolveBadgeClass = function (column, value) {
                        if (!column || !column.map) {
                            return 'secondary';
                        }

                        var key = String(value);
                        var badge = column.map[key];

                        return badge ? badge.class : 'secondary';
                    };

                    vm.viewUrl = function (record) {
                        return vm.routes.base ? vm.routes.base + '/' + record.id : '#';
                    };

                    vm.editUrl = function (record) {
                        return vm.routes.base ? vm.routes.base + '/' + record.id + '/edit' : '#';
                    };

                    vm.destroy = function (record) {
                        if (!vm.routes.base || !record) {
                            return;
                        }

                        if ($window.confirm('¿Deseas eliminar este registro?')) {
                            vm.service.delete(record.id, vm.routes.base + '/' + record.id);
                        }
                    };

                    return vm;
                };
            };
        }]);
})(window, window.angular);
