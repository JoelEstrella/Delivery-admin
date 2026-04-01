@php
    $delivery = $delivery ?? null;
    $items = $delivery && $delivery->relationLoaded('items') ? $delivery->items : collect();
    $validation = $delivery ? $delivery->validation : null;
@endphp

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
