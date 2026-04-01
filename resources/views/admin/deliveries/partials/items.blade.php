@php
    $plants = $plants ?? collect();
    $deliveryItems = old('items', $deliveryItems ?? []);

    if (!is_array($deliveryItems) || empty($deliveryItems)) {
        $deliveryItems = [
            ['plant_id' => '', 'quantity' => 1],
        ];
    }

    $plantList = [];

    foreach ($plants as $plant) {
        $plantList[] = [
            'id' => $plant->id,
            'name' => $plant->name,
        ];
    }

    $deliveryItemsJson = addslashes(json_encode(array_values($deliveryItems)));
    $plantListJson = addslashes(json_encode($plantList));
@endphp

<div class="card-admin p-4 mb-4" ng-app="deliveryAdminApp" ng-controller="DeliveryFormController as vm" ng-init="vm.init()" ng-cloak>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2 mb-3">
        <div>
            <h5 class="mb-1">Detalle de entrega</h5>
            <div class="text-muted small">Agrega una o más plantas a la entrega.</div>
        </div>
        <button type="button" class="btn btn-outline-berry btn-sm" ng-click="vm.addItem()">Agregar planta</button>
    </div>

    @push('scripts')
        <script>
            window.deliveryFormData = {
                items: JSON.parse('{{ $deliveryItemsJson }}'),
                plants: JSON.parse('{{ $plantListJson }}')
            };
        </script>
    @endpush

    <div class="table-responsive">
        <table class="table table-hover align-middle table-admin">
            <thead>
                <tr>
                    <th>Planta</th>
                    <th style="width: 160px;">Cantidad</th>
                    <th style="width: 90px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="item in vm.items">
                    <td>
                        <select class="form-select" ng-model="item.plant_id" name="plant_ids[]" required>
                            <option value="">Seleccione...</option>
                            <option ng-repeat="plant in vm.plants" ng-value="plant.id" ng-bind="plant.name"></option>
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" class="form-control" ng-model="item.quantity" name="quantities[]" required>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm" ng-click="vm.removeItem(item)">Quitar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
