@php
    $plant = $plant ?? null;
    $existingImages = $plant && $plant->relationLoaded('images') ? $plant->images : collect();
@endphp

<div class="card-admin ui-card p-4 mt-4">
    <h5 class="mb-3">Galería</h5>

    @if($existingImages->count())
        <div class="file-preview-grid">
            @foreach($existingImages as $image)
                <div class="file-preview-item">
                    <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $image->file_name }}">
                    <div class="meta d-flex justify-content-between align-items-center gap-2">
                        <span class="text-truncate">{{ $image->file_name }}</span>
                        @if($image->is_primary)
                            <span class="badge bg-berry badge-soft ui-badge ui-badge--soft">Principal</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-muted-soft">Esta planta aún no tiene imágenes cargadas.</div>
    @endif
</div>
