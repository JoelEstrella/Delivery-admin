@extends('layouts.admin')

@section('page-title', 'Entregas')
@section('ngApp', 'deliveries')
@section('ngController', 'deliveries')

@section('content')
<div ng-cloak>
@php
    $delivery = $delivery ?? null;
    $items = $delivery && $delivery->relationLoaded('items') ? $delivery->items : collect();
    $validation = $delivery ? $delivery->validation : null;
@endphp
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

<div class="card-admin ui-card p-4 mb-4" ng-controller="DeliveryFormController as vm" ng-init="vm.init()" ng-cloak>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2 mb-3">
        <div>
            <h5 class="mb-1">Detalle de entrega</h5>
            <div class="text-muted-soft small">Agrega una o más plantas a la entrega.</div>
        </div>
        <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" ng-click="vm.addItem()">Agregar planta</button>
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
        <table class="table table-hover align-middle ui-table table-admin">
            <thead>
                <tr>
                    <th>Planta</th>
                    <th class="table-col-quantity">Cantidad</th>
                    <th class="table-col-actions"></th>
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
                        <button type="button" class="ui-btn ui-btn--negative ui-btn--sm" ng-click="vm.removeItem(item)">Quitar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card-admin ui-card p-4 mt-4">
    <h5 class="mb-3">Plantas entregadas</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle ui-table table-admin mb-0">
            <thead>
                <tr>
                    <th>Planta</th>
                    <th class="text-end">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ optional($item->plant)->name }}</td>
                        <td class="text-end fw-semibold">{{ $item->quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted-soft py-4">Esta entrega no tiene plantas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($validation)
    <div class="card-admin ui-card p-4 mt-4">
        <h5 class="mb-3">Validación</h5>
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <div class="border rounded-3 p-3 bg-white surface-alt">
                    <div class="text-muted-soft small text-uppercase">Recibido</div>
                    <div class="fw-semibold">{{ $validation->received_quantity }}</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="border rounded-3 p-3 bg-white surface-alt">
                    <div class="text-muted-soft small text-uppercase">Estado</div>
                    <div class="fw-semibold">{{ $validation->status }}</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="border rounded-3 p-3 bg-white surface-alt">
                    <div class="text-muted-soft small text-uppercase">Validado por</div>
                    <div class="fw-semibold">{{ optional($validation->validator)->name ?? '—' }}</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="border rounded-3 p-3 bg-white surface-alt">
                    <div class="text-muted-soft small text-uppercase">Fecha</div>
                    <div class="fw-semibold">{{ $validation->validated_at ? $validation->validated_at->format('d/m/Y H:i') : '—' }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="border rounded-3 p-3 bg-white surface-alt">
                    <div class="text-muted-soft small text-uppercase">Observaciones</div>
                    <div class="fw-semibold">{{ $validation->observations ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>
@endif


</div>
@endsection

@push('scripts')
<script src="{{ asset('js/deliveries.js') }}"></script>
@endpush