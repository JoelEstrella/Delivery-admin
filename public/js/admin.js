(function ($) {
    var spanishDataTable = {
        processing: 'Procesando...',
        search: 'Buscar:',
        lengthMenu: 'Mostrar _MENU_ registros',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        infoEmpty: 'Mostrando 0 a 0 de 0 registros',
        infoFiltered: '(filtrado de _MAX_ registros totales)',
        loadingRecords: 'Cargando...',
        zeroRecords: 'No se encontraron resultados',
        emptyTable: 'No hay datos disponibles',
        paginate: {
            first: 'Primero',
            previous: 'Anterior',
            next: 'Siguiente',
            last: 'Último'
        }
    };

    function initSidebarToggle() {
        $('#sidebarToggle').on('click', function (event) {
            event.preventDefault();
            document.body.classList.toggle('sidebar-collapsed');
        });
    }

    function initDataTables() {
        if (!$.fn.DataTable) {
            return;
        }

        $('.datatable').each(function () {
            var $table = $(this);
            var options = {
                responsive: true,
                language: spanishDataTable
            };

            if ($table.data('server-pagination')) {
                options.paging = false;
                options.searching = false;
                options.info = false;
                options.ordering = true;
            }

            $table.DataTable(options);
        });
    }

    function initDeleteConfirmation() {
        $(document).on('submit', 'form[data-confirm]', function (event) {
            var message = $(this).data('confirm-message') || '¿Seguro que deseas eliminar este registro?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    }

    function initFilePreview() {
        $(document).on('change', 'input[type="file"][data-preview-target]', function () {
            var targetId = $(this).data('preview-target');
            var target = document.getElementById(targetId);

            if (!target) {
                return;
            }

            target.innerHTML = '';

            if (!this.files || !this.files.length) {
                target.innerHTML = '<div class="text-muted small">Sin archivos seleccionados.</div>';
                return;
            }

            Array.prototype.forEach.call(this.files, function (file) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var item = document.createElement('div');
                    item.className = 'file-preview-item';
                    item.innerHTML = '<img src="' + e.target.result + '" alt="Vista previa"><div class="meta"><div class="fw-semibold text-truncate">' + file.name + '</div><div class="text-muted">' + Math.round(file.size / 1024) + ' KB</div></div>';
                    target.appendChild(item);
                };

                reader.readAsDataURL(file);
            });
        });
    }

    function initFeatherIcons() {
        if (window.feather) {
            window.feather.replace();
        }
    }

    function initRichText() {
        if (!window.tinymce) {
            return;
        }

        var textareas = document.querySelectorAll('textarea[data-richtext]');

        if (!textareas.length) {
            return;
        }

        window.tinymce.init({
            selector: 'textarea[data-richtext]',
            height: 320,
            menubar: false,
            branding: false,
            promotion: false,
            plugins: 'link lists table code',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link table | code',
            content_style: 'body { font-family: Lato, sans-serif; font-size: 14px; }'
        });
    }

    function initDeliveryForm() {
        if (!window.angular) {
            return;
        }

        var app;

        try {
            app = window.angular.module('deliveryAdminApp');
        } catch (e) {
            app = window.angular.module('deliveryAdminApp', []);
        }

        app.controller('DeliveryFormController', ['$scope', function ($scope) {
            var data = window.deliveryFormData || {};
            var initialItems = data.items && data.items.length ? data.items : [{ plant_id: '', quantity: 1 }];

            $scope.items = window.angular.copy(initialItems);
            $scope.plants = data.plants || [];

            $scope.addItem = function () {
                $scope.items.push({ plant_id: '', quantity: 1 });
            };

            $scope.removeItem = function (item) {
                var index = $scope.items.indexOf(item);

                if (index > -1) {
                    $scope.items.splice(index, 1);
                }

                if (!$scope.items.length) {
                    $scope.addItem();
                }
            };
        }]);
    }

    $(function () {
        initSidebarToggle();
        initDataTables();
        initDeleteConfirmation();
        initFilePreview();
        initFeatherIcons();
        initRichText();
        initDeliveryForm();
    });
})(jQuery);
