@php
    $direction = $direction ?? null;
    $stocks = $direction && $direction->relationLoaded('stocks') ? $direction->stocks : collect();
@endphp

<div class="card-admin p-4 mt-4">
    <h5 class="mb-3">Stock actual por planta</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-admin mb-0">
            <thead>
                <tr>
                    <th>Planta</th>
                    <th class="text-end">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>{{ optional($stock->plant)->name }}</td>
                        <td class="text-end fw-semibold">{{ $stock->stock }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">No hay stock registrado para esta dirección.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
